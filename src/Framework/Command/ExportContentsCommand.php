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
        $limit = $input->getOption('limit');
        $instance = $input->getOption('instance');
        $targetDir = $input->getOption('target-dir');

        $this->input = $input;
        $this->output = $output;

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

        $conn = $this->getContainer()->get('dbal_connection');
        $conn->selectDatabase($instances[$instance]['BD_DATABASE']);

        $this->mediaPath = APPLICATION_PATH.DS.'public'.DS.'media'.DS.$instance;

        $this->exportContents($targetDir, $limit);
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
     * @param string $targetDir  Path to export.
     * @param string $limit      Limit number of articles.
     */
    public function exportContents($targetDir, $limit)
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
        $er = getService('entity_repository');
        // Count contents
        $countContents = $er->countBy($filters);

        // Get total iterations to fetch contents
        $perPage = 100;
        $iterations = (int)($countContents/$perPage)+1;

        // Fetch contents
        if ($limit != '*') {
            $contents = $er->findBy($filters, $order, $limit, 1);
        } else {
            // Fetch contents paginated
            $i = 1;
            $totalContents = array();
            while ($i <= $iterations) {
                $contents = $er->findBy($filters, $order, $perPage, $i);
                $totalContents = array_merge($contents, $totalContents);
                $i++;
                $this->output->write(".");
            }
        }

        $imagesCounter = 0;
        $articlesCounter = 0;
        $opinionsCounter = 0;
        $albumsCounter = 0;
        $videosCounter = 0;
        foreach ($totalContents as $content) {
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

            // Check for album contents and get all photos
            if ($content->content_type_name == 'album') {
                $albumsCounter++;
                $photos = array();
                $photos = $content->_getAttachedPhotos($content->id);

                $content->all_photos = array();
                foreach ($photos as $key => $value) {
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
                        $targetDir.DS.'images'.$value['photo']->path_file,
                        $value['photo']->name
                    );

                    $imagesCounter++;

                    if (!$isCopied) {
                        $imagesCounter--;
                        $this->output->writeln(
                            "\tImage <info>".$content->img1->id.
                            "</info> from article <info>".$content->id.
                            "</info> not copied'"
                        );
                    }
                }
            } elseif ($content->content_type_name == 'article' ||
                      $content->content_type_name == 'opinion'
            ) {
                if ($content->content_type_name == 'article') {
                    $articlesCounter++;
                } else {
                    $opinionsCounter++;
                }
                $imageId = $content->img1;
                $imageInnerId = $content->img2;

                if (!empty($imageId)) {
                    $image[] = $er->find('Photo', $imageId);
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
                        $targetDir.DS.'images'.$content->img1->path_file,
                        $content->img1->name
                    );

                    $imagesCounter++;

                    if (!$isCopied) {
                        $imagesCounter--;
                        $this->output->writeln(
                            "\tImage <info>".$content->img1->id.
                            "</info> from article <info>".$content->id.
                            "</info> not copied'"
                        );
                    }
                }

                if (!empty($imageInnerId)) {
                    $image[] = $er->find('Photo', $imageInnerId);
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
                        $targetDir.DS.'images'.$content->img2->path_file,
                        $content->img2->name
                    );

                    $imagesCounter++;

                    if (!$isCopied) {
                        $imagesCounter--;
                        $this->output->writeln(
                            "\tImage <info>".$content->img2->id.
                            "</info> from article <info>".$content->id.
                            "</info> not copied'"
                        );
                    }
                }
            }

            // Get author obj
            $ur = getService('user_repository');
            $content->author = $ur->find($content->fk_author);

            $authorPhoto = '';
            if (isset($content->author->avatar_img_id) &&
                !empty($content->author->avatar_img_id)
            ) {
                // Get author photo
                $authorPhoto = $er->find('Photo', $content->author->avatar_img_id);
                if (is_object($authorPhoto) && !empty($authorPhoto)) {
                    $content->author->photo = $authorPhoto;
                }
            }

            // Encode author in json format
            $content->author = json_encode($content->author);

            if ($content->content_type_name == 'video') {
                $videosCounter++;
                $newsMLString = $this->convertVideoToNewsML($content);
            } else {
                $newsMLString = $this->convertToNewsML($content);
            }
            $this->storeContentFile($content, $newsMLString, $targetDir);
        }

        $this->output->writeln(
            "\n\nSaved <info>".count($totalContents)."</info>".
            " contents with <info>$imagesCounter</info> images".
            " into '$targetDir'".
            "\nArticles -> <info>$articlesCounter</info>".
            "\nOpinions -> <info>$opinionsCounter</info>".
            "\nAlbums -> <info>$albumsCounter</info>".
            "\nVideos -> <info>$videosCounter</info>"
        );
    }
}
