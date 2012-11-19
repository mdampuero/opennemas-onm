<?php

class CanariasHelper
{

    public $logFile = "";

    public function __construct ($logName = 'log.txt')
    {
        $this->logFile = __DIR__."/../log/".$logName;

        $handle = fopen($this->logFile, "a");
        fclose($handle);
    }



    public function clearLog ()
    {
        echo "\n Log was Cleaned \n ";
        $handle = fopen($this->logFile, "w");
        fclose($handle);
    }

    public function insertRefactorId($contentID, $newID, $type)
    {
        $sql = 'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $values  = array($contentID, $newID, $type);
        $request = $GLOBALS['application']->conn->Prepare($sql);
        $rss     = $GLOBALS['application']->conn->Execute($request, $values);
        if (!$rss) {
             $this->log('\n insertRefactorID: '.$GLOBALS['application']->conn->ErrorMsg());
        }

    }

    public function elementIsImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values  = array($contentID, $contentType);
            $request = $GLOBALS['application']->conn->Prepare($sql);
            $rss     = $GLOBALS['application']->conn->Execute($request, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            echo "There is imported {$contentID} - {$contentType}\n.";
        }
    }

    public function elementTranslated($contentID)
    {
        if (!empty($contentID)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=?';

            $values = array($contentID);
            $request = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($request, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg()."\n";
            } else {
                return ($rss->fields['pk_content']);
            }

        }
        return 0;
    }


    public function sqlClearData()
    {

        echo "\n Database was Cleaned \n ";
        //emtpy tables
        $tables = array('articles', 'albums', 'albums_photos',
            'attachments', 'authors', 'author_imgs', 'comments', 'letters',
            'content_positions', 'kioskos', 'opinions', 'pclave', 'photos', 'polls', 'poll_items',
            'ratings', 'related_contents', 'specials', 'special_contents', 'videos', 'votes',
            'translation_ids', 'author_opinion', 'images_translated');

        foreach ($tables as $table) {
            $sql="TRUNCATE TABLE {$table}";
            $rss = $GLOBALS['application']->conn->Execute($sql);
            if (!$rss) {
                $this->log('clearsql function: '.$GLOBALS['application']->conn->ErrorMsg());
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
            $this->log('clear contents function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "ALTER TABLE `contents` AUTO_INCREMENT =1";

        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "ALTER TABLE `authors` AUTO_INCREMENT =4";

        $rss = $GLOBALS['application']->conn->Execute($sql);


        $sql = " TRUNCATE TABLE `failed_import`";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

    }

    public function clearCategories()
    {

        $sql="DELETE FROM `content_categories` WHERE pk_content_category >20";
        $rss = $GLOBALS['application']->conn->Execute($sql);
        if (!$rss) {
            $this->log('clearCategories function: '.$GLOBALS['application']->conn->ErrorMsg());
        }
        $sql = "ALTER TABLE `authors` AUTO_INCREMENT =20";

        $rss = $GLOBALS['application']->conn->Execute($sql);


    }

    public function insertFailImport($type, $text)
    {
         $sql =
                'INSERT INTO failed_import (`text`, `type`)
                                       VALUES (?, ?)';
        $values = array($text, $type);

        $request = $GLOBALS['application']->conn->Prepare($sql);

        $rss = $GLOBALS['application']->conn->Execute($request, $values);

        if (!$rss) {
            $this->log('element failed register import. function: '.$GLOBALS['application']->conn->ErrorMsg());
        }
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
        $text = \StringUtils::get_title(utf8_encode($text));
        //$text = \Onm\StringUtils::normalize($text);
        //$text = \Onm\StringUtils::get_title($text);

        return $text;
    }

    public function clearImgTag($html)
    {

        //deleted http://canariasahora.com/ http://canariasahora.es/ ../../.. http://www.canariasahora.es/
        $pattern = array(
            "@http://canariasahora.com/@",
            "@http://www.canariasahora.com/@",
            "@http://www.canariasahora.es/@",
            "@http://canariasahora.es/@",
            "@../../../../@",
            "@../../../@"
        );
        $replacement = array("/", "/", "/", "/", "/", "/");
        preg_match_all('@src *= *["\']?([^"\']*)@', $html, $result);
        $source = preg_replace($pattern, $replacement, $result[1][0]);

        return $source;
    }


    public function insertImageTranslated($pk_photo, $url, $type)
    {
        $sql_translation_request =
                'INSERT INTO images_translated (`url`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $values = array($url, $pk_photo, $type);

        $request = $GLOBALS['application']->conn->Prepare($sql_translation_request);

        $rss = $GLOBALS['application']->conn->Execute($request, $values);

        if (!$rss) {
            $this->log('insertImageTranslated function: '.$GLOBALS['application']->conn->ErrorMsg());
        }
    }

    public function imageIsImported($url, $contentType)
    {
        if (isset($url) && isset($contentType)) {


            $sql = 'SELECT * FROM `images_translated` WHERE `url`=? AND type=?';

            $values  = array($url, $contentType);
            $request = $GLOBALS['application']->conn->Prepare($sql);
            $rss     = $GLOBALS['application']->conn->Execute($request, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            echo "Problem: I don't know There is imported {$url} - {$contentType}\n.";
        }
        return false;
    }


    public function insertAuthorTranslated($pk_author, $text)
    {
        $sql = 'INSERT INTO `author_opinion` (`pk_author`, `text`)
                                       VALUES (?, ?)';


        $values = array($pk_author, $text);

        $request = $GLOBALS['application']->conn->Prepare($sql);

        $rss = $GLOBALS['application']->conn->Execute($request, $values);

        if (!$rss) {
            $this->log('insertAuthorTranslated function: '.$GLOBALS['application']->conn->ErrorMsg());
        }
    }

    public function authorIsImported($text)
    {
        if (isset($text) && !empty($text)) {
            $sql = 'SELECT * FROM `author_opinion` WHERE `text` LIKE ? ';

            $values  = array("%{$text}%");
            $request = $GLOBALS['application']->conn->Prepare($sql);
            $rss     = $GLOBALS['application']->conn->Execute($request, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_author']);
            }

        } else {
            echo "There is imported {$text} author opinion \n.";
        }
        return false;
    }

    public function updateCover($contentID, $coverId)
    {
        if (isset($contentID) && isset($coverId)) {

            $sql = 'UPDATE `albums` SET `cover_id`=?  WHERE pk_album=?';

            $values  = array($coverId, $contentID);
            $request = $GLOBALS['application']->conn->Prepare($sql);
            $rss     = $GLOBALS['application']->conn->Execute($request, $values);
            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            }

        } else {
            echo "Please provide a contentID and coverid to update it.";
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

    public function load($properties)
    {
         $item = new stdClass();
        if (is_array($properties)) {
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $item->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if (!is_numeric($k) ) {
                    $item->{$k} = $v;
                }
            }
        }
        return $item;

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

        /*echo "\n AUTHORS OPINION \n";
        $sql = "SELECT count(*) as total FROM `author_opinion` ";
        $rs = $GLOBALS['application']->conn->Execute($sql);
        */
        if (!$rs) {
            echo $GLOBALS['application']->conn->ErrorMsg();
        } else {
            echo "There are imported {$rs->fields['total']}  authors opinion.\n";
            $rs->Close(); # optional
        }
        echo "\n CONTENTS \n";
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

    public function messageStatus($text)
    {
        system('clear');
        $date = date('d.m.Y');
        //$percent = ($total)? floor($current*100/$total): 0;
        echo sprintf("[%s] %s", $date, $text);
    }

    public function exporterLog($text = null)
    {
        if (isset($text) && !is_null($text) ) {
            printf($text);

            $handle = fopen(__DIR__."/../log/exporterLog.txt", "a");

            if ($handle) {
                $datawritten = fwrite($handle, $text." \n");
                fclose($handle);
                return true;
            } else {
                echo "There was a problem while trying to export your message.";
                return false;
            }
        }
    }
}

