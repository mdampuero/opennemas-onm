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

use Framework\Import\Synchronizer\Synchronizer;

class SyncNewsAgencyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('sync:newagency')
            ->setDescription('Cleans all the Symfony generated files')
            ->setDefinition(
                array(
                    new InputArgument('instance', InputArgument::REQUIRED, 'The instance internal name.'),
                )
            )
            ->setHelp(
                <<<EOF
The <info>sync:newagency</info> command synchronizes the instance new agencies.

Put this as a cron action:

<info>php app/console sync:newagency <instance></info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');
        $dbConn = $this->getContainer()->get('db_conn');
        $im     = $this->getContainer()->get('instance_manager');

        $instanceName = $input->getArgument('instance');

        $instance = $im->findOneBy(
            ['internal_name' => [ ['value' => $instanceName] ] ]
        );

        if (!is_object($instance)) {
            throw new \Onm\Exception\InstanceNotFoundException(_('Instance not found'));
        }

        if ($instance->activated != '1') {
            $message = _('Instance not activated');
            throw new \Onm\Instance\NotActivatedException($message);
        }

        $instance->boot();

        $im->current_instance = $instance;
        $im->cache_prefix     = $instance->internal_name;

        $cache = $this->getContainer()->get('cache');
        $cache->setNamespace($instance->internal_name);

        $database = $instance->settings['BD_DATABASE'];
        $dbConn->selectDatabase($database);

        $output->writeln("<fg=yellow>Start synchronizing {$instance->internal_name} instance...</>");
        $logger->info("Start synchronizing {$instance->internal_name} instance", array('cron'));

        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($dbConn);

        $sm = $this->getContainer()->get('setting_repository');
        $sm->setConfig([
            'database' => $database,
            'cache_prefix' => $instance->internal_name
        ]);

        $servers = $sm->get('news_agency_config');

        $syncParams = array('cache_path' => CACHE_PATH);

        $synchronizer = new Synchronizer($syncParams);

        if (!$synchronizer->isSyncEnvironmetReady()) {
            $synchronizer->setupSyncEnvironment();
        }

        $synchronizer->lockSync();

        foreach ($servers as $server) {
            $synchronizer->resetStats();

            if ($server['activated']) {
                try {
                    $output->writeln("==> Synchronizing files from {$server['name']}...");
                    $synchronizer->sync($server);

                    $output->writeln("<fg=red> ==> {$synchronizer->stats['deleted']} files deleted</>");
                    $output->writeln("<info> ==> {$synchronizer->stats['downloaded']} files downloaded</info>");
                    $output->writeln("<info> ==> {$synchronizer->stats['contents']} contents found</info>\n");

                    $logger->info("{$synchronizer->stats['deleted']} files deleted", array('cron'));
                    $logger->info("{$synchronizer->stats['downloaded']} files downloaded", array('cron'));
                    $logger->info("{$synchronizer->stats['contents']} contents found", array('cron'));

                } catch (\Exception $e) {
                    $output->writeln("<error>Sync report for '{$instance->internal_name}': {$e->getMessage()}. Unlocking...</error>");
                }
            }
        }

        $synchronizer->updateSyncFile();
        $synchronizer->unlockSync();
    }
}
