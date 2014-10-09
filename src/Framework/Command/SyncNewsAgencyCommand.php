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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class SyncNewsAgencyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sync:newagency')
            ->setDescription('Cleans all the Symfony generated files')
            ->setDefinition(
                array(
                    new InputArgument('instance_internal_name', InputArgument::REQUIRED, 'internal_name'),
                )
            )
            ->setHelp(
                <<<EOF
The <info>sync:newagency</info> command syncs the instance new agencies if setted up previously.

This is to put as a cron action

<info>php app/console sync:newagency instance_internal_name</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = $this->getContainer()->get('filesystem');
        $logger     = $this->getContainer()->get('logger');
        $dbConn     = $this->getContainer()->get('db_conn');
        $instanceManager = $this->getContainer()->get('instance_manager');

        $instanceName = $input->getArgument('instance_internal_name');

        $instance = $instanceManager->findOneBy(
            array('internal_name' => array(array('value' => $instanceName)))
        );

        //If found matching instance initialize its contants and return it
        if (is_object($instance)) {
            $instance->boot();

            // If this instance is not activated throw an exception
            if ($instance->activated != '1') {
                $message =_('Instance not activated');
                throw new \Onm\Instance\NotActivatedException($message);
            }
        } else {
            throw new \Onm\Exception\InstanceNotFoundException(_('Instance not found'));
        }

        $instanceManager->current_instance = $instance;
        $instanceManager->cache_prefix     = $instance->internal_name;

        $cache = getService('cache');
        $cache->setNamespace($instance->internal_name);

        $database = $instance->settings['BD_DATABASE'];
        $dbConn->selectDatabase($database);

        $logger->notice("Start syncing '{$instance->internal_name}' agencies elements.", array('cron'));

        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($dbConn);

        $servers = \Onm\Settings::get('news_agency_config');

        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncParams);

        try {
            $messages = $synchronizer->syncMultiple($servers);
            foreach ($messages as $message) {
                $finalMessage = "Sync report for '{$instance->internal_name}': {$message}";
                $logger->notice($finalMessage, array('cron'));
            }
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            $output->writeln("<error>Sync report for '{$instance->internal_name}': {$e->getMessage()}. Unlocking and it will sync the next time.</error>");
            $synchronizer->unlockSync();
        } catch (\Exception $e) {
            $output->writeln("<error>Sync report for '{$instance->internal_name}': {$e->getMessage()}</error>{}");
            print_r($e->getTrace()[0]['args']);die();
        }


        return false;
    }
}
