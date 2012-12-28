<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of importContents
 * Import opinions with author and his photos & articles with categories,images...
 *
 *
 */

class OnmHelper
{

    public $logFile = "";

    public function __construct($logName = 'log.txt')
    {
        $this->logFile = __DIR__."/../log/".$logName;

        $handle = fopen($this->logFile, "a");
        fclose($handle);
    }

    public function insertRefactorId($contentID, $newID, $type)
    {
        $sql_translation_request =
                'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($contentID, $newID, $type);
        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);
        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request, $translation_values);
        if (!$rss) {
             $this->log('\n insertRefactorID: '.$GLOBALS['application']->conn->ErrorMsg());
        }

    }

    public function elementIsImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            echo "There is imported {$contentID} - {$contentType}\n.";
        }
    }

    public function elementTranslate($contentID)
    {
        if (!empty($contentID)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=?';

            $values = array($contentID);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg()."\n";
            } else {
                return ($rss->fields['pk_content']);
            }

        }
        return 0;
    }

    public function insertAuthorTranslated($pk_author, $text)
    {
        $sql_translation_request =
                'INSERT INTO `author_opinion` (`pk_author`, `text`)
                                       VALUES (?, ?)';
        $translation_values = array($pk_author, $text);
        var_dump($translation_values);

        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);

        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request, $translation_values);

        if (!$rss) {
            $this->log('insertAuthorTranslated function: '.$GLOBALS['application']->conn->ErrorMsg());
        }
    }

    public function imageIsImported($url, $contentType)
    {
        if (isset($url) && isset($contentType)) {

            if (!empty($this->importedImages)) {
                $newID = $this->importedImages[$contentType][$url];
                if (!empty($newID)) {
                    return $newID;
                }
            } else {
                $this->importedImages = array();
                $sql = 'SELECT * FROM `images_translated`';
                $rs  = $GLOBALS['application']->conn->Execute($sql);
                if (!$rs) {
                    echo $GLOBALS['application']->conn->ErrorMsg();
                    $this->helper->log(self::$originConn->ErrorMsg());
                } else {

                    $totalRows = $rs->_numOfRows;
                    $current = 1;
                    while (!$rs->EOF) {
                        $i = $rs->fields['type'];
                        $j = $rs->fields['url'];
                        $this->importedImages[$i][$j] = $rs->fields['pk_content'];

                        $rs->MoveNext();
                    }
                    $rs->Close();
                }
            }

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


    public function authorIsImported($text)
    {
        if (isset($text) && !empty($text)) {
            $sql = 'SELECT * FROM `author_opinion` WHERE `text` LIKE ? ';

            $values = array("%{$text}%");
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql, $values);

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
        if (isset($contentID) && isset($views)) {
            $sql = 'UPDATE `contents` SET `views`=? WHERE pk_content=?';

            $values = array($views,  $contentID);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql, $values);
            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            }

        } else {
            echo "Please provide a contentID and views to update it.";
        }
    }

    public function sqlClear()
    {
        $sql = " TRUNCATE TABLE `images_translated`";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = " TRUNCATE TABLE `author_opinion`";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = " TRUNCATE TABLE `translation_ids`";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        echo "\n Database imported tables was Cleaned \n ";
    }

    public function sqlClearOpinions()
    {
        $sql = " TRUNCATE TABLE `opinions`";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "SELECT pk_content FROM contents WHERE fk_content_type = 4";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        $result= $rss->GetArray();
        $contents= '';
        foreach ($result as $res) {
            $contents .= $res['pk_content'] .', ';
        }

        $sql = "DELETE FROM contents WHERE pk_content IN ($contents 0)";
        $rss = $GLOBALS['application']->conn->Execute($sql);
        $sql = "DELETE FROM contents_categories WHERE pk_fk_content IN  ($contents 0)";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "ALTER TABLE `contents` AUTO_INCREMENT =100";

    }

    public function sqlClearNewsstand()
    {
        $sql = " TRUNCATE TABLE `kioskos`";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents '.$sql.' function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "SELECT pk_content FROM contents WHERE fk_content_type = 14";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        $result= $rss->GetArray();
        $contents= '';
        foreach ($result as $res) {
            $contents .= $res['pk_content'] .', ';
        }

        $sql = "DELETE FROM contents WHERE pk_content IN ($contents 0)";
        $rss = $GLOBALS['application']->conn->Execute($sql);
        $sql = "DELETE FROM contents_categories WHERE pk_fk_content IN  ($contents 0)";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contents function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

        $sql = "ALTER TABLE `contents` AUTO_INCREMENT =100";

    }


    public function clearContentsCategoriesTable()
    {

        $sql = "SELECT pk_content FROM contents";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        $result= $rss->GetArray();
        $contents= '';
        foreach ($result as $res) {
            $contents .= $res['pk_content'] .', ';
        }

        $sql = "DELETE FROM contents_categories WHERE pk_fk_content  NOT IN ($contents 0)";
        $rss = $GLOBALS['application']->conn->Execute($sql);

        if (!$rss) {
            $this->log('clear contentscategories function: '.$GLOBALS['application']->conn->ErrorMsg());
        }

         echo "\n ContentsCategories Table was Cleaned \n ";

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

        echo "\n AUTHORS OPINION \n";
        $sql = "SELECT count(*) as total FROM `author_opinion` ";
        $rs = $GLOBALS['application']->conn->Execute($sql);

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

    public function sqlExecute()
    {

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

        echo "\n Database- import tables was Created \n \n ";
    }
}

