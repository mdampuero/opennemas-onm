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
use Symfony\Component\Console\Output\OutputInterface;

class DisqusSyncCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('disqus:import')
            ->setDescription('Executes comments import action with Disqus Api')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'database'
            )->setHelp(
                <<<EOF
The <info>disqus:import</info> executes acomments import action with Disqus Api.

<info>php app/console disqus:import database</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        chdir(APPLICATION_PATH);

        // Initialize internal constants
        define('CACHE_PREFIX', 'disqus');
        define('INSTANCE_UNIQUE_NAME', 'community');

        // Get database name from prompt
        $databaseName = $input->getArgument('database');

        // Get database connection
        $conn = $this->getContainer()->get('orm.manager')->getConnection('instance');
        $conn->selectDatabase($databaseName);

        // Initialize script
        $output->writeln("\tStart disqus comments import");

        // Import
        $this->fetchDisqusPosts();

        // Finish script
        $output->writeln("\n\tFinished disqus comments import");
    }

    protected function fetchDisqusPosts()
    {
        $this->getContainer()->get('cache')->save(
            CACHE_PREFIX . 'disqus_last_sync',
            [ 'time' => time(), 'uuid' => uniqid() ],
            300
        );

        $settings = $this->getContainer()
            ->get('orm.manager')
            ->getDataSet('Settings')
            ->get([ 'disqus_shortname', 'disqus_secret_key' ]);

        $disqusSyncher = new \Onm\DisqusSync();
        $disqusSyncher->setConfig(
            $settings['disqus_shortname'],
            $settings['disqus_secret_key']
        );

        // Save disqus comments to database
        $disqusSyncher->saveDisqusCommentsToDatabase();
    }
}
