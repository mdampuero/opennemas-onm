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

        $progress = null;

        $this->start     = new \DateTime();
        $this->path      = $input->getArgument('config-file');
        $this->input     = $input;
        $this->output    = $output;
        $this->migration = $this->getMigration();

        $this->getConfiguration();

        $this->mm = $this->getContainer()->get('migration.manager');
        $this->mm->configure($this->migration);

        $this->preMigrate();

        $this->mm->getTracker()->load();

        $this->getCounters();

        if (!$output->isVeryVerbose()) {
            $progress = new ProgressBar($output, $this->left);
        }

        while (($item = $this->mm->getRepository()->next()) !== false) {
            $this->current++;

            if ($output->isVeryVerbose()) {
                $output->writeln(
                    "    ==> Processing item <fg=red;options=bold>"
                    . "{$this->current}</> of <fg=green;options=bold>$this->left"
                    . "</></>"
                );
            }

            // Apply filters
            $item = $this->mm->filter($item);

            if ($output->isVeryVerbose()) {
                $output->writeln("      <fg=red>==></> Item parsed");
            }

            // Save item
            $sourceId = $item[$this->migration['source']['mapping']['id']];
            $targetId = $this->mm->persist($item);
            $slug     = $item[$this->migration['target']['slug']];

            if ($output->isVeryVerbose()) {
                $output->writeln("      <fg=yellow>==></> Item saved");
            }

            // Add to translations
            if (!empty($targetId)) {
                $this->mm->getTracker()->add($sourceId, $targetId, $slug);
            }

            if ($output->isVeryVerbose()) {
                $output->writeln("      <fg=green>==></> Translation added");
            }

            if (!empty($progress)) {
                $progress->advance();
            }
        }

        if (!empty($progress)) {
            $progress->finish();
        }

        $this->postMigrate();

        $this->getReport();

        $output->writeln("<fg=green;options=bold>Migration ended</>");
    }

    /**
     * Shows configuration details.
     */
    protected function getConfiguration()
    {
        $this->getContainer()->get('session')
            ->set('user', json_decode(json_encode(['id' => 0, 'username' => 'cli'])));

        // Load instance and force ORM and Cache initialization
        $loader = $this->getContainer()->get('core.loader');
        $loader->loadInstanceFromInternalName(
            $this->migration['target']['instance']
        );

        $loader->init();

        $this->output->writeln("<options=bold>(2/6) Configuring the migration...</>");

        $this->output->writeln(sprintf(
            "    ==> Tracking <fg=magenta>%s</>",
            $this->migration['type']
        ));

        $this->output->writeln(sprintf(
            "    ==> Reading from <fg=red>%s (%s)</>",
            $this->migration['source']['repository'],
            $this->migration['source']['repository'] === 'database' ?
                $this->migration['source']['database'] :
                $this->migration['source']['path']
        ));

        $this->output->writeln(sprintf(
            "    ==> Saving as <fg=magenta>%s</> to <fg=green>%s</>",
            $this->migration['target']['persister'],
            $this->migration['target']['database']
        ));
    }

    /**
     * Displays the number of contents that are in the source, were migrated and
     * are ready to migrate.
     */
    protected function getCounters()
    {
        $this->output->writeln("<options=bold>(4/6) Migrating items...</>");

        $this->total = $this->mm->getRepository()->countAll();
        $this->output->writeln("    ==> Total items in source: $this->total");

        $this->left = $this->mm->getRepository()->count();
        $this->output->writeln("    ==> Items ready to migrate: $this->left");

        $this->migrated = $this->mm->getRepository()->countMigrated();
        $this->output->writeln("    ==> Items already migrated: $this->migrated");

        $this->current  = 0;
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
        $this->end = new \DateTime();

        $diff = date_diff($this->end, $this->start);
        $time = $secs = $diff->format('%ss');

        if ($secs > 60) {
            $time = $diff->format('%mm %ss');
        }

        if ($secs > 3600) {
            $time = $diff->format('%hh %mm %ss');
        }

        $this->output->writeln("<options=bold>(6/6) Ending migration...</>");
        $this->output->writeln("    ==> Items migrated: $this->current");
        $this->output->writeln("    ==> Time: $time");
    }

    /**
     * Executes actions before migrating items.
     */
    protected function preMigrate()
    {
        $this->output->writeln("<options=bold>(3/6) Executing pre-migration actions...</>");

        if (!empty($this->input->getOption('no-pre'))
            || !array_key_exists('pre', $this->migration['source'])
            || empty($this->migration['source']['pre'])
        ) {
            $this->output->writeln("    ==> No actions executed");
            return;
        }

        $this->mm->getRepository()->prepare($this->migration['source']['pre']);
    }

    /**
     * Executes actions after migrating items.
     */
    protected function postMigrate()
    {
        $this->output->writeln("<options=bold>(5/6) Executing post-migration actions...</>");

        if (!empty($this->input->getOption('no-post'))
            || !array_key_exists('post', $this->migration['target'])
            || empty($this->migration['target']['post'])
        ) {
            $this->output->writeln("    ==> No actions executed");
            return;
        }

        $this->mm->getPersister()->prepare($this->migration['target']['post']);
    }
}
