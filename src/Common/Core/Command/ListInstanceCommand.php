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
                'field',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Field to print (id, internal_name, name, domains, settings, ' .
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
                'target',
                't',
                InputOption::VALUE_OPTIONAL,
                'Filter by instance ID or name'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;

        $epp         = $input->getOption('epp');
        $this->field = $this->input->getOption('field');
        $target      = $input->getOption('target');

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

            // If target instace parameter exits, use specific instance
            if ($target) {
                $instances = array_filter($instances, function ($instance) use ($target) {
                    return $instance->id == $target || $instance->internal_name == $target;
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
            $instance->domain = $instance->domains[$instance->main_domain - 1] . $subdirectory;

            $str = 'Name: ' . $instance->internal_name
                . ', database: ' . $instance->getDatabaseName()
                . ', main domain: ' . $instance->domain
                . ', domains: [ ' . implode(', ', $instance->domains) . ' ]'
                . ', activated: ' . $instance->activated;

            $field = $this->field;

            if (!empty($field)) {
                $str = 'Name: ' . $instance->internal_name;

                // Check if the field is a property of the object
                if (!empty($instance->{$field})) {
                    $value = is_array($instance->{$field}) ? implode('|', $instance->{$field}) : $instance->{$field};
                    $str  .= ", $field: $value";
                }

                // Check if the field is a key in the settings array
                if (array_key_exists($field, $instance->settings)) {
                    $str .= ", settings.$field: {$instance->settings[$field]}";
                }
            }

            $this->output->writeln($str);
        }
    }
}
