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

class SmartyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean:smarty-cache')
            ->setDescription('Cleans the smarty cache and compile files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseTmpInstancesPath = realpath(APP_PATH.'/../tmp/instances');

        $output->writeln(" - Cleaning smarty compile files for all instances.");

        foreach (glob($baseTmpInstancesPath.'/*') as $folder) {
            $output->write("\t* Compile files for ".basename($folder). ' instance ');

            $fullCompileFolderPath = realpath($folder.'/smarty/compile');
            if ($fullCompileFolderPath) {
                $out = exec('rm -r '.$fullCompileFolderPath);
                echo $out;
            }

            $output->writeln('[REMOVED]');
        }

        $output->writeln(" - Cleaning smarty cache files for all instances.");

        foreach (glob($baseTmpInstancesPath.'/*') as $folder) {
            $output->write("\t* Cache files for ".basename($folder). ' instance ');

            $fullCompileFolderPath = realpath($folder.'/smarty/cache');
            if ($fullCompileFolderPath) {
                $out = exec('rm -r '.$fullCompileFolderPath);
                echo $out;
            }

            $output->writeln('[REMOVED]');
        }

    }
}
