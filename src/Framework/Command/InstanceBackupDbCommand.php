<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;

class InstanceBackupDbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption(
                        'name',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Then instance internal name'
                    ),
                    new InputOption(
                        'id',
                        null,
                        InputOption::VALUE_OPTIONAL,
                        'Then instance id'
                    ),
                    new InputOption(
                        'output_file',
                        null,
                        InputOption::VALUE_REQUIRED,
                        'The output file where to generate the database dump.'
                    ),
                )
            )
            ->setName('instance:backup-db')
            ->setDescription('Creates a database backup from an instance internal name of id.')
            ->setHelp(
                <<<EOF
The <info>instances:list</info> command creates a database backup from an instance internal name of id.

<info>php bin/console instance:backup-db</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();

        if (empty($options['name']) && empty($options['id'])) {
            throw new \InvalidArgumentException('Please specify an internal name or id.');
        }

        if (empty($outputFile)) {
            throw new \InvalidArgumentException('Please specify a path where you want to save the dump.');
        }

        $instanceName = $options['name'];
        $instanceId   = $options['id'];
        $databaseName = $this->getDatabaseName($instanceName, $instanceId);
        $outputFile   = $input->getOption('output_file');


        $dumpCommand = $this->getMysqlDumpCommand($databaseName, $outputFile);

        $output->writeln('Executing command: `'.$dumpCommand.'`');
        $this->executeDatabaseDump($dumpCommand, $output);

        $output->writeln('Dump for instance generated to file: '.$outputFile);
    }

    /**
     * Returns the database name from a given instance name or id
     *
     * @param string $instanceName
     *
     * @return string the instance database name
     * @throws InvalidArgumentException if instance not found
     **/
    public function getDatabaseName($instanceName, $instanceId)
    {
        $dbConn = $this->getContainer()->get('dbal_connection');

        if (!empty($instanceName)) {
            $sql    = 'SELECT settings FROM instances WHERE internal_name=?';
            $value = $instanceName;
        } else {
            $sql   = 'SELECT settings FROM instances WHERE id=?';
            $value = $instanceId;
        }

        try {
            $settings = $dbConn->fetchAll($sql, array($value));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        if (count($settings) < 1) {
            throw new \InvalidArgumentException('Instance not found.');
        }

        $settings = \unserialize($settings[0]['settings']);

        return $settings['BD_DATABASE'];
    }

    /**
     * Returns the mysqldump command with all its parameters
     *
     * @return void
     **/
    public function getMysqlDumpCommand($databaseName, $outputFile)
    {
        $dbConfigurations = $this->getContainer()->getParameter('database');
        $dbUser           = $dbConfigurations['dbal']['connections']['default']['user'];
        $dbPass           = $dbConfigurations['dbal']['connections']['default']['password'];

        return "mysqldump -u{$dbUser} -p{$dbPass} {$databaseName} > {$outputFile}";
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function executeDatabaseDump($dumpCommand, $output)
    {
        $process = new Process($dumpCommand);
        $process->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->write("\t<error>".$buffer. "<error>");
            } else {
                $output->write("\t".$buffer. 'DONE');
            }
        });
    }
}
