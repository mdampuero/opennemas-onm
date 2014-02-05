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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Onm\Settings as s;

class DisqusSyncCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('database', InputArgument::REQUIRED, 'database'),
                )
            )
            ->setName('disqus:import')
            ->setDescription('Executes comments import action with Disqus Api')
            ->setHelp(
                <<<EOF
The <info>disqus:import</info> executes acomments import action with Disqus Api.

<info>php app/console disqus:import database</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpBinPath = exec('which php');
        chdir(APPLICATION_PATH);

        // Initialize internal constants
        define('CACHE_PREFIX', 'disqus');
        define('INSTANCE_UNIQUE_NAME', 'community');

        // Get database name from prompt
        $databaseName = $input->getArgument('database');

        // Get database connection
        $databaseConnection = getService('db_conn');
        $databaseConnection->selectDatabase($databaseName);

        // Load application and initialize Database
        \Application::load();
        \Application::initDatabase($databaseConnection);

        // Initialize script
        $output->writeln("\tStart disqus comments import");

        // Import
        $this->fetchDisqusPosts();

        // Finish script
        $output->writeln("\n\tFinished disqus comments import");
    }

    protected function fetchDisqusPosts()
    {
        // Get cache service and save disqus_last_sync cache time and uuid
        $cache = getService('cache');
        $cache->save(
            CACHE_PREFIX.'disqus_last_sync',
            array('time' => time(), 'uuid' => uniqid()),
            300
        );

        // Save disqus comments to database
        \Onm\DisqusSync::saveDisqusCommentsToDatabase();
    }
}
