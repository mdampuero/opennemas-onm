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
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                ''
            )
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
        $this->output = $output;
        $limit        = $input->getOption('limit');
        $this->conn   = $this->getContainer()->get('orm.manager')->getConnection('manager');

        $this->logCommandStart();
        $this->executeTasksPending();
        $this->logCommandFinish();

        return 0;
    }

    /**
     * Logs the start of the command execution with a timestamp.
     */
    private function logCommandStart()
    {
        $this->output->writeln(sprintf(
            str_pad('<options=bold>Starting command', 32, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));
    }

    /**
     * Logs the completion of the command execution with a timestamp.
     */
    private function logCommandFinish()
    {
        $this->output->writeln(sprintf(
            str_pad('<options=bold>Finish command', 32, '.')
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</></>',
            date('Y-m-d H:i:s')
        ));
    }

    /**
     * Executes tasks that are pending in the database.
     * This method retrieves tasks with a status of 'pending'
     * and processes them by updating their status to 'done'.
     */
    protected function executeTasksPending($limit = 1)
    {
        $sql   = sprintf(
            'SELECT * FROM tasks WHERE status = "%s" AND NOT EXISTS (
                SELECT 1 FROM tasks WHERE status = "%s"
            ) ORDER BY id ASC LIMIT %d',
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            $limit
        );
        $tasks = $this->conn->fetchAll($sql);

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
                str_pad('<options=bold>Executing task #' . $task['id'], 128, '.')
                    . '<fg=green;options=bold>DONE</>'
                    . ' <fg=blue;options=bold>(%s)</></>',
                $task['name']
            ));
        }
    }
}
