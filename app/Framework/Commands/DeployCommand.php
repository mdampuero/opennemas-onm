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
use Symfony\Component\Console\Input\ArrayInput;
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

        // Update onm-core
        $output->writeln(" - Updating onm instance");
        $gitOutput = exec('git pull');
        $output->writeln($gitOutput."\n");

        // Update themes
        $output->write(" - Updating public themes");
        foreach (glob($basePath.'/public/themes/*') as $theme) {
            // Avoid to execute pull in admin and manager themes.
            if (basename($theme) == 'admin' || basename($theme) == 'manager') {
                continue;
            }
            chdir($theme);
            $output->write("\n     * Updating ".basename($theme)." theme ");
            $gitOutput = exec('git pull');
            chdir($basePath);
            // $output->writeln("");
        }
        $output->writeln('');

        // Update dependencies
        $output->writeln(" - Updating vendor libraries");
        $composerOutput = exec($phpBinPath.' '.$basePath.'/bin/composer.phar install');
        $output->writeln($composerOutput."\n");

        // Clean cache and compiles
        $command = $this->getApplication()->find('cache:clean');
        $arguments = array(
            'command' => 'cache:clean',
        );

        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);
    }
}