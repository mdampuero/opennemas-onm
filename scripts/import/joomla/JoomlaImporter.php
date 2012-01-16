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
        $action = preg_replace_callback('/-([a-zA-Z])/', function ($match) {
            return ucfirst($match[1]);
        }, $action);

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

        return self::$configuration['categories'][$category];

    }

    public function initOriginalDatabaseConnection($config = array())
    {

        echo "Initialicing source database connection...".PHP_EOL;
        if (isset($config['host'])
            && isset($config['database'])
            && isset($config['user'])
            && isset($config['password'])
            && isset($config['type']))
        {

            self::$originalDatabaseConn = ADONewConnection($config['type']);
            self::$originalDatabaseConn->PConnect(
                $config['host'], $config['user'],
                $config['password'], $config['database']
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
     * Checks if the content with $contentID is imported.
     *
     **/
    public function isContentIdImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);

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
        if(isset($contentID) && isset($date)) {
            $sql = 'UPDATE `contents` SET `created`=?, `changed`=? WHERE pk_content=?';

            $values = array($date, $date, $contentID);
            $date_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($date_update_sql,
                                                          $values);
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
        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);

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

        $originalContents = self::$originalDatabaseConn->Execute(
            "SELECT * FROM jos_content WHERE catid IN (".implode(",", $categories).")"
        );
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

                $values = array(
                    'title' => ImportHelper::convertoUTF8($originalContents->fields['title']),
                    'category' => self::matchCategory($originalContents->fields['catid']),
                    'with_comment' => 1,
                    'content_status' => 1,
                    'frontpage' => 0,
                    'in_home' => 0,
                    'title_int' => ImportHelper::convertoUTF8($originalContents->fields['title']),
                    'metadata' => String_Utils::get_tags(ImportHelper::convertoUTF8($originalContents->fields['title'])),
                    'subtitle' => '',
                    'agency' => 'Redaccion',
                    'summary' => ImportHelper::convertoUTF8($originalContents->fields['introtext']),
                    'body' => ImportHelper::convertoUTF8($originalContents->fields['introtext']. ' '.$originalContents->fields['fulltext']),
                    'posic' => 0,
                    'id' => 0,
                    'fk_user' => 125,
                    'fk_publisher' => 125
                );


                $article = new Article();
                $newArticleID = $article->create($values);

                if (is_string($newArticleID)) {
                    ImportHelper::logElementInsert($originalArticleID, $newArticleID, 'article');
                    ImportHelper::updateCreateDate($newArticleID, $originalContents->fields['created']);
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





} // END class JoomlaImporter