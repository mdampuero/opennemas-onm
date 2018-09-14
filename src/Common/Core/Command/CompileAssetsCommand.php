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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CompileAssetsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:assets:compile')
            ->setDescription('Finds and compiles assets for backend');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themes = $this->getContainer()->getParameter('core.paths.themes');
        $paths  = [ $themes . '/admin', $themes . '/manager' ];
        $step   = 1;
        $steps  = 1 + count($paths);

        $output->writeln("<options=bold>($step/$steps) Configuring services...</>");
        $step++;

        $this->bag = getService('core.service.assetic.asset_bag');

        foreach ($paths as $path) {
            $this->bag->reset();

            $theme = substr($path, strrpos($path, '/') + 1);

            $output->writeln("<options=bold>($step/$steps) Compiling $theme theme...</>");
            $step++;

            if ($output->isVerbose()) {
                $output->writeln("  <options=bold>==></> <fg=yellow>Extracting</> paths from <fg=blue>$path</>");
            }

            $this->find($path);

            if ($output->isVerbose()) {
                $output->writeln("  <options=bold>==></> <fg=yellow>Compiling</> scripts...</>");
            }

            $this->writeScripts($output);

            if ($output->isVerbose()) {
                $output->writeln("  <options=bold>==></> <fg=yellow>Compiling</> stylesheets...</>");
            }

            $this->writeStyles($output);
        }

        $output->writeln("<options=bold>DONE</>");
    }

    /**
     * Extracts all paths to script files from file content.
     *
     * @param string $file The file content.
     *
     * @return void
     */
    protected function extractScripts($file)
    {
        $pattern = '/\{javascripts src="(?<src>[^"]*)"(\s+filters="(?<filters>['
            . '^"]*)")*(\s+output="(?<output>[^"]*)")*\}/';

        if (!preg_match_all($pattern, $file, $matches)) {
            return;
        }

        foreach (array_keys($matches['src']) as $key) {
            $srcs    = explode(',', preg_replace('/\s*|\n/', '', $matches['src'][$key]));
            $filters = explode(',', preg_replace('/\s*|\n/', '', $matches['filters'][$key]));
            $output  = empty($matches['output'][$key]) ? 'default' : $matches['output'][$key];

            foreach ($srcs as $src) {
                $this->bag->addScript($src, $filters, $output);
            }
        }
    }

    /**
     * Extracts paths to stylesheet files from file content.
     *
     * @param string $file The file content.
     *
     * @return void
     */
    protected function extractStyles($file)
    {
        $pattern = '/\{stylesheets src="(?<src>[^"]*)"(\s+filters="(?<filters>['
            . '^"]*)")*(\s+output="(?<output>[^"]*)")*\}/';


        if (!preg_match_all($pattern, $file, $matches)) {
            return;
        }

        foreach (array_keys($matches['src']) as $key) {
            $srcs    = explode(',', preg_replace('/\s*|\n/', '', $matches['src'][$key]));
            $filters = explode(',', preg_replace('/\s*|\n/', '', $matches['filters'][$key]));
            $output  = empty($matches['output'][$key]) ? 'default' : $matches['output'][$key];

            foreach ($srcs as $src) {
                $this->bag->addStyle($src, $filters, $output);
            }
        }
    }

    /**
     * Finds templates in path and extracts scripts and stylesheets.
     *
     * @param string $path The path to admin theme.
     *
     * @return void
     */
    protected function find($path)
    {
        $finder = new Finder();

        $finder->files()->in($path)->name('*.tpl');

        foreach ($finder as $file) {
            $content = $file->getContents();

            $this->extractScripts($content);
            $this->extractStyles($content);
        }
    }

    /**
     * Writes compiled files for scripts included in templates.
     *
     * @param OutputInterface $output The output object.
     *
     * @return void
     */
    protected function writeScripts($output)
    {
        $am       = $this->getContainer()->get('core.service.assetic.javascript_manager');
        $scripts  = $this->bag->getScripts();
        $progress = null;

        if ($output->isVeryVerbose()) {
            $output->writeln("      - {$this->count($scripts)} scripts to compile");
        }

        foreach ($scripts as $bag => $files) {
            $am->writeAssets($files, $this->bag->getFilters(), $bag);
        }
    }

    /**
     * Writes compiled files for stylesheets included in templates.
     *
     * @param OutputInterface $output The output object.
     *
     * @return void
     */
    protected function writeStyles($output)
    {
        $am       = $this->getContainer()->get('core.service.assetic.stylesheet_manager');
        $styles   = $this->bag->getStyles();
        $progress = null;

        if ($output->isVeryVerbose()) {
            $output->writeln("      - {$this->count($styles)} stylesheets to compile");
        }

        foreach ($styles as $bag => $files) {
            $am->writeAssets($files, $this->bag->getFilters(), $bag);
        }
    }

    /**
     * Counts all files in bag.
     *
     * @param array $bag The list of file bags.
     *
     * @return integer The amount of files in bag.
     */
    protected function count($bag)
    {
        $total = 0;

        foreach ($bag as $files) {
            $total += count($files);
        }

        return $total;
    }
}
