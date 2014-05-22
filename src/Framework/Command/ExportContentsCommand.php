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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

class ExportContentsCommand extends ContainerAwareCommand
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

        $dbConn = $this->getContainer()->get('db_conn_manager');

        $rs = $dbConn->GetAll('SELECT internal_name, settings FROM instances');

        $instances = array();
        foreach ($rs as $database) {
            $instances[$database['internal_name']] =
                unserialize($database['settings']);
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
        define('INSTANCE_UNIQUE_NAME', $instance);

        // Initialize database connection
        $this->connection = $this->getContainer()->get('db_conn');
        $this->connection->selectDatabase($instances[$instance]['BD_DATABASE']);

        // Initialize application
        $GLOBALS['application'] = new \Application();
        \Application::load();
        \Application::initDatabase($this->connection);

        // Initialize the template system
        define('CACHE_PREFIX', '');

        $commonCachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }

        $this->tpl = new \TemplateAdmin('admin');


        $order   = array('created' => 'DESC');
        $filters = array(
            'content_type_name' => array(array('value' => 'article'))
        );

        $conn = $this->getContainer()->get('dbal_connection');
        $conn->selectDatabase($instances[$instance]['BD_DATABASE']);

        $em            = getService('entity_repository');
        $articles      = $em->findBy($filters, $order, $limit, 1);
        $countArticles = $em->countBy($filters);

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
            $images = $em->findBy(
                array(
                    'content_type_name' => array(array('value' => 'photo')),
                    'pk_content'        => array(array('value' => $imageIds, 'operator' => 'IN'))
                )
            );
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

    /**
     * Converts an Article to NewsML.
     *
     * @param  Article $article Article to convert.
     * @return string           Article in NewsML format.
     */
    public function convertToNewsML($article)
    {
        $content = $this->tpl->fetch(
            'news_agency/newsml_templates/base.tpl',
            array('article' => $article)
        );

        return $content;
    }

    /**
     * Copies images.
     *
     * @note Not implemented
     */
    public function copyImages($images)
    {
        return true;
    }

    /**
     * Writes an article in NewsML format to a file.
     *
     * @param Article $article      Article to export.
     * @param string  $newsMLString Article in NewsMML format.
     * @param string  $folder       Path where file will be created.
     */
    public function storeContentFile($article, $newsMLString, $folder)
    {
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        $filename = $folder . DIRECTORY_SEPARATOR . $article->id . '.xml';
        file_put_contents($filename, $newsMLString);
    }
}
