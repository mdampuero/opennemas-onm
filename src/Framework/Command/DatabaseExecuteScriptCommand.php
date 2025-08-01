<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DatabaseExecuteScriptCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('database:execute-script')
            ->setDescription('Handles the current database schema.')
            ->addArgument(
                'script',
                InputArgument::REQUIRED,
                'The SQL script to execute.'
            )
            ->addOption(
                'instance',
                'i',
                InputOption::VALUE_REQUIRED,
                'The instance name where execute the script'
            )
            ->addOption(
                'database',
                'd',
                InputOption::VALUE_REQUIRED,
                'The database name where execute the script'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('core.loader');

        $database = $input->getOption('database');
        $instance = $input->getOption('instance');
        $script   = $input->getArgument('script');

        $this->output  = $output;
        $this->updated = 0;

        $this->getConfiguration();

        if (!empty($database)) {
            $this->executeScript($database, $script);
            $output->writeln("Update finished: $this->updated instances updated");
            return;
        }

        $oql = '';

        if (!empty($instance)) {
            $oql = sprintf('internal_name = "%s"', $instance);
        }

        $instances = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);

        $output->writeln("Instances to update: " . count($instances));

        foreach ($instances as $instance) {
            $this->executeScript($instance->getDatabaseName(), $script);
        }

        $output->writeln("Update finished: $this->updated instances updated");
    }

    /**
     * Executes the script in the given database.
     *
     * @param  string $database The database name.
     * @param  string $script   The path to the script file.
     */
    protected function executeScript($database, $script)
    {
        $output = $this->output;

        $output->writeln("Updating database $database...");

        $cmd = 'mysql'
            . ' -u' . $this->user
            . ' -p' . $this->password
            . ' -h' . $this->host
            . ' -P' . $this->port
            . ' ' . $database . ' < ' . $script;

        $process = new Process($cmd);
        $process->setTimeout(6000);
        $process->setIdleTimeout(6000);

        $error = $process->run(
            function ($type, $buffer) use ($database, $output) {
                if (Process::ERR === $type) {
                    $output->writeln(
                        "<error>Error updating database $database: "
                        . $buffer . "</error>"
                    );
                } else {
                    $output->writeln($buffer);
                }
            }
        );

        if (!$error) {
            $this->updated++;
            $output->writeln("Database $database updated successfully");
        }
    }

    /**
     * Get the current configuration from parameters.
     */
    protected function getConfiguration()
    {
        $config     = $this->getContainer()->getParameter('database');
        $connection = $config['dbal']['default_connection'];

        $this->user     = $config['dbal']['connections'][$connection]['user'];
        $this->password = $config['dbal']['connections'][$connection]['password'];
        $this->host     = $config['dbal']['connections'][$connection]['host'];
        $this->port     = $config['dbal']['connections'][$connection]['port'];
    }
}
