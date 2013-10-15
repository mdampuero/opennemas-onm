<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CleanSmartyCacheCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean:smarty-cache')
            ->setDescription('Cleans the smarty cache and compile files')
            ->addArgument(
                'theme',
                InputArgument::OPTIONAL,
                'What instance do you want to clean?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseTmpInstancesPath = realpath(APP_PATH.'/../tmp/instances');

        $themeName = $input->getArgument('theme');

        if ($themeName != null) {
            $folder = $baseTmpInstancesPath.'/'.$themeName;
            $output->writeln(" - Cleaning smarty files for instance ".$themeName);
            $this->cleanCompileForTheme($input, $output, $folder);
            $this->cleanCacheForTheme($input, $output, $folder);
        } else {
            $output->writeln(" - Cleaning smarty compile files for all instances.");

            foreach (glob($baseTmpInstancesPath.'/*') as $folder) {
                $this->cleanCompileForTheme($input, $output, $folder);
            }

            $output->writeln(" - Cleaning smarty cache files for all instances.");

            foreach (glob($baseTmpInstancesPath.'/*') as $folder) {
                $this->cleanCacheForTheme($input, $output, $folder);
            }
        }
    }

    /**
     * Cleans compile files for a theme
     *
     * @return void
     **/
    private function cleanCompileForTheme($input, $output, $themePath)
    {
        $output->write("\t* Compile files for ".basename($themePath). ' instance ');

        $fullCompileFolderPath = realpath($themePath.'/smarty/compile');
        if ($fullCompileFolderPath) {
            $out = exec('rm -r '.$fullCompileFolderPath);
            echo $out;
        }

        $output->writeln('[REMOVED]');
    }

    /**
     * Cleans cache files for a theme
     *
     * @return void
     **/
    private function cleanCacheForTheme($input, $output, $themePath)
    {
        $output->write("\t* Cache files for ".basename($themePath). ' instance ');

        $fullCompileFolderPath = realpath($themePath.'/smarty/cache');
        if ($fullCompileFolderPath) {
            $out = exec('rm -r '.$fullCompileFolderPath);
            echo $out;
        }

        $output->writeln('[REMOVED]');
    }
}
