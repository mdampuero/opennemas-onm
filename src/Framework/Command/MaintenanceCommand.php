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

class MaintenanceCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:maintenance')
            ->setDescription('Enables/disables the maintenance mode of the framework')->setDefinition(
                array(
                    new InputArgument('action', InputArgument::REQUIRED, "Whether to 'enable' or 'disable' maintenance mode"),
                )
            )
            ->setHelp(
                <<<EOF
The <info>app:maintenance</info> enables the maintenance mode of the framework by
creating a .maintenance file at the root of the project.
Further backend requests will look for that file to show a "Maintenance mode" file,
therefore users will not be able to operate in backend section.

<info>php app/console app:maintenance enable</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

        if ($action !== 'enable' && $action !== 'disable') {
            $output->writeln('Option not valid');
            return 1;
        }

        $maintenanceFile = APP_PATH.'/../.maintenance';
        if ($action != 'enable') {
            $output->writeln('Disabling maintenance mode...');
            if (file_exists($maintenanceFile)) {
                unlink($maintenanceFile);
            }
        } else {
            $output->writeln('Enabling maintenance mode...');
            touch($maintenanceFile);
        }

    }
}
