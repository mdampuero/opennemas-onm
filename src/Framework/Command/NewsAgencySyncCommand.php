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

class NewsAgencySyncCommand extends ContainerAwareCommand
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
        // TODO: Remove ASAP
        $this->getContainer()->get('session')->set(
            'user',
            json_decode(json_encode([ 'id' => 0, 'username' => 'console' ]))
        );

        $logger       = $this->getContainer()->get('logger');
        $instanceName = $input->getArgument('instance');

        $instance = $this->getContainer()->get('core.loader')
            ->loadInstanceFromInternalName($instanceName);

        if ($instance->activated != '1') {
            $message = _('Instance not activated');
            throw new \Common\Core\Component\Exception\InstanceNotActivatedException($message);
        }

        $cache = $this->getContainer()->get('cache');
        $cache->setNamespace($instance->internal_name);

        $database = $instance->settings['BD_DATABASE'];

        $output->writeln("<fg=yellow>Start synchronizing {$instance->internal_name} instance...</>");
        $logger->info("Start synchronizing {$instance->internal_name} instance", array('cron'));

        $servers = $this->getContainer()->get('orm.manager')
            ->getDataSet('Settings', 'instance')->get('news_agency_config');

        $tpl  = $this->getContainer()->get('view')->getBackendTemplate();
        $path = $this->getContainer()->getParameter('core.paths.cache')
            . '/' . $instance->internal_name;

        $synchronizer = new Synchronizer($path, $tpl);

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
                    $output->writeln("<info> ==> {$synchronizer->stats['downloaded']} files downloaded</>");
                    $output->writeln("<info> ==> {$synchronizer->stats['contents']} contents found</>");
                    $logger->info("{$synchronizer->stats['deleted']} files deleted", array('cron'));
                    $logger->info("{$synchronizer->stats['downloaded']} files downloaded", array('cron'));
                    $logger->info("{$synchronizer->stats['contents']} contents found", array('cron'));

                    if (array_key_exists('auto_import', $server) && $server['auto_import']) {
                        $importer = $this->getContainer()->get('news_agency.importer');
                        $importer->configure($server);

                        $results = $importer->importAll();

                        if (!empty($results[1])) {
                            $output->writeln("<fg=yellow> ==> " . $results[1] . " contents already imported</>");
                            $logger->info($results[1] . " contents already imported", array('cron'));
                        }

                        if (!empty(count($results[0]))) {
                            $output->writeln("<info> ==> " . count($results[0]) . " contents imported</>\n");
                            $logger->info(count($results[0]) . " files downloaded", array('cron'));
                        }
                    }

                } catch (\Exception $e) {
                    $output->writeln("<error>Sync report for '{$instance->internal_name}': {$e->getMessage()}. Unlocking...</error>");
                }
            }
        }

        $synchronizer->updateSyncFile();
        $synchronizer->unlockSync();
    }
}
