<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MediaImporter
{

    public static $originConn = '';

    public $categoriesMatches = array();

    public $categoriesData = array();

    public $logFile = "";

    public function __construct($configNewDB = array(), $logName = 'log.txt')
    {

        $this->logFile = __DIR__."/../".$logName;

        echo "Initialicing Onm database connection...".PHP_EOL;
        define('BD_HOST', $configNewDB['host']);
        define('BD_USER', $configNewDB['user']);
        define('BD_PASS', $configNewDB['password']);
        define('BD_TYPE', $configNewDB['type']);
        define('BD_DATABASE', $configNewDB['database']);

        $GLOBALS['application'] = new Application();

        Application::initDatabase();
    }


    /**
     * Explanation for this function.
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function matchInternalCategory($category)
    {
        $category = $this->convertToUtf8($category);
        $category = \Onm\StringUtils::setSeparator(strtolower($category), '-');
        $key = $this->categoriesData[$category];

        if (empty($key)) {
            $this->log(" Category not found: {$category} \n ");
            return 20;
        }
        return $key;

    }

    /**
     * Extract category data of old db and insert these in new DB.
     * Drop example categories in new DB.
     *
     * @param array $categories categories ids of used contents
     *
     * @return bool
     */

    public function loadCategories()
    {

        $categories = ContentCategoryManager::get_instance()->categories;

        foreach ($categories as $key => $value) {
            $this->categoriesData[$value->name] = $value->pk_content_category;
        }

        return false;

    }


    public function listDir()
    {

        $directory = OLD_MEDIA;
        var_dump(OLD_MEDIA);

        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

        while ($it->valid()) {

            if (!$it->isDot()) {

                echo 'SubPathName: ' . $it->getSubPathName() . "\n";
                echo 'SubPath:     ' . $it->getSubPath() . "\n";
                echo 'Key:         ' . $it->key() . "\n\n";
            }

            $it->next();
        }
    }
    /* create new image */


    public function getImages()
    {

        $directory = OLD_MEDIA;

        $dir = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS);

        // Flatten the recursive iterator, folders come before their files
        $it  = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::SELF_FIRST);

        // Maximum depth is 1 level deeper than the base folder
        $it->setMaxDepth(1);

        // Basic loop displaying different messages based on file or folder
        $data = array();
        foreach ($it as $fileinfo) {
            if ($fileinfo->isDir()) {
                printf("Folder - %s\n", $fileinfo->getFilename());
            } elseif ($fileinfo->isFile()) {
                $category = $it->getSubPath();
                printf("File From %s - %s\n", $category, $fileinfo->getFilename());
                $data['name'] = $fileinfo->getFilename();
                $data['category_name'] = $category;
                $data['category'] = $this->matchInternalCategory($category);
                $data['metadata'] = $category_name.", ".$data['name'];
                $data['created']  =  date("Y-m-d H:i:s");
                $this->createImage($data);

            }
        }
    }

    public function createImage($data)
    {

        $oldPath = OLD_MEDIA.$data['category_name'].'/'.$data['name'] ;
        var_dump($oldPath);
        $values = array(
                'title' => $this->convertToUtf8($data['name']),
                'category' => $data['category'],
                'fk_category' => $data['category'],
                'category_name'=> $data['category_name'],
                'content_status' => 1,
                'frontpage' => 0,
                'in_home' => 0,
                'metadata' => $data['metadata'],
                'description' => $data['metadata'],
                'id' => 0,
                'created' => $data['created'],
                'starttime' => $data['created'],
                'changed' => $data['created'],
                'fk_user' => USER_ID,
                'fk_author' => USER_ID,
                'local_file' => $oldPath,
            );

        $image = new Photo();
        if (is_null($dateForDirectory)) {
            $dateForDirectory = date("/Y/m/d/");
        }
        $newimageID = null;
        try {
            $newimageID = $image->createFromLocalFile($values, $dateForDirectory);
        } catch (Exception $e) {
            $this->insertfailImport('image', $oldPath);
            $this->log(" Problem with image: {$e->getMessage()} {$oldPath} \n ");
        }
        if (is_string($newimageID)) {

            //  $this->insertImageTranslated($newimageID, $data['oldName'], 'image');

        } else {
            // $this->insertfailImport('image', $oldPath);
            $this->log(" Problem with image: {$oldPath} \n ");
            return 'other';
        }

        echo "new id {$newimageID} [DONE]\n";
        return $newimageID;
    }


    /**
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    public function convertToUtf8($string)
    {
        return mb_convert_encoding($string, 'UTF-8');
    }


    public function getSlug($text)
    {

        $text =  \Onm\StringUtils::generateSlug(utf8_encode($text));

        return $text;
    }

    public function log($text = null)
    {
        if (isset($text) && !is_null($text) ) {
            printf($text);

            $handle = fopen($this->logFile, "a");

            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }

    public function printResults()
    {

        echo "\n IMAGES \n";
        $sql = "SELECT type , count( * ) AS `total` FROM `images_translated` GROUP BY type";

        $count_sql = $GLOBALS['application']->conn->Prepare($sql);
        $rs = $GLOBALS['application']->conn->Execute($count_sql);
        if (!$rs) {
            echo $GLOBALS['application']->conn->ErrorMsg();
        } else {
            while (!$rs->EOF) {
                echo "There are imported {$rs->fields['total']} type {$rs->fields['type']}.\n";
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }
}

