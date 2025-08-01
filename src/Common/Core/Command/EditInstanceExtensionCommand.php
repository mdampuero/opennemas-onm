<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EditInstanceExtensionCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('core:instance:extension')
            ->setDescription('Updates extensions for an instance')
            ->setHelp(
                <<<EOF
Adds/removes an extension from the list of purchased extensions.

If `activated` flag enabled, the extension is also enabled (Added to activated modules).

EOF
            )->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'Instance internal name.'
            )->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'Extension UUID.'
            )->addOption(
                'activate',
                'a',
                InputOption::VALUE_NONE,
                'If set, the extension will be enabled/disabled basing on the remove flag.'
            )->addOption(
                'remove',
                'r',
                InputOption::VALUE_NONE,
                'If set, the extension will be removed.'
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
        $name     = $input->getArgument('instance');
        $uuid     = $input->getArgument('uuid');
        $activate = $input->getOption('activate');
        $remove   = $input->getOption('remove');

        $instance = $this->getContainer()->get('core.loader.instance')
            ->loadInstanceByName($name)
            ->getInstance();

        $this->getContainer()->get('core.loader')->configureInstance($instance);

        $instance->purchased         = $instance->purchased ?? [];
        $instance->activated_modules = $instance->activated_modules ?? [];

        $before = count($instance->purchased)
            + count($instance->activated_modules);

        $instance->purchased[] = $uuid;
        $instance->purchased   = array_unique($instance->purchased);

        if ($activate) {
            $instance->activated_modules[] = $uuid;
            $instance->activated_modules   = array_unique($instance->activated_modules);
        }

        if ($remove) {
            $instance->purchased = array_values(
                array_diff($instance->purchased, [ $uuid ])
            );

            if ($activate) {
                $instance->activated_modules = array_values(
                    array_diff($instance->activated_modules, [ $uuid ])
                );
            }
        }

        $after = count($instance->purchased)
            + count($instance->activated_modules);

        if ($before !== $after) {
            $this->getContainer()->get('orm.manager')->persist($instance);

            $msg = sprintf(
                "Instance %s saved successfully (%d extensions %s).",
                $name,
                abs($before - $after),
                $remove ? 'removed' : 'added'
            );

            $output->writeln($msg);
        }
    }
}
