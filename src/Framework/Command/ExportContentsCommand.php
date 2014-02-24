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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class ExportContentsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption('instance', 'i', InputOption::VALUE_REQUIRED, 'Instance to get contents from', '*'),
                    new InputOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of contents to export', 100),
                    new InputOption('target-dir', 't', InputOption::VALUE_REQUIRED, 'The folder where store backups', './backups'),
                )
            )
            ->setName('export:contents')
            ->setDescription('Exports contents from one instance to a given folder path')
            ->setHelp(
<<<EOF
The <info>%command.name%</info> exports contents from an instance:

  <info>%command.full_name%</info>

Specify from which instance you want to export the contents:

  <info>%command.full_name% --instance=path</info>

To specify where to store the backup you have to provide the <info>--target-dir</info> option:

  <info>%command.full_name% --target-dir=backups/</info>

You can specify the limit of contents to export with the <info>--limit</info> option:

  <info>%command.full_name% --limit=200</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get arguments
        $limit = $input->getOption('limit');
        $instance = $input->getOption('instance');
        $targetDir = $input->getOption('target-dir');

        // Initialize application
        $basePath = APPLICATION_PATH;
        $phpBinPath = exec('which php');

        chdir($basePath);

        // Initialize Databse connection
        $dbConn = $this->getContainer()->get('db_conn_manager');

        $rs = $dbConn->GetAll('SELECT internal_name, settings FROM instances');
        // $rs = $dbConn->getAll('SHOW DATABASES');

        $instances = array();
        foreach ($rs as $database) {
            $instances [$database['internal_name']] = unserialize($database['settings']);
        }
        $instanceNames = array_keys($instances);

        if ($instance == '*') {
            // Ask password
            $dialog = $this->getHelperSet()->get('dialog');

            $validator = function ($value) use ($instanceNames) {
                if (trim($value) == '') {
                    throw new \Exception('The instance name cannot be empty');
                }
                if (!in_array($value, $instanceNames)) {
                    throw new \Exception('Instance name not valid');
                }

                return $value;
            };

            $instance = $dialog->askHiddenResponseAndValidate(
                $output,
                'From what instance do you want to create the backup ('
                .implode(', ', $instanceNames)
                .'): ',
                $validator,
                5,
                true
            );
        } elseif (!in_array($instance, $instanceNames)) {
            throw new \Exception('Instance name not valid');
        }

        $output->writeln("Exporting content from the instance $instance");

        // Initialize internal constants for logger
        // Logger in content class when creating widgets
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));
        define('INSTANCE_UNIQUE_NAME', $instance);

        // Initialize application
        $GLOBALS['application'] = new \Application();
        \Application::initDatabase();
        \Application::initLogger();

        // Initialize the template system
        define('CACHE_PATH', APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.INSTANCE_UNIQUE_NAME);
        define('SS', '/');
        define('SITE_URL', '');
        $commonCachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }
        define('COMMON_CACHE_PATH', realpath($commonCachepath));
        $this->tpl = new \TemplateAdmin('admin');

        // Initialize database connection
        $dbParams['connections']['default']['database_name'] = $instances[$instance]['BD_DATABASE'];
        $dbConn = $this->getConnection($dbParams);

        $cm = new \ContentManager();
        list($countArticles, $articles)= $cm->getCountAndSlice(
            'Article',
            null,
            null,
            'ORDER BY created DESC',
            1,
            $limit
        );

        $imageIds = array();
        foreach ($articles as $article) {
            $imageIds []= $article->img1;

            // Load category related information
            $article->category_name  = $article->loadCategoryName($article->id);
            $article->category_title = $article->loadCategoryTitle($article->id);

            $article->created_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->created);
            $article->updated_datetime = \DateTime::createFromFormat('Y-m-d H:i:s', $article->changed);
        }

        if (count($imageIds) > 0) {
            $images = $cm->find('Photo', 'pk_content IN ('. implode(',', $imageIds) .')');
        } else {
            $images = array();
        }

        foreach ($articles as $article) {
            // Load attached and related contents from array
            $article->loadFrontpageImageFromHydratedArray($images);

            $newsMLString = $this->convertToNewsML($article);
            $this->storeContentFile($article, $newsMLString, $targetDir);
        }

        $this->copyImages($images);

        $output->writeln("\tSaved ".count($articles)." articles into '$targetDir' <info>DONE</info> ");
    }

    public function getConnection($connectionParams)
    {
        $GLOBALS['application']->conn = new \Onm\DatabaseConnection();

        $GLOBALS['application']->conn->connect($connectionParams);

        return $GLOBALS['application']->conn;
    }

    public function convertToNewsML($article)
    {
        $content = $this->tpl->fetch('news_agency/newsml_templates/base.tpl', array('article' => $article));

        return $content;
    }

    public function copyImages($images)
    {
        return true;
    }

    public function storeContentFile($article, $newsMLString, $folder)
    {
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        $filename = $folder.DIRECTORY_SEPARATOR.$article->id.'.xml';

        file_put_contents($filename, $newsMLString);
    }
}
