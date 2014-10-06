<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Handles all the importation operations
 *
 * @package Onm
 * @author
 **/
class JoomlaImporter
{
    const CONF_FILE = 'configuration.ini';

    public static $configuration = '';

    public static $originalDatabaseConn = '';

    public static $reflector;

    /**
     * Implements the frontController pattern
     *
     * @return boolean
     **/
    public static function executeAction($action, $args)
    {

        self::loadConfiguration();
        self::initOriginalDatabaseConnection(self::$configuration['original-database']);
        self::initTargetDatabaseConnection(self::$configuration['target-database']);

        self::$reflector = new \ReflectionClass('JoomlaImporter');
        // $action = strtolower(str_replace('-','',$action));
        $action = preg_replace_callback(
            '/-([a-zA-Z])/',
            function ($match) {
                return ucfirst($match[1]);
            },
            $action
        );

        if (method_exists('JoomlaImporter', $action)) {
            return self::$action($args);
        } else {
            throw new \InvalidArgumentException('Action with name '.$action.' doesn\'t exists.');
        }
    }


    /**
     * Parses the configuration file and loads it
     *
     * @return void
     **/
    public static function loadConfiguration()
    {
        echo "Loading script configuration...".PHP_EOL;
        $configurationFile = realpath(__DIR__.'/'.self::CONF_FILE);
        if (!is_file($configurationFile)) {
            echo "Configuration file 'configuration.ini' doesn't exists".PHP_EOL;
            die();
        }
        self::$configuration = parse_ini_file($configurationFile, true);

        defined('MEDIA_PATH')
            || define('MEDIA_PATH', SITE_PATH. 'media');

        defined('IMG_DIR')
            || define('IMG_DIR', self::$configuration['media']['instance-name'].'images');


    }

    public function initOriginalDatabaseConnection($config = array())
    {

        echo "Initialicing source database connection...".PHP_EOL;
        if (
            isset($config['host'])
            && isset($config['database'])
            && isset($config['user'])
            && isset($config['password'])
            && isset($config['type'])
        ) {
            self::$originalDatabaseConn = ADONewConnection($config['type']);
            self::$originalDatabaseConn->PConnect(
                $config['host'],
                $config['user'],
                $config['password'],
                $config['database']
            );

        } else {

            echo "ERROR: You must provide the connection configuration to the Joomla database";
            die();
        }

    }

    public function initTargetDatabaseConnection($config)
    {
        echo "Initialicing target database connection...".PHP_EOL;
        define('BD_HOST', $config['host']);
        define('BD_USER', $config['user']);
        define('BD_PASS', $config['password']);
        define('BD_TYPE', $config['type']);
        define('BD_DATABASE', $config['database']);

        $GLOBALS['application'] = new Application();
        Application::initDatabase();
        Application::initLogger();
    }

    /**
     * Fetches the original categories
     *
     **/
    public function getOriginalCategories()
    {
        return array_keys(self::$configuration['categories']);
    }

    /**
     * Fetches the target categories
     *
     **/
    public function getTargetCategories()
    {
        return array_values(self::$configuration['categories']);
    }


    public function matchCategory($category)
    {
        if (array_key_exists($category, self::$configuration['categories'])) {
            ImportHelper::log("{$category} = ".self::$configuration['categories'][$category]."  - ");
            return self::$configuration['categories'][$category];
        } else {
            ImportHelper::log("- NOT {$category} =>".self::$configuration['categories'][0]."  \n");
            return self::$configuration['categories'][0];
        }

    }

    /**
     * Show categories.
     *
     **/
    public function showCategories()
    {
        require 'Console/ProgressBar.php';
        require 'ImportHelper.php';

        // Retrieve original articles
        echo "\tGetting original categories...".PHP_EOL;
        $categories = self::$configuration['categories'];
        foreach ($categories as $key => $value) {
            echo "{$key} => {$value} \n";
        }
        echo "dump some infornation \n";
        echo self::$configuration['media']['old-media']."\n";
        echo self::$configuration['media']['instance-name']."\n";
        echo self::$configuration['data']['userId']."\n";
        echo self::$configuration['data']['agency']."\n";

    }


     /**
     * Show categories.
     *
     **/
    public function clearDatabase()
    {

        ImportHelper::sqlClearData();

        return true;

    }
    /**
     * Checks if the content with $contentID is imported.
     *
     **/
    public function isContentIdImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute(
                $views_update_sql,
                $values
            );

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->_numOfRows >= 1);
            }

        } else {
            echo "Please provide a contentID and views to update it.";
        }
    }

    public function updateCreateDate($contentID, $date)
    {
        if (isset($contentID) && isset($date)) {
            $sql = 'UPDATE `contents` SET `created`=?, `changed`=? WHERE pk_content=?';

            $values = array($date, $date, $contentID);
            $date_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute(
                $date_update_sql,
                $values
            );
            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            }

        } else {
            echo "Please provide a contentID and views to update it.";
        }
    }



    public function logElementInsert($original, $final, $type)
    {
        $sql_translation_request =
                'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($original, $final, $type);
        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);
        $rss = $GLOBALS['application']->conn->Execute(
            $translation_ids_request,
            $translation_values
        );

        if (!$rss) {
            echo $GLOBALS['application']->conn->ErrorMsg();
        }
    }


    /**
     * Generates a bunch of error files for one modules section
     *
     * @return boolean
     * @author
     **/
    public static function importArticles($args)
    {

        require 'Console/ProgressBar.php';
        require 'ImportHelper.php';

        echo PHP_EOL.'Importing articles from joomla'.PHP_EOL;

        // Retrieve original articles
        echo "\tGetting original articles...".PHP_EOL;
        $categories = self::getOriginalCategories();

        $sql = "SELECT * FROM jos_content WHERE catid IN (".implode(",", $categories).")";

        if (!empty($args->limit)) {
            $sql = "SELECT * FROM jos_content  LIMIT 0, ".((int)$args->limit);
        }

        $originalContents = self::$originalDatabaseConn->Execute($sql);

        if (!$originalContents) {
            echo self::$originalDatabaseConn->ErrorMsg();
        }

        echo "\tStarting to import articles".PHP_EOL;

        $format = "\t[%bar%] %percent% (%current%/%max%) ";
        $filler = '=>';
        $empty  = ' ';
        $width  = 70;
        $size   = $originalContents->_numOfRows;
        $bar = new Console_ProgressBar($format, $filler, $empty, $width, $size);


        $maxElement = ((int)$args->max);

        $current = $imported = 0;

        while (!$originalContents->EOF) {

            if (isset($args->max) && $imported >= $args->max) {
                $bar->update($size);
                echo PHP_EOL."Max of elements imported reached".PHP_EOL;
                break;
            }

            if (ImportHelper::elementIsImported($originalContents->fields['id'], 'article') ) {
                ImportHelper::log('Original content with id '.$originalContents->fields['id']." already imported.\n");
            } else {

                $originalArticleID = $originalContents->fields['id'];
                $data = array();

                if (self::$configuration['ImageMethod'] == 'inText') {
                    $data['img1'] = $data['img2'] = '';
                    $data['video'] = $data['video2'] = '';
                    list($data['introtext'], $data['fulltext'])
                    = self::importImageFromText($originalContents);
                } else {
                    $data = self::importMediaContents($originalContents->fields);
                }

                $category = self::matchCategory($originalContents->fields['catid']);
                $values = array(
                    'title' => ImportHelper::convertoUTF8($originalContents->fields['title']),
                    'category' => $category,
                    'with_comment' => 1,
                    'content_status' => ($originalContents->fields['state'] == 1)?1:0,
                    'available' => ($originalContents->fields['state'] == 1)?1:0,
                    'frontpage' => 0,
                    'in_home' => 0,
                    'title_int' => ImportHelper::convertoUTF8($originalContents->fields['title']),
                    'metadata' => StringUtils::get_tags(ImportHelper::convertoUTF8($originalContents->fields['title'])),
                    'subtitle' => '',
                    'img1' => $data['img1'],
                    'img2' => $data['img2'],
                    'fk_video' => $data['fk_video'],
                    'fk_video2' => $data['fk_video2'],
                    'summary' => ImportHelper::convertoUTF8($data['introtext']),
                    'body' => ImportHelper::convertoUTF8($data['fulltext']),
                    'created' => $originalContents->fields['created'],
                    'starttime' => $originalContents->fields['publish_up'],
                    'endtime' => $originalContents->fields['publish_down'],
                    'changed' => $originalContents->fields['modified'],
                    'agency' => self::$configuration['data']['agency'],
                    'id' => 0,
                    'fk_user' => self::$configuration['data']['userId'],
                    'fk_publisher' => self::$configuration['data']['userId'],
                    'description' => substr(ImportHelper::convertoUTF8($data['introtext']), 0, 120),
                    'slug' => \StringUtils::getTitle(ImportHelper::convertoUTF8($rs->fields['title'])),
                );



                $article = new Article();
                $newArticleID = $article->create($values);

                if (is_string($newArticleID)) {
                    ImportHelper::logElementInsert($originalArticleID, $newArticleID, 'article');
                    // ImportHelper::updateCreateDate($newArticleID, $originalContents->fields['created']);
                    ImportHelper::log('Content with ID '.$originalContents->fields['id']." imported.\n");
                    $imported++;
                }
            }

            $bar->update($current);
            $current++;

            $originalContents->MoveNext();
        }

        $originalContents->Close(); # optional

    }


    public static function importImageFromText($originalContents)
    {
        preg_match_all('/(<img .*?>)/', $originalContents->fields['introtext'], $summaryResult);
        preg_match_all('/(<img .*?>)/', $originalContents->fields['fulltext'], $bodyResult);

        foreach ($bodyResult[0] as $res) {
            ImportHelper::importImages($originalContents->fields, $res);
        }
        foreach ($summaryResult[0] as $res) {
            ImportHelper::importImages($originalContents->fields, $res);
        }

        // Change images url on introtext
        $originalContents->fields['introtext'] = preg_replace(
            '/(<img .*?>)/',
            '',
            $originalContents->fields['introtext']
        );
        // Change images url on fulltext
        $originalContents->fields['fulltext'] = preg_replace(
            '/src="images/',
            'src="'.self::$configuration['media']['actual-directory'],
            $originalContents->fields['fulltext']
        );

        return array($originalContents->fields['introtext'], $originalContents->fields['fulltext'] );
    }


    public static function importMediaContents($originalData)
    {
        $data = array();
        if (!empty($originalData['images'])) {
            /* example values
            salidateenrumi.jpg|left||0||bottom||
            salidateennnn.jpg|left||0||bottom||
            */

            preg_match_all("/(.*).(png|jpg|gif|jpeg)(.*)||/i", $originalData['images'], $matches, PREG_SET_ORDER);


            if (!empty($matches[0][1])) {
                $data['img1'] = ImportHelper::importImage($originalData, $matches[0][1].".".$matches[0][2]);
            }
            if (!empty($matches[1][1])) {
                $data['img2'] = ImportHelper::importImage($originalData, $matches[1][1].".".$matches[1][2]);
            }
        }
        //videos {youtube}npj0K0IFv0Y{/youtube}
        $youtube = "http://www.youtube.com/watch?v=";
        $originalData['origin'] = 'youtube';
        preg_match_all("/{youtube}(.*){\/youtube}/i", $originalData['introtext'], $matches, PREG_SET_ORDER);

        if (!empty($matches[0][1])) {
            $data['fk_video'] = ImportHelper::importVideo($originalData, $youtube.$matches[0][1]);
        }

        preg_match_all("/{youtube}(.*){\/youtube}/i", $originalData['fulltext'], $matches);
        if (!empty($matches[0][1])) {
            $data['fk_video2'] = ImportHelper::importVideo($originalData, $youtube.$matches[0][1]);
        }

        $data['introtext'] = preg_replace(
            array('/{mosimage}/', '/{youtube}(.*){\/youtube}/'),
            array(" ", ""),
            $originalData['introtext']
        );
        // Change images url on fulltext
        $data['fulltext'] = preg_replace(
            array('/{mosimage}/', '/{youtube}(.*){\/youtube}/'),
            array(" ", ""),
            $originalData['fulltext']
        );

        return $data;
    }



    /**
     * Generates a bunch of error files for one modules section
     *
     * @return boolean
     * @author
     **/
    public static function updateVideo($args)
    {

        require 'Console/ProgressBar.php';
        require 'ImportHelper.php';

        echo PHP_EOL.'Updating fk_video in articles from joomla'.PHP_EOL;

        // Retrieve original articles
        echo "\tGetting original videos...".PHP_EOL;
        $categories = self::getOriginalCategories();

        $sql = "SELECT * FROM jos_content WHERE catid IN (".implode(",", $categories).")";

        $originalContents = self::$originalDatabaseConn->Execute($sql);

        if (!$originalContents) {
            echo self::$originalDatabaseConn->ErrorMsg();
        }

        echo "\tStarting to import videos".PHP_EOL;

        $format = "\t[%bar%] %percent% (%current%/%max%) ";
        $filler = '=>';
        $empty  = ' ';
        $width  = 70;
        $size   = $originalContents->_numOfRows;
        $bar = new Console_ProgressBar($format, $filler, $empty, $width, $size);



        $current = $imported = 0;

        while (!$originalContents->EOF) {

            $newArticleID = ImportHelper::elementTranslated($originalContents->fields['id'], 'article');
            if (!empty($newArticleID)) {

                $data = array();

                $youtube = "http://www.youtube.com/watch?v=";
                $originalData = $originalContents->fields;
                $originalData['origin'] = 'youtube';
                preg_match_all(
                    "/{youtube}(.*){\/youtube}/i",
                    $originalContents->fields['introtext'],
                    $matches,
                    PREG_SET_ORDER
                );
                $data['fk_video'] =0;
                if (!empty($matches[0][1])) {
                    $data['fk_video'] = ImportHelper::searchVideo($youtube.$matches[0][1]);
                    if (empty($data['fk_video'])) {
                        $data['fk_video'] = ImportHelper::importVideo($originalData, $youtube.$matches[0][1]);
                    }
                }

                $data['fk_video2'] =0;
                preg_match_all("/{youtube}(.*){\/youtube}/i", $originalContents->fields['fulltext'], $matches);
                if (!empty($matches[0][1])) {
                    $data['fk_video2'] = ImportHelper::searchVideo($youtube.$matches[0][1]);
                    if (empty($data['fk_video2'])) {
                        $data['fk_video2'] = ImportHelper::importVideo($originalData, $youtube.$matches[0][1]);
                    }
                }


                if (!empty($newArticleID) && (!empty($data['fk_video']) || !empty($data['fk_video2']))) {
                      ImportHelper::updateVideoElements($newArticleID, $data);

                }
            }

            $bar->update($current);
            $current++;

            $originalContents->MoveNext();
        }

        $originalContents->Close(); # optional

    }
}

