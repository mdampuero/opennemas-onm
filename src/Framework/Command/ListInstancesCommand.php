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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class ListInstancesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption(
                        'field',
                        'f',
                        InputOption::VALUE_OPTIONAL,
                        'Field to print (id, internal_name, name, domains, settings, '.
                        'activated, contact_mail, BD_DATABASE)'
                    ),
                )
            )
            ->setName('instances:list')
            ->setDescription('Lists all the available instances')
            ->setHelp(
                <<<EOF
The <info>instances:list</info> command shows a list with all the available instances.

<info>php bin/console instances:list</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dbConn = $this->getContainer()->get('db_conn_manager');
        $rs = $dbConn->GetArray('SELECT * FROM instances');

        $field = $input->getOption('field');
        foreach ($rs as $instance) {
            $instance['settings'] = unserialize($instance['settings']);

            if (!is_null($field)) {
                if (array_key_exists($field, $instance)) {
                    $output->writeln($instance[$field]);
                } else {

                    $output->writeln($instance['settings'][$field]);
                }
            } else {
                $output->writeln(
                    '- INT_NAME: '.$instance['internal_name'].
                    ', DB_NAME: '.$instance['settings']['BD_DATABASE'].
                    ', DOMAINS: \''.$instance['domains'].'\''.
                    ', ACTIVATED: '.$instance['activated']
                );
            }

        }

    }
}
