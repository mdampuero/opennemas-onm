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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class ListInstancesCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName('instances:list')
            ->setDescription('Lists all the available instances')
            ->setHelp(
                <<<EOF
The <info>instances:list</info> command shows a list with all the available instances.

<info>php bin/console instances:list</info>

EOF
            )
            ->addOption(
                'field',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Field to print (id, internal_name, name, domains, settings, '.
                'activated, contact_mail, BD_DATABASE)'
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
        $field = $input->getOption('field');

        $this->getContainer()->get('core.loader')
            ->loadInstanceFromInternalName('manager');

        $instances = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->findBy();

        foreach ($instances as $instance) {
            $str = 'Name: ' . $instance->internal_name
                . ', database: ' . $instance->getDatabaseName()
                . ', domains: [ ' . implode(', ', $instance->domains) . ' ]'
                . ', activated: ' . $instance->activated;

            if (!empty($field)) {
                $str = 'Name: ' . $instance->internal_name;

                if (!empty($instance->$field)) {
                    $str .= ", $field: {$instance->$field}";
                }

                if (array_key_exists($field, $instance->settings)) {
                    $str .= ", settings.$field: {$instance->settings[$field]}";
                }
            }

            $output->writeln($str);
        }
    }
}
