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

class ServerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('server')
            ->setDescription('Starts a new PHP server using Opennemas as SaaS')
            ->addOption(
                'port',
                null,
                InputOption::VALUE_REQUIRED,
                'The port where listen for requests from',
                8000
            )
            ->addOption(
                'domain',
                null,
                InputOption::VALUE_REQUIRED,
                'The base domain where listen for requests from',
                'localhost'
            )
            ->addOption(
                'v',
                'verbose',
                InputOption::VALUE_REQUIRED,
                'The base domain where listen for requests from',
                'localhost'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $webPath    = APPLICATION_PATH."/public/";
        $phpBinPath = exec('which php');

        $port   = $input->getOption('port');
        $domain = $input->getOption('domain');

        $output->writeln("<info>Initiazing opennemas server at $domain:$port</info>");

        exec($phpBinPath." -S $domain:$port -t ".$webPath." ".$webPath."/app.php");
    }
}
