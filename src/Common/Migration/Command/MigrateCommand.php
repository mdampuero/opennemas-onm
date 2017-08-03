<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class MigrateCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('migration:migrate')
            ->setDescription('Migrates data from an external data source to opennemas')
            ->addArgument(
                'config-file',
                InputArgument::REQUIRED,
                'The migration configuration file.'
            )->addOption(
                'no-pre',
                false,
                InputOption::VALUE_NONE,
                'If set, command will NOT execute the pre-migration actions'
            )->addOption(
                'no-post',
                false,
                InputOption::VALUE_NONE,
                'If set, command will NOT execute the post-migration actions'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<fg=green;options=bold>Starting migration...</>");

        $this->start     = time();
        $this->path      = $input->getArgument('config-file');
        $this->input     = $input;
        $this->output    = $output;
        $this->migration = $this->getMigration();

        $this->getConfiguration();

        $this->conn = $this->getContainer()->get('orm.manager')
            ->getConnection('manager');

        $this->preMigrate();
        $this->postMigrate();

        $this->getReport();

        $output->writeln("<fg=green;options=bold>Migration ended</>");
    }

    /**
     * Shows configuration details.
     */
    protected function getConfiguration()
    {
        $this->getContainer()->get('session')->set(
            'user',
            json_decode(json_encode(['id' => 0, 'username' => 'cli']))
        );

        // Load instance and force ORM and Cache initialization
        $loader = $this->getContainer()->get('core.loader');
        $loader->loadInstanceFromInternalName(
            $this->migration['target']['instance']
        );

        $loader->init();

        $this->output->writeln("<options=bold>(2/6) Configuring the migration...</>");

        $this->output->writeln(sprintf(
            "    ==> Migrating <fg=magenta>%s</>",
            $this->migration['type']
        ));

        $this->output->writeln(sprintf(
            "    ==> Reading from <fg=red>%s (%s)</>",
            $this->migration['source']['repository'],
            $this->migration['source']['repository'] === 'database' ?
                $this->migration['source']['database'] :
                $this->migration['source']['path']
        ));
    }

    /**
     * Parses the configuration file.
     *
     * @return array The migration configuration.
     */
    protected function getMigration()
    {
        $this->output->writeln("<options=bold>(1/6) Parsing configuration file...</>");

        $yaml      = new Parser();
        $migration = $yaml->parse(file_get_contents($this->path));
        $migration = $migration['migration'];

        $this->output->writeln(sprintf(
            "    ==> Migrating from <fg=red>%s</> to <fg=green>%s</>",
            $migration['source']['url'],
            $migration['target']['instance']
        ));

        return $migration;
    }

    /**
     * Displays the migration final report.
     */
    protected function getReport()
    {
        date_default_timezone_set('UTC');

        $this->end = time();

        $diff = $this->end - $this->start;
        $time = date('H:i:s', $diff);

        $this->output->writeln("<options=bold>(6/6) Ending migration...</>");
        $this->output->writeln("    ==> Time: $time");
    }

    /**
     * Executes actions before migrating items.
     */
    protected function preMigrate()
    {
        $this->output->writeln("<options=bold>(3/6) Executing pre-migration actions...</>");

        if (!empty($this->input->getOption('no-pre'))
            || !array_key_exists('pre', $this->migration)
            || empty($this->migration['pre'])
        ) {
            $this->output->writeln("    ==> No actions executed");
            return;
        }

        $this->conn->selectDatabase($this->migration['source']['database']);

        foreach ($this->migration['pre'] as $q) {
            $q = $this->prepareQuery($q);

            $this->conn->executeQuery($q);
        }
    }

    /**
     * Executes actions after migrating items.
     */
    protected function postMigrate()
    {
        $this->output->writeln("<options=bold>(5/6) Executing post-migration actions...</>");

        if (!empty($this->input->getOption('no-post'))
            || !array_key_exists('post', $this->migration)
            || empty($this->migration['post'])
        ) {
            $this->output->writeln("    ==> No actions executed");
            return;
        }

        $this->conn->selectDatabase($this->migration['target']['database']);

        foreach ($this->migration['post'] as $q) {
            $q = $this->prepareQuery($q);

            $this->conn->executeQuery($q);
        }
    }

    /**
     * Replaces placeholders with real values from migration configuration.
     *
     * @return string The query to execute.
     */
    protected function prepareQuery($q)
    {
        if (!preg_match_all('/\[[a-z.]+\]/', $q, $matches)) {
            return $q;
        }

        foreach ($matches[0] as $placeholder) {
            $keys  = explode('.', preg_replace('/\[|\]/', '', $placeholder));
            $value = $this->migration;

            foreach ($keys as $key) {
                $value = $value[$key];
            }

            if (!is_numeric($value) && !is_string($value)) {
                throw new \InvalidArgumentException();
            }

            $q = preg_replace('/' . preg_quote($placeholder) . '/', $value, $q);
        }

        return $q;
    }
}
