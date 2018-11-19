<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The CheckSchemaCommand class defines a command to check the current database
 * schema against the ORM configuration.
 */
class CheckSchemaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('orm:schema:check')
            ->setDescription('Checks a database schema basing on ORM configuration')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'The database name.'
            )
            ->addOption(
                'connection',
                null,
                InputOption::VALUE_REQUIRED,
                'The database connection to use',
                'manager'
            )
            ->addOption(
                'schema',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of the schema',
                'manager'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = $input->getOption('connection');
        $database   = $input->getArgument('database');
        $schema     = $input->getOption('schema');

        $em = $this->getContainer()->get('orm.manager');

        $dumper = $em->getDumper($schema);
        $conn   = $em->getConnection($connection);

        $conn->selectDatabase($database);

        $default = $dumper->dump($schema);
        $current = $dumper->discover($conn, $database);

        $sql = $current->getMigrateToSql($default, $conn->getDatabasePlatform());

        if (count($sql) > 0) {
            $output->writeln(str_replace("\n", '', \SqlFormatter::highlight("USE `$database`;")));

            foreach ($sql as $value) {
                $output->writeln(str_replace("\n", '', \SqlFormatter::highlight("$value;")));
            }
        }
    }
}
