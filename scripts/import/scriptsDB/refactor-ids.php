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

class refactorIds {

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

        $sql=" ALTER TABLE `contents`
            CHANGE `permalink` `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
         $sql2=" ALTER TABLE `contents` ADD `params` LONGTEXT NULL,
            ADD `category_name` VARCHAR( 255 ) NOT NULL COMMENT 'name category',           
            DROP `archive`,
            DROP `paper_page`";
        $rss = $this->orig->conn->Execute($sql);
        if (!$rss) {
            printf( 'sqlExecute function: '. $this->orig->conn->ErrorMsg());
            $this->log('sqlExecute function: '.$this->orig->conn->ErrorMsg() );
            exit();
          
        }
        $rss = $this->orig->conn->Execute($sql2);
        if (!$rss) {
            printf( 'sqlExecute function: '. $this->orig->conn->ErrorMsg());
            $this->log('sqlExecute function: '.$this->orig->conn->ErrorMsg() );
        
        }

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

    public function updateContent($contentID, $newID, $slug, $content_type) {
       
        $values = array($newID, $slug, $contentID);
         
        $sql = 'UPDATE `contents` SET `pk_content`=?, `slug`=? WHERE pk_content=?';
        $contentSql =  $this->orig->conn->Prepare($sql);

        $rss = $this->orig->conn->Execute($contentSql,$values);

        if (!$rss) {
            printf( 'updateContent function: '.  $values. $this->orig->conn->ErrorMsg() );
            $this->log('updateContent function: '. $values. $this->orig->conn->ErrorMsg() );
            return false;
        }
         //tables(articles, opinions,...)
        $pk_table = " pk_".strtolower($content_type);
        $table_name = strtolower($content_type). 's';
         
        $sql = "UPDATE ". $table_name ." SET ".$pk_table."= ? WHERE ". $pk_table ."= ?";
     
        $sqlTable = $this->orig->conn->Prepare($sql);
        $values = array($newID, $contentID);

        $rss = $this->orig->conn->Execute($sqlTable,$values);

         if (!$rss) {
            printf( 'updateContent function: '.$pk_table." - ".  $values. $this->orig->conn->ErrorMsg() );
            $this->log('updateContent function: '.$pk_table." - ".  $values. $this->orig->conn->ErrorMsg() );
            return false;
        }


        return true;
    }


    public function prepareSqls() {

    //contents_categories
        $sql = "UPDATE contents_categories SET `pk_fk_content`=?  WHERE `pk_fk_content` = ? " ;
        $sqlList[] = $this->orig->conn->Prepare($sql);


    //content_positions
        $sql = "UPDATE content_positions SET `pk_fk_content`= ? WHERE `pk_fk_content` = ?";
         $sqlList[] = $this->orig->conn->Prepare($sql);

    //albums_photos
        $sql = "UPDATE albums_photos SET `pk_photo`= ? WHERE  `pk_photo`= ?";
        $sql2 =" UPDATE albums_photos SET `pk_album`= ? WHERE  `pk_album`= ?";

        $sqlList[] = $this->orig->conn->Prepare($sql);
        $sqlList[] = $this->orig->conn->Prepare($sql2);

    //author_imgs
        $sql = "UPDATE author_imgs SET `fk_photo`= ? WHERE  `fk_photo`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);

    // comments
        $sql = "UPDATE comments SET `pk_comment`= ? WHERE  `pk_comment`= ?";
        $sql2 =" UPDATE comments SET `fk_content`= ? WHERE  `fk_content`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);
        $sqlList[] = $this->orig->conn->Prepare($sql2);

    //poll_items
        $sql =" UPDATE poll_items SET `fk_pk_poll`= ? WHERE  `fk_pk_poll`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);

    //related_contents
        $sql = "UPDATE related_contents SET `pk_content1`= ? WHERE  `pk_content1`= ?";
        $sql2 =" UPDATE related_contents SET `pk_content2`= ? WHERE  `pk_content2`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);
        $sqlList[] = $this->orig->conn->Prepare($sql2);

    //votes
        $sql = "UPDATE votes SET `pk_vote`= ? WHERE  `pk_vote`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);

        return $sqlList;
    }


    public function updateTables($sqlList,$values) {
        foreach($sqlList as $sql) {
            $rss = $this->orig->conn->Execute($sql,$values);
            if (!$rss) {
                $this->log( "\n- ".$sql." - ".$values." - ".$this->orig->conn->ErrorMsg() );
            }
        }
    }


    public function refactorDB($pk_contents) {

        $content_types = $this->getContentTypes();
        
        $newID = 1;
        $values = array();
        foreach ($pk_contents as $content) {

            $slug = String_Utils::get_title( $content["title"] );
            $content_type = $content["fk_content_type"];
            $contentID = $content['pk_content'];
            $resp = $this->updateContent($contentID, $newID, $slug, $content_types[$content_type]);
         //   var_dump($contentID.", $newID, $slug, $content_type, ".$content_types[$content_type]);
          
            if ($resp) {

                $this->insertRefactorID($contentID, $newID, $content_types[$content_type]);
                $values[] = array($newID,  $contentID);
               
                $newID++;

            }

        }

        $sqlList = $this->prepareSqls();
        
        $this->updateTables($sqlList,$values);
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

    public function addMasterUsers() {
    //If there are some group with id=4
        $sql ="UPDATE  `user_groups` SET `pk_user_group` = '8' WHERE `pk_user_group` ='4'";
        $rss = $this->orig->conn->Execute($sql);
        $sql2="UPDATE `users` SET `fk_user_group` = '8' WHERE `users`.`fk_user_group` ='4'";
        $rss = $this->orig->conn->Execute($sql2);

    //add Masters
        $sqlInsert= "INSERT INTO `user_groups` (`pk_user_group` ,`name`)".
                    "VALUES (4 , 'Masters' )";
        $rss = $this->orig->conn->Execute($sqlInsert);

        $sqlUpdate = " UPDATE `users` SET `fk_user_group` = '4' WHERE ".
                     "`users`.`login` ='macada' ".
                     " OR `users`.`login` ='alex'".
                     " OR `users`.`login` ='fran'".
                     " OR `users`.`login` ='sandra'";

        $rss = $this->orig->conn->Execute($sqlUpdate);

    }

}
 