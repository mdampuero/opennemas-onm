<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of refactor_ids
 *
 * @author sandra
 */

class importCronicas {

      public function __construct ($config = array())
    {
        $this->logFile ="log.txt";
        $this->dbConfig = $config;

        $handle = fopen( $this->logFile , "wb");
        fclose($handle);


        if (isset($config['bd_host'])
            && isset($config['bd_database'])
            && isset($config['bd_user'])
            && isset($config['bd_pass'])
            && isset($config['bd_type']))
        {

            $this->orig->conn= ADONewConnection($config['bd_type']);
            $this->orig->conn->PConnect(
                                    $config['bd_host'], $config['bd_user'],
                                    $config['bd_pass'], $config['bd_database']
                                  );

        } else {

            printf(    "ERROR: You must provide the connection configuration to the database");
            die();
        }

    }

    public function sqlExecute() {

        $sql="CREATE TABLE IF NOT EXISTS `refactor_ids` (
          `pk_content_old` bigint(10) NOT NULL,
          `pk_content` bigint(10) NOT NULL,
          `type` varchar(20)  NULL,
          PRIMARY KEY (`pk_content_old`,`pk_content`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
        $rss = $this->orig->conn->Execute($sql);

    }

     public function getContents() {
         $sql = "SELECT pk_content, title, fk_content_type FROM contents";
         $rss = $this->orig->conn->Execute($sql);

        if (!$rss) {
            printf( $this->orig->conn->ErrorMsg() );
            $this->log($this->orig->conn->ErrorMsg() );
        } else {
            return ($rss->GetArray());
        }

     }

      public function getContentTypes() {
       
        $items = array();
        $sql = 'SELECT pk_content_type, name, title FROM content_types ';

        $rs =  $this->orig->conn->Execute($sql);
        while(!$rs->EOF) {
            $pk_content_type = $rs->fields['pk_content_type'];
            $items[$pk_content_type] = htmlentities($rs->fields['name']);
            $rs->MoveNext();
        }

        return $items;

     }

     public function insertRefactorID($contentID, $newID, $type) {
        $sql_translation_request =
                'INSERT INTO refactor_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($contentID, $newID, $type);

        $translation_ids_request = $this->orig->conn->Prepare($sql_translation_request);

        $rss = $this->orig->conn->Execute($translation_ids_request,
                                                      $translation_values);

        if (!$rss) {
            printf( 'insertRefactorID function: '. $this->orig->conn->ErrorMsg() );
            $this->log('insertRefactorID function: '.$this->orig->conn->ErrorMsg() );
        }
    }

  






    public function log($text = null) {
        if(isset($text) && !is_null($text) ) {
            $handle = fopen( $this->logFile , "a");
            
            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }

}
 