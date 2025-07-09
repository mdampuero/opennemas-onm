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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class TaskCommand extends ContainerAwareCommand
{
    private $output;

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
        $this->output = $output;

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
            str_pad('<options=bold>Starting command', 128, '.')
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
            str_pad('<options=bold>Finish command', 128, '.')
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
    protected function executeTasksPending()
    {
        $sql   = sprintf(
            'SELECT * FROM tasks WHERE status = "%s" AND NOT EXISTS (
                SELECT 1 FROM tasks WHERE status = "%s"
            ) ORDER BY id ASC LIMIT 1',
            'pending',
            'processing'
        );
        $tasks = $this->getContainer()->get('orm.manager')->getConnection('manager')
            ->fetchAll($sql);

        foreach ($tasks as $task) {
            $this->output->writeln(sprintf(
                str_pad('<options=bold>Executing task #' . $task['id'], 128, '.')
                    . '<fg=green;options=bold>DONE</>'
                    . ' <fg=blue;options=bold>(%s)</></>',
                $task['name']
            ));

            $command = $task['command'];
            $params  = unserialize($task['params'] ?? '[]');

            try {
                $process = new Process(vsprintf($command, $params));
                $process->start();

                if (!$process->isSuccessful()) {
                    throw new \RuntimeException($process->getErrorOutput());
                }
                $this->getContainer()->get('orm.manager')->getConnection('manager')
                    ->update('tasks', ['status' => 'done'], ['id' => $task['id']]);
            } catch (\Exception $e) {
                $this->getContainer()->get('orm.manager')->getConnection('manager')
                    ->update('tasks', ['status' => 'error'], ['id' => $task['id']]);
            }
        }
    }
}
