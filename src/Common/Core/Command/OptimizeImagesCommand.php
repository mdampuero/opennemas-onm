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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The ClearConfigCommand class defines a command to load the ORM configuration.
 */
class OptimizeImagesCommand extends ContainerAwareCommand
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
            ->setName('assets:image:optimize')
            ->setDescription('Optimizes an image or a list of images in a directory')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'The image or directory of images to optimize'
            )->addOption(
                'optimize',
                'o',
                InputOption::VALUE_NONE,
                'Whether to apply dpi/quality optimization'
            )->addOption(
                'resize',
                'r',
                InputOption::VALUE_REQUIRED,
                'Resizes the image to fit in a square with the provided dimensions (e.g. 1280x1280)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<options=bold>assets:image:optimize</>');

        $this->start();
        $output->writeln(sprintf(
            '(1/4) Starting command...<options=bold></><fg=green;options=bold>DONE</> <fg=blue;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->started)
        ));

        $source = $input->getArgument('source');
        $resize = $this->getResizeParameters($input->getOption('resize'));

        if ($source[0] !== '/') {
            $source = getcwd() . '/' . $source;
        }

        $output->write('(2/4) Reading files......');
        $files = $this->getFiles($source);
        $output->writeln(sprintf(
            '<fg=green;options=bold>DONE</> <fg=blue;options=bold>(%s files)</>',
            count($files)
        ));

        $output->writeln('(3/4) Processing files...<fg=yellow;options=bold>STARTED</>');
        $processor = $this->getContainer()->get('core.image.processor');
        $progress  = new ProgressBar($output, count($files));

        foreach ($files as $file) {
            $processor->open($file->getRealPath());

            if (!empty($input->getOption('optimize'))) {
                $processor->optimize();
            }

            if (!empty($resize)) {
                $processor->apply(
                    'thumbnail',
                    explode('x', $input->getOption('resize'))
                );
            }

            $processor->save($file->getRealPath());

            $progress->advance();
        }

        $this->end();
        $progress->finish();
        $output->writeln('');

        $output->writeln(sprintf(
            '(4/4) Ending command.....<options=bold></>'
                . '<fg=green;options=bold>DONE</>'
                . ' <fg=blue;options=bold>(%s)</>'
                . ' <fg=yellow;options=bold>(%s)</>',
            date('Y-m-d H:i:s', $this->ended),
            $this->getDuration()
        ));
    }

    /**
     * Updates the ended time with the current timestamp.
     */
    protected function end()
    {
        $this->ended = time();
    }

    /**
     * Returns the duration of the current command.
     *
     * @return string The duration of the current command.
     */
    protected function getDuration()
    {
        return date('H:i:s', $this->ended - $this->started);
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

        return $finder->in($path)->files();
    }

    /**
     * Returns the parameters to apply on resize action basing on a resolution
     * provided as string.
     *
     * @param string $resolution The resolution.
     *
     * @return array The list of parameters to apply on resize action.
     */
    protected function getResizeParameters($resolution)
    {
        if (empty($resolution)) {
            return null;
        }

        $params = array_map(function ($a) {
            return (int) $a;
        }, explode('x', $resolution));

        $params = array_filter($params, function ($a) {
            return $a > 0;
        });

        if (empty($params) || count($params) !== 2) {
            throw new \InvalidArgumentException(
                'Invalid resolution: ' . $resolution
            );
        }

        return $params;
    }

    /**
     * Updates the started time with the current timestamp.
     */
    protected function start()
    {
        $this->started = time();
    }
}
