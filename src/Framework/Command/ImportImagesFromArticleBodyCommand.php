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
                    new InputOption(
                        'instance',
                        'i',
                        InputOption::VALUE_REQUIRED,
                        'Instance to import images from',
                        '*'
                    ),
                    new InputOption(
                        'wordpress',
                        'w',
                        InputOption::VALUE_NONE,
                        'Import images from wordpress'
                    ),
                    new InputOption(
                        'joomla',
                        'j',
                        InputOption::VALUE_NONE,
                        'Import images from joomla'
                    ),
                    new InputOption(
                        'directory',
                        'd',
                        InputOption::VALUE_REQUIRED,
                        'Directory where images are'
                    ),
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
        chdir(APPLICATION_PATH);

        // Get instance name from prompt
        $instance = $input->getOption('instance');
        $isWordpress = $input->getOption('wordpress');
        $isJoomla = $input->getOption('joomla');
        $this->localDir = $input->getOption('directory');

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

        // Check if wordpress option is selected and import
        if ($isWordpress) {
            $this->importImagesWP();
        } elseif ($isJoomla) {
            $this->importImagesJoomla();
        } else {
            $this->importImages();
        }

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
    }

    /**
     * Import all images from articles
     */
    public function importImagesWP()
    {
        // Sql order, limit and filters
        $order  = array('created' => 'DESC');
        $filter = array(
            'content_type_name' => array(array('value' => 'article')),
            'body'              => array(array('value' => '%<img%', 'operator' => 'LIKE'))
        );

        // Get entity repository
        $this->er = getService('entity_repository');

        // Count total articles
        $articlesTotal = $this->er->countBy($filter);

        // Fetch articles paginated
        $perPage = 100;
        $iterations = (int)($articlesTotal/$perPage)+1;
        $i = 1;
        while ($i <= $iterations) {
            $articles = $this->er->findBy($filter, $order, $perPage, $i);
            $this->output->write(
                "Processing page $i of $iterations with ".count($articles)." articles\n"
            );

            // Process
            $this->processArticlesWP($articles);

            $i++;
            unset($articles);
            gc_collect_cycles();
        }
    }

    /**
     * Import all images from articles in Joomla
     */
    public function importImagesJoomla()
    {
        // Sql order, limit and filters
        $order  = array('created' => 'DESC');
        $filter = array(
            'content_type_name' => array(array('value' => 'article')),
            'body'              => array(array('value' => '%<img%', 'operator' => 'LIKE'))
        );

        // Get entity repository
        $this->er = getService('entity_repository');

        // Count total articles
        $articlesTotal = $this->er->countBy($filter);

        // Fetch articles paginated
        $perPage = 20;
        $iterations = (int)($articlesTotal/$perPage)+1;
        $i = 1;
        while ($i <= $iterations) {
            $articles = $this->er->findBy($filter, $order, $perPage, 1);
            $this->output->write(
                "Processing page $i of $iterations with ".count($articles)." articles\n"
            );

            // Process
            $this->processArticlesJoomla($articles);

            $i++;
            unset($articles);
            gc_collect_cycles();
        }
    }

    /**
     * Process articles to extract images from wordpress contents
     *
     * @param array $articles  Articles to process.
     */
    public function processArticlesWP($articles)
    {
        foreach ($articles as $key => $article) {
            // Get all caption images from article body
            preg_match_all(
                '@\[caption .*?\].* src="?(.*?)" alt="?(.*?)".*?\[\/caption\]@',
                $article->body,
                $result
            );

            if (empty($result[0])) {
                preg_match_all(
                    '@\[caption .*?\].* alt="?(.*?)" src="?(.*?)".*?\[\/caption\]@',
                    $article->body,
                    $result
                );

                if (empty($result[0])) {
                    preg_match_all(
                        '@<a .*?href=".+?".*?><img .*?src="?(.*?)" alt="?(.*?)".*?><\/a>@',
                        $article->body,
                        $result
                    );

                    if (empty($result[0])) {
                        preg_match_all(
                            '@<a .*?href=".+?".*?><img .*?alt="?(.*?)" src="?(.*?)".*?><\/a>@',
                            $article->body,
                            $result
                        );

                        if (!empty($result[0])) {
                            $imageSrc = $result[2][0];
                            $footer   = $result[1][0];
                        }
                    } else {
                        $imageSrc = $result[1][0];
                        $footer   = $result[2][0];
                    }
                } else {
                    $imageSrc = $result[2][0];
                    $footer   = $result[1][0];
                }
            } else {
                $imageSrc = $result[1][0];
                $footer   = $result[2][0];
            }

            // Create image in onm
            $imageId = $this->processImage(
                html_entity_decode($imageSrc),
                $article->category_name
            );

            // Replace body to eliminate [caption]
            $body = preg_replace('/\[caption .*?\].*?\[\/caption\]/', '', $article->body);
            // Replace body to eliminate <a><img></a>
            $body = preg_replace('@<a .*?href=".+?".*?><img .*?><\/a>@', '', $body);
            $body = preg_replace('@<img .*?>@', '', $body);

            $summary = \StringUtils::getNumWords(html_entity_decode($body), 20);

            // Set sql's for updating articles body
            $sql = 'UPDATE `contents` SET `body` = \''.$body.'\',
                   `description` = \''.$summary.'\'
                    WHERE `pk_content` ='.$article->id;

            $rs = $this->connection->Execute($sql);
            if ($rs == false) {
                $this->output->writeln(
                    "\tArticle ".$article->id." body not updated"
                );
            }

            // Set sql's for updating articles summary
            $sql = 'UPDATE  `articles` SET  `summary` = \''.$summary.'\'
                    WHERE  `pk_article` = '.$article->id;

            $rs = $this->connection->Execute($sql);
            if ($rs == false) {
                $this->output->writeln(
                    "\tArticle ".$article->id." summary not updated"
                );
            }

            // Set image to article and update
            if ($imageId !== false) {
                // Set sql's for updating articles images
                $sql = 'UPDATE  `articles` SET  `img1` = '.$imageId.',
                        `img1_footer` = \''.$footer.'\',
                        `summary` = \''.$summary.'\',
                        `img2` = '.$imageId.',
                        `img2_footer` = \''.$footer.'\' WHERE  `pk_article` = '.$article->id;

                $rs = $this->connection->Execute($sql);
                if ($rs == false) {
                    $this->output->writeln(
                        "\tArticle ".$article->id.
                        " not updated with image ".$imageId
                    );
                }
            }

            unset($body);
            unset($sql);
            unset($result);
            unset($imageSrc);
            unset($footer);
            gc_collect_cycles();
            $this->output->writeln("\tArticle ".($key+1)." of ".count($articles)." processed");
        }
    }

    /**
     * Process articles to extract images
     *
     * @param array $articles  Articles to process.
     * @param string $field  Field to process (body or summary).
     */
    public function processArticles($articles, $field)
    {
        foreach ($articles as $article) {
            // Get images src from article
            $dom = new \domDocument;
            @$dom->loadHTML($article->{$field}, LIBXML_NOBLANKS | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $dom->preserveWhiteSpace = false;
            // Get new text without image
            $images = $dom->getElementsByTagName('img');
            foreach ($images as $image) {
                $imageSrc = $image->getAttribute('src');
                // Do not process some images
                if (preg_match("/(c:|C:|file:|oir|oir2)/", $imageSrc) != 1) {
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
     * Process articles from joomla to extract images
     *
     * @param array $articles  Articles to process.
     * @param string $field  Field to process.
     */
    public function processArticlesJoomla($articles)
    {
        foreach ($articles as $key => $article) {
            // Regular expresion to extract image src and caption
            $regex = '@<span.*src=["|\'](.+?)["|\'].*\/>.*">[<strong>]*(.+?)'.
                     '<\/s[pan|trong]*><\/s[pan|trong]*>[<\/span>]*@';
            preg_match(
                $regex,
                $article->body,
                $result
            );

            if (!array_key_exists(0, $result)) {
                // Regular expresion to extract only image src
                $regex = '@<img.*src=["|\'](.+?)["|\'].*\/>@';
                preg_match($regex, $article->body, $result);
                // Get image src
                $imgSource = $result[1];
                $footer = '';
                // Replace body to remove image code
                $body = preg_replace($regex, '', $article->body);
            } else {
                // Get image src and footer
                $imgSource = $result[1];
                $footer = $result[2];
                // Replace body to remove image code
                $body = preg_replace($regex, '', $article->body);
            }

            // Escape blank spaces
            $imgSource = str_replace("%20", " ", $imgSource);
            // Create image in onm
            $imageId = $this->processImage(
                html_entity_decode($imgSource),
                $article->category_name,
                $this->localDir
            );

            // Update articles body
            $sql = 'UPDATE `contents` SET `body` = ? WHERE `pk_content` = ?';

            $rs = $this->connection->Execute($sql, [$body, $article->id]);
            if ($rs == false) {
                $this->output->writeln(
                    "\tArticle ".$article->id." body not updated"
                );
            }

            // Set image to article and update
            if ($imageId !== false) {
                // Set sql's for updating articles images
                $sql = 'UPDATE  `articles` SET  `img1` = '.$imageId.',
                        `img1_footer` = \''.$footer.'\',
                        `img2` = '.$imageId.',
                        `img2_footer` = \''.$footer.'\' WHERE  `pk_article` = '.
                        $article->id;

                $rs = $this->connection->Execute($sql);
                if ($rs == false) {
                    $this->output->writeln(
                        "\tArticle ".$article->id.
                        " not updated with image ".$imageId
                    );
                }
            }

            unset($body);
            unset($sql);
            unset($result);
            unset($regex);
            unset($imageSrc);
            unset($footer);
            gc_collect_cycles();
            $this->output->writeln("\tArticle ".($key+1)." of ".count($articles)." processed");
        }
    }

    /**
     * Process image and store it in opennemas instance
     *
     * @param Image $image  Image to process.
     * @param string $category  Category for this image.
     */
    public function processImage($image, $category, $localDir = false)
    {
        $dir = '/tmp/import_images/';

        // Create directory
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if ($localDir) {
            // Set localFile path
            $localfile = $localDir.'/'.$image;
            // Check if image exists
            if (!file_exists($localfile)) {
                $this->output->writeln("\tImage ".$image." not exists");
                return false;
            }
            // Get file info
            $filePath = pathinfo($localfile);
        } else {
            // Get file info
            $filePath = pathinfo($image);
            // Save image in local from url
            $isCopied = @copy($image, $dir.$filePath['basename']);
            if (!$isCopied) {
                $this->output->writeln("\tImage ".$image." not copied");
                return false;
            }
            // Set localFile path
            $localFile = $dir.$filePath['basename'];
        }

        // Building information for the photo image
        $data = array(
            'title'             => $filePath['filename'],
            'description'       => '',
            'local_file'        => $localfile,
            'fk_category'       => $category,
            'category_name'     => $category,
            'category'          => $category,
            'metadata'          => \Onm\StringUtils::getTags($filePath['filename']),
            'author_name'       => '&copy; '.INSTANCE_UNIQUE_NAME.' '.date('Y'),
            'original_filename' => $filePath['basename'],
        );

        $photo = new \Photo();
        $photoId = $photo->createFromLocalFile($data);

        return $photoId;
    }
}
