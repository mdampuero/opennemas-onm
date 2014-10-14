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
                    new InputOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of contents to export', '*'),
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
        $this->limit = $input->getOption('limit');
        $instance = $input->getOption('instance');
        $this->targetDir = $input->getOption('target-dir');

        $this->input = $input;
        $this->output = $output;

        // Initialize application
        $basePath = APPLICATION_PATH;

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

        $output->writeln("Exporting contents from instance $instance");

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

        $conn = $this->getContainer()->get('dbal_connection');
        $conn->selectDatabase($instances[$instance]['BD_DATABASE']);

        // Set media
        $this->mediaPath = APPLICATION_PATH.DS.'public'.DS.'media'.DS.$instance;
        define('MEDIA_IMG_PATH_WEB', 'media/'.$instance.'/'.'images');

        $this->exportContents();
    }

    /**
     * Converts an Article, Opinion and album to NewsML.
     *
     * @param  Content $content Content to convert.
     * @return string           Content in NewsML format.
     */
    public function convertToNewsML($content)
    {
        $content = $this->tpl->fetch(
            'news_agency/newsml_templates/base.tpl',
            array(
                'article'    => $content,
                'photo'      => $content->img1,
                'photoInner' => $content->img2
            )
        );

        return $content;
    }

    /**
     * Converts a Video to NewsML.
     *
     * @param  Video $content Video to convert.
     * @return string         Video in NewsML format.
     */
    public function convertVideoToNewsML($content)
    {
        $content = $this->tpl->fetch(
            'news_agency/newsml_templates/video.tpl',
            array(
                'video'    => $content,
            )
        );

        return $content;
    }

    /**
     * Copy an image to another location.
     *
     * @param  string $source path to original image
     * @param  string $dest path of destination
     * @param  string $file file name of image
     *
     */
    public function copyImage($source, $dest, $file)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0777, true);
        }

        $isCopied = @copy($source, $dest.$file);

        return $isCopied;
    }

    /**
     * Writes an article in NewsML format to a file.
     *
     * @param Article $content      Article to export.
     * @param string  $newsMLString Article in NewsMML format.
     * @param string  $folder       Path where file will be created.
     */
    public function storeContentFile($content, $newsMLString, $folder)
    {
        if (!is_dir($folder)) {
            mkdir($folder, 0777);
        }

        $filename = $folder.DIRECTORY_SEPARATOR.$content->content_type_name.$content->id.'.xml';
        file_put_contents($filename, $newsMLString);
    }

    /**
     * Export all articles from an instance in xml files
     *
     */
    public function exportContents()
    {
        // Sql order, limit and filters
        $order   = array('created' => 'DESC');
        $filters = array(
            'content_type_name' => array(
                'union' => 'OR',
                array('value' => 'article'),
                array('value' => 'opinion'),
                array('value' => 'album'),
                array('value' => 'video'),
            ),
        );

        // Get entity repository
        $this->er = getService('entity_repository');

        // Get total per page contents
        $perPage = 100;

        // Initialize counters
        $this->imagesCounter = 0;
        $this->articlesCounter = 0;
        $this->opinionsCounter = 0;
        $this->albumsCounter = 0;
        $this->videosCounter = 0;

        // Set contents type array
        $types = array(
            'video',
            'album',
            'opinion',
            'article',
        );

        // Fetch contents
        foreach ($types as $type) {
            $filters = array(
                'content_type_name' => array(array('value' => $type)),
            );
            if ($this->limit != '*') {
                $contents = $this->er->findBy($filters, $order, $this->limit, 1);
                $this->processContents($contents);
            } else {
                // Count contents
                $countContents = $this->er->countBy($filters);
                $iterations = (int)($countContents/$perPage)+1;
                // Fetch contents paginated
                $i = 1;
                while ($i <= $iterations) {
                    $contents = $this->er->findBy($filters, $order, $perPage, $i);
                    $i++;
                    $this->processContents($contents);
                    unset($contents);
                    gc_collect_cycles();
                    $this->output->write("$i - $iterations\n");
                }
            }
        }

        $this->output->writeln(
            "\n\nSaved contents with <info>$this->imagesCounter</info> images".
            " into '$this->targetDir'".
            "\nArticles -> <info>$this->articlesCounter</info>".
            "\nOpinions -> <info>$this->opinionsCounter</info>".
            "\nAlbums -> <info>$this->albumsCounter</info>".
            "\nVideos -> <info>$this->videosCounter</info>"
        );
    }


    /**
     * Export all articles from an instance in xml files
     *
     * @param array $contents  Contents to process.
     */
    public function processContents($contents)
    {
        foreach ($contents as $content) {
            $this->output->write(".");
            // Load category related information
            $content->category_name  = $content->loadCategoryName($content->id);
            $content->category_title = $content->loadCategoryTitle($content->id);

            $content->created_datetime =
                \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $content->created
                );
            $content->updated_datetime =
                \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $content->changed
                );

            switch ($content->content_type_name) {
                case 'album':
                    $this->albumsCounter++;
                    $photos = array();
                    $photos = $content->_getAttachedPhotos($content->id);

                    $content->all_photos = array();
                    foreach ($photos as $value) {
                        // Add DateTime with format Y-m-d H:i:s
                        $value['photo']->created_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $value['photo']->created
                            );
                        $value['photo']->updated_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $value['photo']->changed
                            );

                        $value['photo']->img_source =
                            $this->mediaPath.DS.'images'.
                            $value['photo']->path_file.
                            $value['photo']->name;


                        $content->all_photos[] = $value['photo'];

                        $isCopied = $this->copyImage(
                            $value['photo']->img_source,
                            $this->targetDir.DS.'images'.$value['photo']->path_file,
                            $value['photo']->name
                        );

                        $this->imagesCounter++;

                        if (!$isCopied) {
                            $this->imagesCounter--;
                            $this->output->writeln(
                                "\tImage <info>".$value['photo']->name.
                                "</info> from album <info>".$content->id.
                                "</info> not copied'"
                            );
                        }
                    }
                    break;

                case 'article':
                case 'opinion':
                    if ($content->content_type_name == 'article') {
                        $this->articlesCounter++;
                    } else {
                        $this->opinionsCounter++;
                    }
                    $imageId = $content->img1;
                    $imageInnerId = $content->img2;

                    if (!empty($imageId)) {
                        $image[] = $this->er->find('Photo', $imageId);
                        // Load attached and related contents from array
                        $content->loadFrontpageImageFromHydratedArray($image);
                        // Add DateTime with format Y-m-d H:i:s
                        $content->img1->created_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $content->img1->created
                            );
                        $content->img1->updated_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $content->img1->changed
                            );
                        if (!mb_check_encoding($content->img1->description)) {
                            $content->img1->description = utf8_encode($content->img1->description);
                        }
                        $content->img1_source = $this->mediaPath.DS.'images'.$content->img1_path;

                        $isCopied = $this->copyImage(
                            $content->img1_source,
                            $this->targetDir.DS.'images'.$content->img1->path_file,
                            $content->img1->name
                        );

                        $this->imagesCounter++;

                        if (!$isCopied) {
                            $this->imagesCounter--;
                            $this->output->writeln(
                                "\tImage <info>".$content->img1->name.
                                "</info> from ".$content->content_type_name." <info>".$content->id.
                                "</info> not copied'"
                            );
                        }
                    }

                    if (!empty($imageInnerId)) {
                        $image[] = $this->er->find('Photo', $imageInnerId);
                        // Load attached and related contents from array
                        $content->loadInnerImageFromHydratedArray($image);
                        // Add DateTime with format Y-m-d H:i:s
                        $content->img2->created_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $content->img2->created
                            );
                        $content->img2->updated_datetime =
                            \DateTime::createFromFormat(
                                'Y-m-d H:i:s',
                                $content->img2->changed
                            );
                        if (!mb_check_encoding($content->img2->description)) {
                            $content->img2->description = utf8_encode($content->img2->description);
                        }
                        $content->img2_source = $this->mediaPath.DS.'images'.$content->img2_path;

                        $isCopied = $this->copyImage(
                            $content->img2_source,
                            $this->targetDir.DS.'images'.$content->img2->path_file,
                            $content->img2->name
                        );

                        $this->imagesCounter++;

                        if (!$isCopied) {
                            $this->imagesCounter--;
                            $this->output->writeln(
                                "\tImage <info>".$content->img2->name.
                                "</info> from ".$content->content_type_name." <info>".$content->id.
                                "</info> not copied'"
                            );
                        }
                    }
                    break;
            }

            // Get author obj
            $ur = getService('user_repository');
            $content->author = $ur->find($content->fk_author);
            if (isset($content->author->name)) {
                $content->author = $content->author->name;
            } else {
                $content->author = 'Redacción';
            }

            // Convert content to NewsML
            if ($content->content_type_name == 'video') {
                $this->videosCounter++;
                $newsMLString = $this->convertVideoToNewsML($content);
            } else {
                $newsMLString = $this->convertToNewsML($content);
            }

            // Save xml file
            $this->storeContentFile($content, $newsMLString, $this->targetDir);
        }
    }
}
