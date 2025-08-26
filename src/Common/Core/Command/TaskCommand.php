<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TaskCommand extends ContainerAwareCommand
{
    private $output;
    private $conn;
    private const STATUS_PROCESSING = 'processing';
    private const STATUS_PENDING    = 'pending';

    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:core:task')
            ->setDescription('Runs a task related to storage operations')
            ->setHelp('');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  The input object.
     * @param OutputInterface $output The output object.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config       = $this->getConfig();
        $limit        = $config['tasks']['concurrent'] ?? 1;
        $this->output = $output;
        $this->conn   = $this->getContainer()->get('orm.manager')->getConnection('manager');

        $this->executeTasksPending($limit);

        return 0;
    }

    /**
     * Get manager config
     */
    public function getConfig()
    {
        return $this->getContainer()->get('orm.manager')
            ->getDataSet('Settings', 'manager')
            ->get('storage_settings', []);
    }

    /**
     * Executes tasks that are pending in the database.
     * This method retrieves tasks with a status of 'pending'
     * and processes them by updating their status to 'done'.
     */
    protected function executeTasksPending($limit = 1)
    {
        $sqlCount = sprintf(
            'SELECT COUNT(*) as cnt FROM tasks WHERE status = "%s"',
            self::STATUS_PROCESSING
        );

        $processingCount = (int) $this->conn->fetchColumn($sqlCount);

        $availableSlots = max(0, $limit - $processingCount);

        $tasks = [];
        if ($availableSlots > 0) {
            $sql   = sprintf(
                'SELECT * FROM tasks WHERE status = "%s" ORDER BY id ASC LIMIT %d',
                self::STATUS_PENDING,
                $availableSlots
            );
            $tasks = $this->conn->fetchAll($sql);
        }

        foreach ($tasks as $task) {
            $command  = $task['command'] . ' --task=%s';
            $params   = unserialize($task['params'] ?? '[]');
            $params[] = $task['id'];
            $process  = new Process(vsprintf($command, $params));
            $process->start();
            $pid = $process->getPid();
            $this->conn->update('tasks', [
                'status' => self::STATUS_PROCESSING,
                'pid'    => $pid
            ], ['id' => $task['id']]);
            $this->output->writeln(sprintf(
                str_pad('<options=bold>Executing task #' . $task['id'], 64, '.')
                    . '<fg=green;options=bold>DONE</>'
                    . ' <fg=blue;options=bold>(%s)</></>',
                $task['name']
            ));
        }
    }
}
