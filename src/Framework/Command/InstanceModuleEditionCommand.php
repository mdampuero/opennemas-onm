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
        $instanceName = $input->getArgument('instance');
        $uuid         = $input->getArgument('uuid');

        $em = $this->getContainer()->get('instance_manager');
        $this->getContainer()->get('cache_manager')->setNamespace('manager');

        $instance = $em->findOneBy(
            ['internal_name' => [ [ 'value' => $instanceName ] ] ]
        );

        if (!is_object($instance)) {
            throw new \Onm\Exception\InstanceNotFoundException(_('Instance not found'));
        }

        if (empty($instance->metas)) {
            $instance->metas = [ 'purchased' => [] ];
        }

        if (empty($instance->metas['purchased'])) {
            $instance->metas['purchased'] = [];
        }

        if ($input->getOption('remove')) {
            $before = count($instance->metas['purchased']);
            $instance->metas['purchased'] =
                array_diff($instance->metas['purchased'], [ $uuid ]);
            $after = count($instance->metas['purchased']);

            if ($before !== $after) {
                $msg = "Instance %s saved successfully (%d extensions removed).";
                $em->persist($instance);
                $output->writeln(sprintf($msg, $instanceName, $before - $after));
            }

            return;
        }

        $before = count($instance->metas['purchased']);
        $instance->metas['purchased'][] = $uuid;
        $instance->metas['purchased'] = array_unique($instance->metas['purchased']);
        $after = count($instance->metas['purchased']);

        if ($before !== $after) {
            $msg = "Instance %s saved successfully (%d extensions added).";
            $em->persist($instance);
            $output->writeln(sprintf($msg, $instanceName, $after - $before));
        }

        return;
    }
}
