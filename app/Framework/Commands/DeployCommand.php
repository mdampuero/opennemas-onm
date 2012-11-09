<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Framework\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeployCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('framework:deploy')
            ->setDescription('Deploys the application to the latest version')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');

        chdir($basePath);

        $output->writeln(" - Updating onm instance");
        $gitOutput = exec('git pull');
        $output->writeln($gitOutput."\n");

        $output->writeln(" - Updating public themes\n");
        foreach (glob($basePath.'/public/themes/*') as $theme) {
            chdir($theme);
            $output->writeln("     * Updating ".basename($theme)." path");
            $gitOutput = exec('git pull');
            chdir($basePath);
            $output->writeln("");
        }

        $output->writeln(" - Updating vendor libraries");
        $composerOutput = exec($phpBinPath.' '.$basePath.'/bin/composer.phar install');
        $output->writeln($composerOutput."\n");
    }
}