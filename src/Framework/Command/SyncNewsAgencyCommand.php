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
                    new InputArgument('instance_database', InputArgument::REQUIRED, 'theme'),
                )
            )
            ->setHelp(
                <<<EOF
The <info>cache:clear</info> cache the app.

<info>php app/console cache:clear</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSystem = $this->getContainer()->get('filesystem');
        $logger     = $this->getContainer()->get('logger');
        $dbConn = $this->getContainer()->get('db_conn');

        $database = $input->getArgument('instance_database');
        $dbConn->selectDatabase($database);

        $logger->notice('Synchying '.$database. ' agencies elements.', array('cron'));


        // CRAP: take this out, Workaround
        \Application::load();
        \Application::initDatabase($dbConn);

        $servers = \Onm\Settings::get('news_agency_config');

        define('CACHE_PATH', APPLICATION_PATH.'/tmp/instances/opennemas/');

        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new \Onm\Import\Synchronizer\Synchronizer($syncParams);

        try {
            $messages = $synchronizer->syncMultiple($servers);
            foreach ($messages as $message) {
                $finalMessage = ' Sync "'.$database.'": '.$message;
                $logger->notice($finalMessage, array('cron'));
            }
        } catch (\Onm\Import\Synchronizer\LockException $e) {
            var_dump($e);die();

        } catch (\Exception $e) {
            $output->writeln("<error>Sync {$database} - {$e->getMessage()}</error>");
        }


        return false;
    }
}
