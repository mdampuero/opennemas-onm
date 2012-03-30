<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of importContents
 * Import opinions with author and his photos & articles with categories,images...
 *
 * @author sandra
 */

class migrationNewslibrary {

    public $logFile = "";

    public function __construct ()
    {
        $this->logFile = __DIR__."/../log/log.txt";

        $handle = fopen( $this->logFile , "wb");
        fclose($handle);
    }

    public function insertRefactorID($contentID, $newID, $type) {
        $sql_translation_request =
                'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($contentID, $newID, $type);
        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);
        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);
        if (!$rss) {
             $this->log('\n insertRefactorID: '.$GLOBALS['application']->conn->ErrorMsg() );
        }

    }

    public function elementIsImported($contentID, $contentType) {
        if(isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            echo "There is imported {$contentID} - {$contentType}\n.";
        }
    }

    public function elementTranslate($contentID) {
        if(!empty($contentID)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=?';

            $values = array($contentID, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        }
        return 0;
    }

    public function sqlExecute() {

        //create table for tranlated images path in photId
        $sql="CREATE TABLE IF NOT EXISTS `images_translated` (
          `pk_content` bigint(10) NOT NULL,
          `url` varchar(250)  NULL,
          `type` varchar(20)  NULL,
          PRIMARY KEY (`pk_content`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1";

        $rss = $GLOBALS['application']->conn->Execute($sql);

        //create table for tranlated author opinion
        $sql="CREATE TABLE IF NOT EXISTS `author_opinion` (
          `pk_author` bigint(10) NOT NULL,
          `text` varchar(250)  NULL,
          PRIMARY KEY (`text`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1";

        $rss = $GLOBALS['application']->conn->Execute($sql);

    }

    public function sqlClearData() {

        //emtpy tables
        $tables = array('articles, albums, albums_photos,
            attachments, authors, authors_imgs, books, commets,
            content_positions, kiosko, opinions, pclave, photos, polls, poll_items,
            ratings, related_contents, specials, special_contents, videos, votes,
            translation_ids, author_opinion, images_translated');

        foreach($tables as $table) {
            $sql="TRUNCATE TABLE {$table}";
            $rss = $GLOBALS['application']->conn->Execute($sql);
            if (!$rss) {
                $this->log('clearsql function: '.$GLOBALS['application']->conn->ErrorMsg() );
            }
        }

        $sql = "SELECT pk_content FROM contents WHERE content_type != 2 AND content_type != 12 AND content_type != 13";
        $rss = $GLOBALS['application']->conn->Execute($sql);
        $result= $rs->getArray();
        $contents = explode(',', $result);

        $sql = "DELETE * FROM contents WHERE pk_content IN ($contents)";
        $rss = $GLOBALS['application']->conn->Execute($sql);
        $sql = "DELETE * FROM contents_categories WHERE pk_fk_content IN ($contents)";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents function: '.$GLOBALS['application']->conn->ErrorMsg() );
        }


    }


     public function insertImageTranslated($pk_photo, $url, $type) {
        $sql_translation_request =
                'INSERT INTO images_translated (`url`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($url, $pk_photo, $type);

        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);

        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);

        if (!$rss) {
            $this->log('insertImageTranslated function: '.$GLOBALS['application']->conn->ErrorMsg() );
        }
    }

    public function imageIsImported($url, $contentType)
    {
        if(isset($url) && isset($contentType)) {
            $sql = 'SELECT * FROM `images_translated` WHERE `url`=? AND type=?';

            $values = array($url, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            echo "There is imported {$url} - {$contentType}\n.";
        }
        return false;
    }


     public function insertAuthorTranslated($pk_author, $text) {
        $sql_translation_request =
                'INSERT INTO `author_opinion` (`pk_author`, `text`)
                                       VALUES (?, ?)';
        $translation_values = array($pk_author, $text);

        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);

        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);

        if (!$rss) {
            $this->log('insertAuthorTranslated function: '.$GLOBALS['application']->conn->ErrorMsg() );
        }
    }

     public function authorIsImported($text)
    {
        if(isset($text) && !empty($text)) {
            $sql = 'SELECT * FROM `author_opinion` WHERE `text` LIKE ? ';

            $values = array("%{$text}%");
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_author']);
            }

        } else {
            echo "There is imported {$text} \n.";
        }
        return false;
    }


    public function updateViews($contentID, $views)
    {
        if(isset($contentID) && isset($views)) {
            $sql = 'UPDATE `contents` SET `views`=?, `available`=? WHERE pk_content=?';

            $values = array($views, 1, $contentID);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql,
                                                          $values);
            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            }

        } else {
            echo "Please provide a contentID and views to update it.";
        }
    }

    /**
     * Load properties in a object.
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function load($properties) {
         $item = new stdClass();
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $item->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $item->{$k} = $v;
                }
            }
        }
        return $item;

    }

    public function log($text = null) {
        if(isset($text) && !is_null($text) ) {
            printf($text);

            $handle = fopen( $this->logFile , "a");

            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }

    static public function printResults() {

        $sql = "SELECT type , count( * ) AS `total` FROM `translation_ids` GROUP BY type";

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

    public function messageStatus($text)
    {
        system('clear');
        $date = date('d.m.Y');
        //$percent = ($total)? floor($current*100/$total): 0;
        echo sprintf("[%s] %s", $date, $text);
    }

}
