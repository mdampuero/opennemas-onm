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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;

class MigrateInstanceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('migration:instance:migrate')
            ->setDescription('Migrates an instance from an external CMS to opennemas')
            ->addArgument(
                'config-file',
                InputArgument::REQUIRED,
                'The migration configuration file.'
            )
            ->addOption(
                'checkTranslations',
                false,
                InputOption::VALUE_NONE,
                'If set, command will check and configure the translations table'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<fg=green;options=bold>Starting migration...</>");

        $start = new \DateTime();
        $path  = $input->getArgument('config-file');

        $this->output    = $output;
        $this->migration = $this->getMigration($path);

        // Load instance and force ORM and Cache initialization
        $this->getContainer()->get('core.loader')
            ->loadInstanceFromInternalName($this->migration['target']['instance']);

        $this->getConfiguration();

        $this->mm = $this->getContainer()->get('migration.manager');
        $this->mm->configure($this->migration);

        $this->mm->getMigrationTracker()->load();

        $this->migrated = count($this->mm->getMigrationTracker()->getParsed());
        $this->current  = 0;
        $this->left     = $this->mm->getRepository()->count();
        $this->total    = $this->mm->getRepository()->countAll();

        $output->writeln("<options=bold>(3/4) Migrating items...</>");
        $output->writeln("    ==> Total items in source: $this->total");
        $output->writeln("    ==> Items ready to migrate: $this->left");
        $output->writeln("    ==> Items already migrated: $this->migrated");

        while(($item = $this->mm->getRepository()->next()) !== false) {
            $this->current++;

            if ($output->isVerbose()) {
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
            $this->mm->getMigrationTracker()->add($sourceId, $targetId, $slug);

            if ($output->isVeryVerbose()) {
                $output->writeln("      <fg=green>==></> Translation added");
            }
        }

        $end  = new \DateTime();

        $this->getReport($start, $end);

        $output->writeln("<fg=green;options=bold>Migration ended</>");
    }

    /**
     * Shows configuration details.
     */
    protected function getConfiguration()
    {
        $this->output->writeln("<options=bold>(2/4) Configuring the migration...</>");

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
     * Parses the configuration file.
     *
     * @param string $path The path to configuration file.
     *
     * @return array The migration configuration.
     */
    protected function getMigration($path)
    {
        $this->output->writeln("<options=bold>(1/4) Parsing configuration file...</>");

        $yaml      = new Parser();
        $migration = $yaml->parse(file_get_contents($path));
        $migration = $migration['migration'];

        $this->output->writeln(sprintf(
            "    ==> Migrating from <fg=red>%s</> to <fg=green>%s</>",
            $migration['source']['url'],
            $migration['target']['instance']
        ));

        return $migration;
    }

    /**
     * Returns the migration final report.
     *
     * @param DateTime $start The migration starttime.
     * @param DateTime $end   The migration endtime.
     */
    protected function getReport($start, $end)
    {
        $diff = date_diff($end, $start);
        $time = $secs = $diff->format('%ss');

        if ($secs > 60) {
            $time = $diff->format('%mm %ss');
        }

        if ($secs > 60) {
            $time = $diff->format('%hh %mm %ss');
        }

        $this->output->writeln("<options=bold>(4/4) Ending migration...</>");
        $this->output->writeln("    ==> Items migrated: $this->current");
        $this->output->writeln("    ==> Time: $time");
    }
}
