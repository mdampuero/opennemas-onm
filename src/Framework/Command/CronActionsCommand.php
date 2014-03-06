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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class CronActionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:run')
            ->setDescription('Executes all the cron actions')
            ->setHelp(
                <<<EOF
The <info>cron:run</info> executes all the registered cron actions.

<info>php app/console cron:run</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $eventDispatcher = $this->getContainer()->get('event_dispatcher');

        $output->writeln('Executing cron actions');

        $event = new Event();
        $event->input     = $input;
        $event->output    = $output;
        $event->container = $this->getContainer();

        $eventDispatcher->dispatch('cron.actions', $event);
    }
}
