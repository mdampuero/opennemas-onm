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
        $instance     = $instanceManager->loadFromInternalName($instanceName);

        $instanceManager->current_instance = $instance;
        $instanceManager->cache_prefix     = $instance->internal_name;

        $cache = getService('cache');
        $cache->setNamespace($instance->internal_name);

        $database = $instance->settings['BD_DATABASE'];
        $dbConn->selectDatabase($database);

        $logger->notice('Synchying '.$instance->internal_name. ' agencies elements.', array('cron'));

        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($dbConn);

        $servers = \Onm\Settings::get('news_agency_config');

        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncParams);

        try {
            $messages = $synchronizer->syncMultiple($servers);
            foreach ($messages as $message) {
                $finalMessage = ' Sync "'.$database.'": '.$message;
                $logger->notice($finalMessage, array('cron'));
            }
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            $output->writeln("<error>Sync {$instance->internal_name} - {$e->getMessage()}</error>");
            $synchronizer->unlockSync();
        } catch (\Exception $e) {
            $output->writeln("<error>Sync {$instance->internal_name} - {$e->getMessage()}</error>");
        }


        return false;
    }
}
