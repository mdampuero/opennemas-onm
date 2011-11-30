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
    public function logElementInsert($original, $final, $type) {
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
     * Logs one action in a file
     *
     * @return void
     * @author 
     **/
    public function log($text = null) {

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
    
    public function updateViews($contentID, $views)
    {
        if(isset($contentID) && isset($views)) {
            $sql = 'UPDATE `contents` SET `views`=? WHERE pk_content=?';
            
            $values = array($views, $contentID);
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
                return ($rss->_numOfRows >= 1);
            }
            
        } else {
            echo "Please provide a contentID and views to update it.";
        }
    }
} // END class ImportHelper