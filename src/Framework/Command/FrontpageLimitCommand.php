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

use Onm\DatabaseConnection;

class FrontpageLimitCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputArgument('instance-name', InputArgument::REQUIRED, 'instance-name'),
                )
            )
            ->setName('frontpage:limit')
            ->setDescription(
                'Executes command to clean up frontpage elements from all categories that exceeds the limit'
            )
            ->setHelp(
                <<<EOF
The <info>frontpage:limit</info> executes command to clean up frontpage elements from all categories that exceeds the limit.

<info>php app/console frontpage:limit instance-name</info>

EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $instance = $input->getArgument('instance-name');

        $this->getContainer()->get('session')->set(
            'user',
            json_decode(json_encode([ 'id' => 0, 'username' => 'console' ]))
        );

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

        // Get all categories
        $allcategorys  = getService('category_repository')->findBy(
            ['internal_category' => [['value' => '1']]],
            'name ASC'
        );

        $cm = new \ContentManager();
        foreach ($allcategorys as $category) {
            $cache = getService('cache');
            $cache->delete('frontpage_elements_map_' . $category->id);

            // Get all contents id before cleaning
            $contentsIds = $cm->getContentsIdsForHomepageOfCategory($category->id);

            // Get cleaned contents for all valid categories
            $contents = $cm->getContentsForHomepageOfCategory($category->id);

            // Iterate over each element and fetch its parameters to save.
            $cleanContents = [];
            foreach ($contents as $content) {
                $cleanContents[] = [
                    'id'           => $content->id,
                    'category'     => $category->id,
                    'placeholder'  => $content->placeholder,
                    'position'     => $content->position,
                    'content_type' => $content->content_type_l10n_name,
                ];
            }

            // Save contents
            $savedProperly = \ContentManager::saveContentPositionsForHomePage(
                $category->id,
                $cleanContents
            );

            $output->writeln(
                'Category '. $category->title . ': Total ' . count($contentsIds)
                . ' : Removed ' . (count($contentsIds) - count($contents))
            );

            $cache->delete('frontpage_elements_map_' . $category->id);
        }
    }
}
