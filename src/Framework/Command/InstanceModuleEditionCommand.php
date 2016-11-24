<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstanceModuleEditionCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('instance:module:add')
            ->setDescription('Updates onm-instances database counters')
            ->setHelp(
                'Adds/removes an extension from an instance.'
            )
            ->addArgument(
                'instance',
                InputArgument::REQUIRED,
                'Instance internal name.'
            )
            ->addArgument(
                'uuid',
                InputArgument::REQUIRED,
                'Extension UUID.'
            )
            ->addOption(
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
        $name   = $input->getArgument('instance');
        $uuid   = $input->getArgument('uuid');
        $remove = $input->getOption('remove');

        $loader   = $this->getContainer()->get('core.loader');
        $instance = $loader->loadInstanceFromInternalName($name);

        $loader->init();

        $before = count($instance->purchased);

        $instance->purchased[] = $uuid;
        $instance->purchased   = array_unique($instance->purchased);

        if ($remove) {
            $instance->purchased = array_diff($instance->purchased, [ $uuid ]);
        }

        $after = count($instance->purchased);

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
