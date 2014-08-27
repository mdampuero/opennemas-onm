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

class ImportImagesFromArticleBodyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setDefinition(
                array(
                    new InputOption('instance', 'i', InputOption::VALUE_REQUIRED, 'Instance to import images from', '*'),
                )
            )
            ->setName('import:images')
            ->setDescription('Executes images import from article body action')
            ->setHelp(
                <<<EOF
The <info>import:images</info> executes images import action from articles.

<info>php app/console import:images instance</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $phpBinPath = exec('which php');
        chdir(APPLICATION_PATH);

        // Get instance name from prompt
        $instance = $input->getOption('instance');

        // Set input/output interface
        $this->input = $input;
        $this->output = $output;

        // Get instances
        $dbConn = $this->getContainer()->get('db_conn_manager');

        $rs = $dbConn->GetAll('SELECT internal_name, settings FROM instances');

        $instances = array();
        foreach ($rs as $database) {
            $instances[$database['internal_name']] =
                unserialize($database['settings']);
        }

        $instanceNames = array_keys($instances);

        // Validate instance name
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
                'From what instance do you want to import images from articles body ('
                .implode(', ', $instanceNames)
                .'): ',
                $validator,
                5,
                true
            );
        } elseif (!in_array($instance, $instanceNames)) {
            throw new \Exception('Instance name not valid');
        }

        // Initialize internal constants for instance
        define('CACHE_PREFIX', '');
        define('INSTANCE_UNIQUE_NAME', $instance);
        define('TEMPLATE_USER', $instances[$instance]['TEMPLATE_USER']);
        define('TEMPLATE_USER_PATH', SITE_PATH.DS."themes".DS.TEMPLATE_USER.DS);
        $cachepath = APPLICATION_PATH . DS . 'tmp' . DS . 'instances' . DS . $instance;
        if (!file_exists($cachepath)) {
            mkdir($cachepath, 0755, true);
        }
        define('CACHE_PATH', realpath($cachepath));

        // Initialize database connection
        $this->connection = $this->getContainer()->get('db_conn');
        $this->connection->selectDatabase($instances[$instance]['BD_DATABASE']);
        $conn = $this->getContainer()->get('dbal_connection');
        $conn->selectDatabase($instances[$instance]['BD_DATABASE']);

        // Initialize application
        $GLOBALS['application'] = new \Application();
        \Application::load();
        \Application::initDatabase($this->connection);

        // Initializa media path and session
        define('MEDIA_PATH', SITE_PATH."media".DS.$instance);
        $_SESSION['username'] = 'console';
        $_SESSION['userid'] = '0';

        // Initialize script
        $output->writeln("\tStart importing images from articles");

        // Import
        $this->importImages();

        // Finish script
        $output->writeln("\n\tFinished importing");
    }

    /**
     * Import all images from articles
     */
    public function importImages()
    {
        // Sql order, limit and filters
        $order   = array('created' => 'DESC');
        $filters = array(
            array(
                'content_type_name' => array(array('value' => 'article')),
                'body'              => array(array('value' => '%<img%', 'operator' => 'LIKE'))
            ),
            array(
                'tables'            => array('articles'),
                'pk_content'        => array(array('value' => 'pk_article', 'field' => true)),
                'content_type_name' => array(array('value' => 'article')),
                'summary'           => array(array('value' => '%<img%', 'operator' => 'LIKE'))
            ),
        );

        // Get entity repository
        $this->er = getService('entity_repository');

        foreach ($filters as $filter) {
            // Count total articles
            $articlesTotal = $this->er->countBy($filter);

            // Fetch articles paginated
            $perPage = 100;
            $iterations = (int)($articlesTotal/$perPage)+1;
            $i = 1;
            while ($i <= $iterations) {
                $articles = $this->er->findBy($filter, $order, $perPage, $i);
                $this->output->write(
                    "Processing page $i of $iterations with "
                    .count($articles)." articles\n"
                );
                // Process article body/summary
                if (array_key_exists('body', $filter)) {
                    $this->processArticles($articles, 'body');
                } elseif (array_key_exists('summary', $filter)) {
                    $this->processArticles($articles, 'summary');
                }
                $i++;
                unset($articles);
                gc_collect_cycles();
            }
        }

        $this->output->writeln();
    }


    /**
     * Process articles to extract images
     *
     * @param array $articles  Articles to process.
     * @param string $field  Field to process (body or summary).
     */
    public function processArticles($articles, $field)
    {
        foreach ($articles as $key => $article) {
            // Get images src from article
            $dom = new \domDocument;
            @$dom->loadHTML($article->{$field}, LIBXML_NOBLANKS | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $dom->preserveWhiteSpace = false;
            // Get new text without image
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image) {
                $imageSrc = $image->getAttribute('src');
                // Do not process some images
                if (preg_match("/(c:|C:|file:|oir|oir2)/", $imageSrc, $matches) != 1) {
                    // Create image in onm
                    $imageId = $this->processImage(
                        $imageSrc,
                        $article->category_name
                    );

                    // Remove img tag from dom
                    $image->parentNode->removeChild($image);
                    $finalText = $dom->saveHTML();

                    // Set sql's for updating articles text
                    if ($field == 'body') {
                        $sqlText = 'UPDATE `contents` SET `body` = \''.$finalText.
                                   '\' WHERE `pk_content` ='.$article->id;
                    } else {
                        $sqlText = 'UPDATE `articles` SET `summary` = \''.$finalText.
                                   '\' WHERE `pk_article` ='.$article->id;
                    }

                    $rsText = $this->connection->Execute($sqlText);
                    if ($rsText == false) {
                        $this->output->writeln(
                            "\tArticle ".$article->id.
                            " ".$field." not updated"
                        );
                    }

                    // Set image to article and update body/summary
                    if ($imageId !== false) {
                        // Set sql's for updating articles images
                        if ($field == 'body') {
                            $sql = 'UPDATE  `articles` SET  `img2` =  '.$imageId.
                                   ' WHERE  `pk_article` ='.$article->id;
                        } else {
                            $sql = 'UPDATE  `articles` SET  `img1` =  '.$imageId.
                                   ' WHERE  `pk_article` ='.$article->id;
                        }

                        $rs = $this->connection->Execute($sql);
                        if ($rs == false) {
                            $this->output->writeln(
                                "\tArticle ".$article->id.
                                " not updated with image ".$imageId
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Process image and store it in opennemas instance
     *
     * @param Image $image  Image to process.
     * @param string $category  Category for this image.
     */
    public function processImage($image, $category)
    {
        $dir = '/tmp/import_images/';

        // Create directory
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Get file info
        $filePath = pathinfo($image);

        // Save image in local from url
        $isCopied = @copy($image, $dir.$filePath['basename']);
        if (!$isCopied) {
            $this->output->writeln("\tImage ".$image." not copied");
            return false;
        }

        // Building information for the photo image
        $data = array(
            'title'             => $filePath['filename'],
            'description'       => '',
            'local_file'        => $dir.$filePath['basename'],
            'fk_category'       => $category,
            'category_name'     => $category,
            'category'          => $category,
            'metadata'          => \Onm\StringUtils::get_tags($filePath['filename']),
            'author_name'       => '&copy; '.INSTANCE_UNIQUE_NAME.' '.date('Y'),
            'original_filename' => $filePath['basename'],
        );

        $photo = new \Photo();
        $photoId = $photo->createFromLocalFile($data);

        return $photoId;
    }
}
