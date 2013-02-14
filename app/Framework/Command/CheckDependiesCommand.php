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

class CheckDependiesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('framework:check-dependencies')
            ->setDescription('Checks if all the dependencies for the framework is installed')
            ->addOption(
                'html',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will output in html format'
            )
            ->setHelp(
                <<<EOF
The <info>framework:check-dependencies</info> checks if all the required dependencies
are fulfilled for executing the onm application.

<info>php app/console framework:check-dependencies</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Not implemented');
    }
}
