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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListInstanceCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('core:instance:list')
            ->setDescription('Lists all the available instances')
            ->setHelp(
                <<<EOF
The <info>instances:list</info> command shows a list with all the available instances.

<info>php bin/console core:instance:list</info>

EOF
            )
            ->addOption(
                'fields',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Fields to print (id, internal_name, name, domains, settings, ' .
                'activated, contact_mail, BD_DATABASE)'
            )
            ->addOption(
                'epp',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Elements per page. On large datasets this is used as the ' .
                'number of elements to load at batch.',
                20
            )
            ->addOption(
                'instance-id',
                'i',
                InputOption::VALUE_OPTIONAL,
                'Filter by instance ID'
            )
            ->addOption(
                'instance-name',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Filter by instance name'
            )
            ->addOption(
                'only-values',
                'o',
                InputOption::VALUE_NONE,
                'Only display the values of the fields printed, not keys'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $epp              = $input->getOption('epp');
        $this->fields     = $this->input->getOption('fields');
        $id               = $input->getOption('instance-id');
        $name             = $input->getOption('instance-name');
        $this->onlyValues = $this->input->getOption('only-values');

        $instance = $this->getContainer()->get('core.loader.instance')
            ->loadInstanceByName('manager')
            ->getInstance();

        $this->getContainer()->get('core.loader')->configureInstance($instance);

        // Get the total number of instances in the database and
        // the pages required to iterate over them
        $instanceCount = $this->getContainer()->get('orm.manager')
            ->getRepository('Instance')->countBy();

        $output->writeln('<info>Total instances:</info> ' . $instanceCount, OutputInterface::VERBOSITY_VERBOSE);
        $output->writeln('<info>Elements per page:</info> ' . $epp, OutputInterface::VERBOSITY_DEBUG);

        $oqlTemplate = 'limit %s offset %s';

        // Iterate over the pages and show information for the instances on that page
        $page = 0;
        while ($page * $epp < $instanceCount) {
            $this->output->writeln("Iteration $page", OutputInterface::VERBOSITY_DEBUG);

            $oql       = sprintf($oqlTemplate, $epp, $epp * $page);
            $instances = $this->getContainer()->get('orm.manager')
                ->getRepository('Instance')->findBy($oql);

            // If id instace parameter exits, use specific instance
            if ($id) {
                $instances = array_filter($instances, function ($instance) use ($id) {
                    return $instance->id == $id;
                });
            }

            if ($name) {
                $instances = array_filter($instances, function ($instance) use ($name) {
                    return $instance->internal_name == $name;
                });
            }
            $this->printInstanceInfo($instances);
            $page++;
        }
    }

    /**
     * Prints in the stdout the information about the array of instances received
     * as parameter
     *
     * @param array $instances the list of Instance objects to show infor for
     */
    private function printInstanceInfo($instances = [])
    {
        if (empty($instances)) {
            $this->output->writeln('No instances found!');
            return;
        }
        foreach ($instances as $instance) {
            $subdirectory     = $instance->subdirectory ?? '';
            $instance->domain = $instance->main_domain ?
                $instance->domains[$instance->main_domain - 1] . $subdirectory :
                $instance->domains[0] . $subdirectory;

            $str = 'name:' . $instance->internal_name
                . ';database:' . $instance->getDatabaseName()
                . ';main_domain:' . $instance->domain
                . ';domains:[' . implode(',', $instance->domains) . ']'
                . ';activated:' . $instance->activated;

            $fields = $this->fields ? explode(',', $this->fields) : [];

            if (!empty($fields)) {
                $filteredStr = '';
                foreach ($fields as $field) {
                    // Check if the field is a property of the object
                    if (isset($instance->{$field})) {
                        $value        = is_array($instance->{$field})
                                      ? implode('|', $instance->{$field})
                                      : $instance->{$field};
                        $filteredStr .= "$field:$value;";
                    }

                    // Check if the field is a key in the settings array
                    if (array_key_exists($field, $instance->settings)) {
                        $filteredStr .= "settings.$field: {$instance->settings[$field]}";
                    }
                }
                $str = $filteredStr;
            }

            $onlyValues = $this->onlyValues;

            if ($onlyValues) {
                $str = implode(';', array_map(function ($part) {
                    return explode(':', $part)[1] ?? $part;
                }, explode(';', $str)));
            }
            $this->output->writeln($str);
        }
    }
}
