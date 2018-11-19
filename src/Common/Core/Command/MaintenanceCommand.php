<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The MaintenanceCommand class defines a command to enable or disable the
 * maintenance mode.
 */
class MaintenanceCommand extends ContainerAwareCommand
{
    /**
     * Configures the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:maintenance')
            ->setDescription('Enables/disables the maintenance mode of the framework')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                'Whether to "enable" or "disable" maintenance mode'
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

    /**
     * Executes the command.
     *
     * @param InputInterface  $input  The input object.
     * @param OutputInterface $output The output object.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');

        if ($action !== 'enable' && $action !== 'disable') {
            $output->writeln('Option not valid');
            return;
        }

        $file = $this->getContainer()->getParameter('core.maintenance.file');

        if ($action != 'enable') {
            $output->write('Disabling maintenance mode...');

            if (file_exists($file)) {
                unlink($file);
            }

            $output->writeln('<info>Disabled</info>');

            return;
        }

        $output->write('Enabling maintenance mode...');
        touch($file);
        $output->writeln('<info>Enabled</info>');
    }
}
