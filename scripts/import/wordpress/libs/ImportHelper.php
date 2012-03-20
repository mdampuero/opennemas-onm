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
        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);
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

        if(isset($text) && !is_null($text) ) {
            $handle = fopen( self::$logFile , "a");
            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }


    public function elementIsImported($contentID, $contentType)
    {
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
} // END class ImportHelper