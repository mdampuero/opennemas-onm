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

class FixCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('migration:fix')
            ->setDescription('Fixes data stored in an opennemas database')
            ->addArgument(
                'fix-file',
                InputArgument::REQUIRED,
                'The fix configuration file.'
            )->addOption(
                'no-end',
                false,
                InputOption::VALUE_NONE,
                'If set, command will not delete the table of fixed items when finishing'
            )->addOption(
                'reset',
                false,
                InputOption::VALUE_NONE,
                'If set, command will delete the table of fixed items when starting'
            );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<fg=green;options=bold>Starting fix...</>");

        $end      = $input->getOption('no-end');
        $reset    = $input->getOption('reset');
        $progress = null;

        $this->start  = time();
        $this->path   = $input->getArgument('fix-file');
        $this->input  = $input;
        $this->output = $output;
        $this->fix    = $this->getFix();

        $this->getConfiguration();

        $this->mm = $this->getContainer()->get('migration.manager');
        $this->mm->configure($this->fix);

        $tracker = $this->mm->getTracker();

        if ($reset) {
            $tracker->end();
        }

        $tracker->start();

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
            $this->mm->getPersister()->persist($item);

            // Add to translations
            $tracker->add($item[$this->fix['tracker']['fields'][0]]);

            if (!empty($progress)) {
                $progress->advance();
            }
        }

        if (!$end) {
            $tracker->end();
        }

        if (!empty($progress)) {
            $progress->finish();
            $output->writeln('');
        }

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
            $this->fix['source']['instance']
        );

        $loader->init();

        $this->output->writeln("<options=bold>(2/4) Configuring the fix...</>");

        $this->output->writeln(sprintf(
            "    ==> Fixing <fg=magenta>%s</>",
            $this->fix['type']
        ));
    }

    /**
     * Displays the number of contents that are in the source, were migrated and
     * are ready to migrate.
     */
    protected function getCounters()
    {
        $this->output->writeln("<options=bold>(3/4) Migrating items...</>");

        $this->total = $this->mm->getRepository()->countAll();
        $this->output->writeln("    ==> Total items in source: $this->total");

        $this->left = $this->mm->getRepository()->count();
        $this->output->writeln("    ==> Items ready to migrate: $this->left");

        $this->fixed = $this->mm->getRepository()->countFixed();
        $this->output->writeln("    ==> Items already migrated: $this->fixed");

        $this->current = 0;
    }

    /**
     * Parses the configuration file.
     *
     * @return array The fix configuration.
     */
    protected function getFix()
    {
        $this->output->writeln("<options=bold>(1/4) Parsing fix file...</>");

        $yaml = new Parser();
        $fix  = $yaml->parse(file_get_contents($this->path));
        $fix  = $fix['fix'];

        $this->output->writeln(sprintf(
            "    ==> Fixing data in <fg=red>%s</>.<fg=green>%s</>",
            $fix['source']['database'],
            $fix['source']['table']
        ));

        return $fix;
    }

    /**
     * Displays the migration final report.
     */
    protected function getReport()
    {
        $this->end = time();

        $diff = $this->end - $this->start;
        $time = date('H:i:s', $diff);

        $this->output->writeln("<options=bold>(4/4) Ending migration...</>");
        $this->output->writeln("    ==> Items migrated: $this->current");
        $this->output->writeln("    ==> Time: $time");
    }
}
