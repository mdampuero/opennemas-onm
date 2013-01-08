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

class LogsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean:logs')
            ->setDescription('Removes all the log files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $baseTmpLogsPath = realpath(APP_PATH.'/../tmp/logs');

        $output->write(" - Cleaning all the log files : ");

        if ($baseTmpLogsPath) {
            $fullLogsFolderPath = $baseTmpLogsPath.'/*';
            $files = glob($fullLogsFolderPath);
            $count = count($files);

            if ($count > 0) {
                $out = exec('rm '.$fullLogsFolderPath);
                $output->writeln($count.' files [REMOVED]');
            } else {
                $output->writeln('0 files [REMOVED]');
            }

            return;
        }
        $output->writeln('[FAILED]');
    }
}
