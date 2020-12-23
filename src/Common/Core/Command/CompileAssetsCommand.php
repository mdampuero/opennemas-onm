<?php

namespace Common\Core\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class CompileAssetsCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:assets:compile')
            ->setDescription('Finds and compiles assets for backend');

        $this->steps[] = 3;
        $this->steps[] = 2;
        $this->steps[] = 3;
    }

    /**
     * Executes the command.
     */
    protected function do()
    {
        $this->writeStep('Compiling themes');

        if ($this->output->isVerbose()) {
            $this->writeStatus('warning', 'IN PROGRESS', true);
        }

        $themes = $this->getContainer()->getParameter('core.paths.public') . '/core/themes';
        $paths  = [ $themes . '/admin', $themes . '/manager' ];

        $this->bag = $this->getContainer()->get('core.service.assetic.asset_bag');

        foreach ($paths as $path) {
            $this->bag->reset();

            $theme = substr($path, strrpos($path, '/') + 1);


            if ($this->output->isVerbose()) {
                $this->writeStep("Compiling $theme theme", true, 2);
                $this->writeStep('Extracting assets', false, 3);
            }

            $this->find($path);

            if ($this->output->isVerbose()) {
                $this->writeStatus('success', 'DONE');
                $this->writeStatus('info', sprintf(
                    ' (%s scripts, %s stylesheets)',
                    $this->count($this->bag->getScripts()),
                    $this->count($this->bag->getStyles())
                ), true);
            }


            if ($this->output->isVerbose()) {
                $this->writeStep('Compiling scripts', false, 3);
            }

            $this->writeScripts();

            if ($this->output->isVerbose()) {
                $this->writeStatus('success', 'DONE', true);
                $this->writeStep('Compiling stylesheets', false, 3);
            }

            $this->writeStyles();

            if ($this->output->isVerbose()) {
                $this->writeStatus('success', 'DONE', true);
            }

            // Reset steps for level 3 for the next theme
            $this->step[2] = 1;
        }

        if (!$this->output->isVerbose()) {
            $this->writeStatus('success', 'DONE', true);
        }
    }

    /**
     * Extracts all paths to script files from file content.
     *
     * @param string $file The file content.
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
     */
    protected function writeScripts()
    {
        $am      = $this->getContainer()->get('core.service.assetic.javascript_manager');
        $scripts = $this->bag->getScripts();

        foreach ($scripts as $bag => $files) {
            $am->writeAssets($files, $this->bag->getFilters(), $bag);
        }
    }

    /**
     * Writes compiled files for stylesheets included in templates.
     */
    protected function writeStyles()
    {
        $am     = $this->getContainer()->get('core.service.assetic.stylesheet_manager');
        $styles = $this->bag->getStyles();

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
