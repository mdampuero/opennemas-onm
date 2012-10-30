<?php
/**
 * Bunch of classes that helps to import elements into ONM
 *
 * @package Onm
 * @author
 **/
class ImportHelper
{

    /**
     * undocumented class variable
     *
     * @var string
     **/
    static $logFile;

    /**
     * Registers one content into the matching table
     *
     * @return void
     * @author
     **/
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
     * Converts a given string to UTF-8 codification
     *
     * @return string
     **/
    static public function convertoUTF8($string)
    {
        return mb_convert_encoding($string, 'UTF-8');
    }

    /**
     * Logs one action in a file
     *
     * @return void
     * @author
     **/
    public function log($text = null)
    {

        self::$logFile = __DIR__.'/importer.log';

        if (isset($text) && !is_null($text) ) {
            $handle = fopen(self::$logFile, "a");
            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }

    public function updateViews($contentID, $views)
    {
        if (isset($contentID) && isset($views)) {
            $sql = 'UPDATE `contents` SET `views`=? WHERE pk_content=?';

            $values = array($views, $contentID);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute(
                $views_update_sql,
                $values
            );
            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
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

    public function elementIsImported($contentID, $contentType)
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

    /**
     * Function that creates images from local and inserts it on ONM Database
     *
     * @return boolean
     * @author
     **/
    public function importImages($data, $imageSrc)
    {
        preg_match_all("/src=[\"']?([^\"']?.*(png|jpg|gif|jpeg))[\"']?/i", $imageSrc, $matches);
        $res = explode('/', $matches[1][0]);
        $metadata = implode(', ', $res);
        $localPath = "/home/webdev-manager/public_html/public/media/hibridos/".urldecode($matches[1][0]);

        $imgData = array(
            'title' => $res[3],
            'category' => JoomlaImporter::matchCategory($data['catid']),
            'fk_category' => JoomlaImporter::matchCategory($data['catid']),
            'category_name'=> 'image',
            'content_status' => 1,
            'frontpage' => 0,
            'in_home' => 0,
            'metadata' => $res[0],
            'description' => self::convertoUTF8($data['title']),
            'id' => 0,
            'created' => $data['created'],
            'starttime' => $data['publish_up'],
            'changed' => $data['modified'],
            'fk_user' => $data['created_by'],
            'fk_author' => $data['created_by'],
            'local_file' => $localPath,
        );

        $image = new Photo();

        $newimageID = $image->createFromLocalFile($imgData);
        if (is_string($newimageID)) {
            ImportHelper::log('Image with ID '.$newimageID." imported.\n");
        } else {
            ImportHelper::log('Image '.$matches[1][0]." from article ".$data['id']." NOT imported.\n");
        }
    }

    /**
     * Function that creates images from local and inserts it on ONM Database
     *
     * @return boolean
     * @author
     **/
    public function importImage($data, $imageSrc)
    {
        $date = new DateTime($data['created']);
        $dateForDirectory = $date->format("/Y/m/d/");
        $imgData = array(
            'title' => $imageSrc,
            'category' => JoomlaImporter::matchCategory($data['catid']),
            'fk_category' => JoomlaImporter::matchCategory($data['catid']),
            'category_name'=> 'image',
            'content_status' => 1,
            'frontpage' => 0,
            'in_home' => 0,
            'metadata' => $imageSrc,
            'description' => self::convertoUTF8($data['title']),
            'id' => 0,
            'created' => $data['created'],
            'changed' => $data['modified'],
            'local_file' => JoomlaImporter::$configuration['media']['old-media'].$imageSrc,
        );



        $image = new Photo();

        $newimageID = $image->createFromLocalFile($imgData, $dateForDirectory);

        if (is_string($newimageID)) {
            ImportHelper::log('Image with ID '.$newimageID." imported.\n");
            return $newimageID;
        } else {
            ImportHelper::log('Image '.$imageSrc." from article ".$data['id']." NOT imported.\n");
        }
    }

    /**
     * Function that creates video from url and inserts it on ONM Database
     *
     * @return boolean
     * @author
     **/
    public function importVideo($data, $url)
    {

        try {
            $url = rawurldecode($url);
            $videoP = new \Panorama\Video($url);
            $information = $videoP->getVideoDetails();

            $values = array(
                'file_path'      => $url,
                'video_url'      => $url,
                'category'       => JoomlaImporter::matchCategory($data['catid']),
                'available'      => 1,
                'content_status' => 1,
                'title'          =>  ImportHelper::convertoUTF8($data['title']),
                'metadata'       => $data['metadata'],
                'description'    => $data['title'].' video '.$data['introtext'],
                'author_name'    => $data['origin'],
            );
            $values['information'] = $information;

        } catch (\Exception $e) {
            ImportHelper::log("\n 1 Can't get video information. Check the $url\n ");
            return;
        }


        try {

            $video = new \Video();
            $newVideoID = $video->create($values);

            if (is_string($newVideoID)) {
                ImportHelper::log('Video with ID '.$newVideoID." imported.\n");
                return $newVideoID;
            } else {
                ImportHelper::log('Video '.$url." from article ".$data['id']." NOT created.\n");

            }


        } catch (\Exception $e) {

            ImportHelper::log("\n Problem with video: {$e->getMessage()} {$url} \n ");
        }


    }

    /***
        Clear default contents in target database
    */

    public function sqlClearData()
    {

        echo "\n Database was Cleaned \n ";
        //emtpy tables
        $tables = array('articles', 'videos', 'photos', 'contents_categories',
          /*  'attachments', 'authors', 'author_imgs', 'comments', 'letters',
            'content_positions', 'kioskos', 'opinions', 'pclave',  'polls', 'poll_items',
            'ratings', 'related_contents', 'specials', 'special_contents',  'votes',
            'author_opinion', 'images_translated', album, album_photos */
            'translation_ids');

        foreach ($tables as $table) {
            $sql="TRUNCATE TABLE {$table}";
            $rss = $GLOBALS['application']->conn->Execute($sql);
            if (!$rss) {
                ImportHelper::log('clearsql function: '.$GLOBALS['application']->conn->ErrorMsg());
            }
        }

        $sql = "SELECT pk_content FROM contents WHERE fk_content_type = 2".
        " OR fk_content_type = 12 OR fk_content_type = 13";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        $result= $rss->GetArray();
        $contents= '';
        foreach ($result as $res) {
            $contents .= $res['pk_content'] .', ';
        }

        $sql = "DELETE FROM contents WHERE pk_content NOT IN ($contents 0)";

        $rss = $GLOBALS['application']->conn->Execute($sql);
        $sql = "DELETE FROM contents_categories WHERE pk_fk_content NOT IN  ($contents 0)";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            ImportHelper::log('clear contents function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "ALTER TABLE `contents` AUTO_INCREMENT =1";

        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            ImportHelper::log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "ALTER TABLE `authors` AUTO_INCREMENT =4";

        $rss = $GLOBALS['application']->conn->Execute($sql);


    }
}

