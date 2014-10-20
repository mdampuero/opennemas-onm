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

class CleanLogsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean:logs')
            ->setDescription('Removes all the log files')
            ->setHelp(
                <<<EOF
The <info>clean:logs</info> removes all the files from the tmp/logs directory.

<info>php app/console clean:logs</info>

EOF
            );
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
                exec('rm '.$fullLogsFolderPath);
                $output->writeln($count.' files [REMOVED]');
            } else {
                $output->writeln('0 files [REMOVED]');
            }

            return;
        }
        $output->writeln('[FAILED]');
    }
}
