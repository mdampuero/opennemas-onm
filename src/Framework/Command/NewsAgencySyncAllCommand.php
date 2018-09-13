<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Jack\Symfony\ProcessManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class NewsAgencySyncAllCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('sync:news-agency:all')
            ->setDescription('Synchronizes news agencies for all instances')
            ->addOption(
                'processes',
                'p',
                InputOption::VALUE_REQUIRED,
                'Maximum number of parallel processes.'
            )
            ->setHelp(
                <<<EOF
The <info>sync:newagency</info> command synchronizes the instance new agencies.

Put this as a cron action:

<info>php app/console sync:newagency <instance></info>

EOF
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance.
     * @param OutputInterface $output An OutputInterface instance.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Force ORM and Cache intialization
        $this->getContainer()->get('core.loader');

        $max = 2;
        $oql = sprintf(
            'activated = 1 and activated_modules ~ "%s"',
            'NEWS_AGENCY_IMPORTER'
        );

        if (!empty($input->getOption('processes'))) {
            $max = $input->getOption('processes');
        }

        $instances = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy($oql);

        if (empty($instances)) {
            $output->writeln('No instances to synchronize');
            return ;
        }

        $output->writeln('Instances to synchronize:');
        $processes = [];
        foreach ($instances as $instance) {
            $output->writeln('  - ' . $instance->internal_name);

            $cmd = 'php '. $this->getContainer()->getParameter('kernel.root_dir')
                . '/../bin/console sync:newagency ' . $instance->internal_name;

            $processes[] = new Process($cmd);
        }

        $pm = new ProcessManager();
        $pm->runParallel($processes, $max, 1000);
    }
}
