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
            if (!file_exists($folder)) {
                $output->writeln('Instance name not valid');
                return 1;
            }

            $output->writeln(" - Cleaning cache files for instance ".$themeName);
            $this->cleanCacheForTheme($output, $folder);
        } else {
            $output->writeln(" - Cleaning cache files");

            foreach (glob($baseTmpInstancesPath.'/*') as $folder) {
                $this->cleanCacheForTheme($output, $folder);
            }
        }
        $this->cleanCompileForTheme($output, $baseTmpInstancesPath);
    }

    /**
     * Cleans compile files for a theme
     *
     * @return void
     **/
    private function cleanCompileForTheme($output, $baseTmpInstancesPath)
    {
        $fullCompileFolderPath = realpath($baseTmpInstancesPath.'/common/smarty');

        $output->write(" - Cleaning common compile folder ");
        if ($fullCompileFolderPath) {
            $out = exec('rm -r '.$fullCompileFolderPath);
            $output->write($out);
        }

        $output->writeln('[REMOVED]');
    }

    /**
     * Cleans cache files for a theme
     *
     * @return void
     **/
    private function cleanCacheForTheme($output, $themePath)
    {
        $output->write("\t* '".basename($themePath)."' cache files ");

        $fullCompileFolderPath = realpath($themePath.'/smarty/cache');
        if ($fullCompileFolderPath) {
            $out = exec('rm -r '.$fullCompileFolderPath);
            $output->write($out);
        }

        $output->writeln('[REMOVED]');
    }
}
