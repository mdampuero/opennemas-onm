<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Command;

use Common\Core\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The ExtractCommand class defines a command to extract contents from NewsML
 * files and generates a PHP file to save the information as a serialized
 * string.
 */
class ExtractCommand extends Command
{
    /**
     * The timestamp when the command was ended.
     *
     * @var integer
     */
    protected $ended;

    /**
     * The timestamp when the command was started.
     *
     * @var integer
     */
    protected $started;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('news-agency:extract')
            ->setDescription('Extracts contents from NewsML files in a directory')
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'The instance internal name'
            )->addOption(
                'agency',
                'a',
                InputOption::VALUE_REQUIRED,
                'The news-agency server id to configure the command with'
            )->addOption(
                'source',
                's',
                InputOption::VALUE_REQUIRED,
                'The path to the directory where NewsML are stored'
            )->addOption(
                'target',
                't',
                InputOption::VALUE_REQUIRED,
                'The path to write the extracted contents to'
            );
    }

    /**
     * codeCoverageIgnore
     *
     * Checks if the provided source is valid.
     *
     * @param string $source The path to source.
     *
     * @throws \InvalidArgumentException If the source is not valid.
     */
    protected function checkSource(string $source) : void
    {
        $fs = new Filesystem();

        if (empty($source) || !$fs->exists($source)) {
            throw new \InvalidArgumentException('No such file or directory ' . $source);
        }
    }

    /**
     * codeCoverageIgnore
     *
     * Checks if the provided target is valid.
     *
     * @param string $source The path to target.
     *
     * @throws \InvalidArgumentException If the target is not valid.
     */
    protected function checkTarget(string $target) : void
    {
        $fs = new Filesystem();

        if (empty($target) || $fs->exists($target) || is_dir($target)) {
            throw new \InvalidArgumentException('Unable to create file ' . $target);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->start();
        $output->writeln(sprintf(
            '(1/5) Starting command...<options=bold></><fg=green;options=bold>DONE</> <fg=blue;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $instance = $input->getArgument('instance');
        $agency   = $input->getOption('agency');
        $source   = $input->getOption('source');
        $target   = $input->getOption('target');

        $this->getContainer()->get('core.loader')->load($instance);

        if (!empty($agency)) {
            $source = sprintf(
                '%s/../tmp/instances/%s/importers/%s',
                $this->getContainer()->getParameter('kernel.root_dir'),
                $instance,
                $agency
            );

            $target = $source;
        }

        $this->checkSource($source);
        $this->checkTarget($target);

        $output->write('(2/5) Reading files......');
        $files = $this->getFiles($source);
        $output->writeln(sprintf(
            '<fg=green;options=bold>DONE</>'
            . ' <fg=blue;options=bold>(%s found)</>',
            count($files)
        ));


        $output->writeln('(3/5) Processing files...<fg=yellow;options=bold>STARTED</>');
        $progress = new ProgressBar($output, count($files));
        $factory  = $this->getContainer()->get('news_agency.factory.parser');
        $contents = [];

        foreach ($files as $file) {
            try {
                $xml = simplexml_load_string($file->getContents());

                $parser   = $factory->get($xml);
                $contents = array_merge($contents, $parser->parse($xml));
            } catch (\Exception $e) {
            }

            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');

        $output->write('(5/5) Writing contents......');
        file_put_contents($target, serialize($contents));
        $output->writeln(sprintf(
            '<fg=green;options=bold>DONE</>'
            . ' <fg=blue;options=bold>(%s found)</>',
            count($contents)
        ));

        $this->end();
        $output->writeln(sprintf(
            '(5/5) Ending command.....<options=bold></>'
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }

    /**
     * @codeCoverageIgnore
     *
     * Returns a list of files to process.
     *
     * @param string $path The path to a file or a directory.
     *
     * @return array The list of files.
     */
    protected function getFiles($path)
    {
        $finder = new Finder();

        if (empty($path) || !file_exists($path)) {
            throw new \InvalidArgumentException(
                'No such file or directory: ' . $path
            );
        }

        if (is_file($path)) {
            return [ new SplFileInfo($path, '', basename($path)) ];
        }

        return $finder->in($path)->name('*.xml')->files();
    }
}
