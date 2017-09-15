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

        $this->conn = $this->getContainer()->get('orm.manager')
            ->getConnection('manager');

        $tracker = $this->mm->getTracker();

        if ($reset) {
            $tracker->end();
        }

        $tracker->start();

        $this->getCounters();

        if (!$output->isVeryVerbose()) {
            $progress = new ProgressBar($output, $this->left);
        }

        $this->preFix();

        $this->output->writeln("<options=bold>(5/7) Executing the fix...</>");

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

        if (!empty($progress)) {
            $progress->finish();
            $output->writeln('');
        }

        $this->postFix();

        if (!$end) {
            $tracker->end();
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

        $this->output->writeln("<options=bold>(2/7) Configuring the fix...</>");

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
        $this->output->writeln("<options=bold>(3/7) Fixing items...</>");

        $this->total = $this->mm->getRepository()->countAll();
        $this->output->writeln("    ==> Total items in source: $this->total");

        $this->left = $this->mm->getRepository()->count();
        $this->output->writeln("    ==> Items ready to fix: $this->left");

        $this->fixed = $this->mm->getRepository()->countFixed();
        $this->output->writeln("    ==> Items already fixed: $this->fixed");

        $this->current = 0;
    }

    /**
     * Parses the configuration file.
     *
     * @return array The fix configuration.
     */
    protected function getFix()
    {
        $this->output->writeln("<options=bold>(1/7) Parsing fix file...</>");

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
        date_default_timezone_set('UTC');

        $this->end = time();

        $diff = $this->end - $this->start;
        $time = date('H:i:s', $diff);

        $this->output->writeln("<options=bold>(7/7) Ending fixing...</>");
        $this->output->writeln("    ==> Items migrated: $this->current");
        $this->output->writeln("    ==> Time: $time");
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
            $value = $this->fix;

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

    /**
     * Executes actions before migrating items.
     */
    protected function preFix()
    {
        $this->output->writeln("<options=bold>(4/7) Executing pre-fixing actions...</>");

        if (!empty($this->input->getOption('no-pre'))
            || !array_key_exists('pre', $this->fix)
            || empty($this->fix['pre'])
        ) {
            $this->output->writeln("    ==> No actions executed");
            return;
        }

        $this->conn->selectDatabase($this->fix['source']['database']);

        foreach ($this->fix['pre'] as $q) {
            $q = $this->prepareQuery($q);

            $this->conn->executeQuery($q);
        }
    }

    /**
     * Executes actions after fixing items.
     */
    protected function postFix()
    {
        $this->output->writeln("<options=bold>(6/7) Executing post-fixing actions...</>");

        if (!empty($this->input->getOption('no-post'))
            || !array_key_exists('post', $this->fix)
            || empty($this->fix['post'])
        ) {
            $this->output->writeln("    ==> No actions executed");
            return;
        }

        $this->conn->selectDatabase($this->fix['source']['database']);

        foreach ($this->fix['post'] as $q) {
            $q = $this->prepareQuery($q);

            $this->conn->executeQuery($q);
        }
    }
}
