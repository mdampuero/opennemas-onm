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
use Symfony\Component\Yaml\Parser;

use Onm\DatabaseConnection;

class CreateAdsOnmCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('instance-name', InputArgument::REQUIRED, 'instance-name'),
                    new InputArgument('config-file', InputArgument::REQUIRED, 'config-file'),
                )
            )
            ->setName('create:ads:onm')
            ->setDescription(
                'Executes command to create onm ads on instace'
            )
            ->setHelp(
                <<<EOF
The <info>create:ads:onm</info> executes command to create onm ads on instace.

<info>php app/console create:ads:onm instance-name config-file</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instance = $input->getArgument('instance-name');

        // Initialize application
        $basePath = APPLICATION_PATH;

        chdir($basePath);

        $dbConn = $this->getContainer()->get('db_conn_manager');

        $rs = $dbConn->GetAll('SELECT internal_name, settings FROM instances');

        $instances = array();
        foreach ($rs as $database) {
            $dbSettings = unserialize($database['settings']);
            $instances[$database['internal_name']] = $dbSettings;
        }

        $instanceNames = array_keys($instances);

        if (!in_array($instance, $instanceNames)) {
            throw new \Exception('Instance name not valid');
        }

        // Initialize internal constants for logger
        define('INSTANCE_UNIQUE_NAME', $instance);

        // Initialize database connection
        $this->connection = $this->getContainer()->get('db_conn');
        $this->connection->selectDatabase($instances[$instance]['BD_DATABASE']);
        $conn = getService('dbal_connection');
        $conn->selectDatabase($instances[$instance]['BD_DATABASE']);

        // Initialize application
        $GLOBALS['application'] = new \Application();
        \Application::load();
        \Application::initDatabase($this->connection);

        define('CACHE_PREFIX', $instance);

        $_SESSION['username'] = 'console';
        $_SESSION['userid'] = '0';

        $path = $input->getArgument('config-file');
        if (!file_exists($path)) {
            throw new \Exception("Can't access the file or doesn't exists");
        }

        $yaml = new Parser();
        $ads  = $yaml->parse(file_get_contents($path));

        if (is_null($ads) || empty($ads)) {
            throw new \Exception("File content is not valid");
        }

        $am = new \Advertisement();
        foreach ($ads as $ad) {
            $data = [
                'title' => $ad['title'],
                'metadata' => \StringUtils::getTags($ad['title']),
                'category' => '0',
                'categories' => $ad['categories'],
                'available' => '1',
                'content_status' => '1',
                'with_script' => '1',
                'overlap' => '',
                'type_medida' => 'NULL',
                'num_clic' => '',
                'num_view' => '',
                'starttime' => '2015-08-14 00:00:00',
                'endtime' => '',
                'timeout' => '15',
                'url' => 'http://www.opennemas.com',
                'img' => '',
                'script' => $ad['script'],
                'type_advertisement' => $ad['type'],
                'fk_author' => '0',
                'fk_publisher' => '0',
                'params' =>
                    [
                        'width' => $ad['width'],
                        'height' => $ad['height'],
                        'openx_zone_id' => '',
                        'googledfp_unit_id' => '',
                    ]
            ];

            if ($am->create($data)) {
                $output->writeln('Ad '.$data['title'].' created successfully');
            } else {
                $output->writeln('Failed to create ad '.$data['title']);
            }
        }
    }
}
