<?php
/*
 * Change old database schema for new onm-core
 * Update content table and refactor id's
 */

/**
 * Description of refactor_ids
 *
 * @author sandra
 */

class refactorIds {

    /**
     * Create logfile and get database conection.
     *
     * @param array $config data for Database conn.
     *
     */
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

    /**
     * Create table refactor_ids in database and modify table contents.
     * @return bool  true if sql execute ok
     *
     */
    public function modifySchema() {

        $sql1 ="DROP TABLE IF EXISTS `settings`";
        $rss = $this->orig->conn->Execute($sql1);

        $sql2 = " CREATE TABLE IF NOT EXISTS `settings` (
                  `name` varchar(128) NOT NULL DEFAULT '',
                  `value` longtext NOT NULL,
                  PRIMARY KEY (`name`)
                ) ENGINE=MyISAM DEFAULT CHARSET=latin1;
                ";
        $rss = $this->orig->conn->Execute($sql2);

        if (!$rss) {
            printf(    "ERROR: Can't create settings table");
            die();
        }

        $sql="CREATE TABLE IF NOT EXISTS `refactor_ids` (
          `pk_content_old` bigint(10) NOT NULL,
          `pk_content` bigint(10) NOT NULL,
          `type` varchar(20)  NULL,
          PRIMARY KEY (`pk_content_old`,`pk_content`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1";

        $rss = $this->orig->conn->Execute($sql);
        if (!$rss) {
            printf(    "ERROR: Can't modify database");
            die();
        }

        $sql1=" ALTER TABLE `contents`
            CHANGE `permalink` `slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";

        $sql2=" ALTER TABLE `contents`
            ADD `params` LONGTEXT NULL,
            ADD `category_name` VARCHAR( 255 ) NOT NULL COMMENT 'name category',           
            DROP `archive`,
            DROP `paper_page`";

     //   $rss = $this->orig->conn->Execute($sql1);
        if (!$rss) {
            printf( 'sqlExecute function: '. $this->orig->conn->ErrorMsg());
            $this->log('sqlExecute function: '.$this->orig->conn->ErrorMsg() );

          
        }
       // $rss = $this->orig->conn->Execute($sql2);
        if (!$rss) {
            printf( 'sqlExecute function: '. $this->orig->conn->ErrorMsg());
            $this->log('sqlExecute function: '.$this->orig->conn->ErrorMsg() );

        }

        $sql = <<<INSERTAR
INSERT INTO `settings` (`name`, `value`) VALUES
('site_title', 's:110:"Nueva Tribuna - OpenNemas - Servicio online para tu periÃ³dico digital - Online service for digital newspapers";'),
('site_description', 's:110:"Nueva Tribuna - OpenNemas - Servicio online para tu periÃ³dico digital - Online service for digital newspapers";'),
('europapress_server_auth', 'a:3:{s:6:"server";s:0:"";s:8:"username";s:0:"";s:8:"password";s:0:"";}'),
('site_keywords', 's:83:"nuevatribuna, openNemas, servicio, online, periÃ³dico, digital, service, newspapers";'),
('time_zone', 's:3:"334";'),
('site_language', 's:5:"es_ES";'),
('mail_server', 's:9:"localhost";'),
('mail_username', 's:9:"webmaster";'),
('mail_password', 's:0:"";'),
('google_maps_api_key', 's:86:"ABQIAAAA_RE85FLaf_hXdhkxaS463hQC49KlvU2s_1jV47V5-i8q6UJ2IBQiAxw97Jt7tEWzuIY513Qutp-Cqg";'),
('google_custom_search_api_key', 's:33:"001675133575090086387:p82kkkctkiu";'),
('facebook', 'a:2:{s:7:"api_key";s:1:" ";s:10:"secret_key";s:1:" ";}'),
('google_analytics', 'a:2:{s:7:"api_key";s:1:" ";s:11:"base_domain";s:1:" ";}'),
('piwik', 'a:2:{s:7:"page_id";s:0:"";s:10:"server_url";s:0:"";}'),
('recaptcha', 'a:2:{s:10:"public_key";s:40:"6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is";s:11:"private_key";s:40:"6LfpY8ISAAAAAAuChcU2Agdwg8YzhprxZZ55B7Is";}'),
('items_per_page', 's:2:"20";'),
('refresh_interval', 's:3:"900";'),
('advertisements_enabled', 'b:0;'),
('log_level', 's:6:"normal";'),
('log_enabled', 's:2:"on";'),
('log_db_enabled', 's:2:"on";'),
('newsletter_maillist', 'a:2:{s:4:"name";s:8:"Openhost";s:5:"email";s:30:"newsletter@lists.opennemas.com";}'),
('site_agency', 's:26:"nuevatribuna.opennemas.com";'),
('activated_modules', 'a:32:{i:0;s:11:"ADS_MANAGER";i:1;s:15:"ADVANCED_SEARCH";i:2;s:13:"ALBUM_MANAGER";i:3;s:15:"ARTICLE_MANAGER";i:4;s:13:"CACHE_MANAGER";i:5;s:16:"CATEGORY_MANAGER";i:6;s:15:"COMMENT_MANAGER";i:7;s:20:"EUROPAPRESS_IMPORTER";i:8;s:12:"FILE_MANAGER";i:9;s:17:"FRONTPAGE_MANAGER";i:10;s:13:"IMAGE_MANAGER";i:11;s:15:"KEYWORD_MANAGER";i:12;s:14:"KIOSKO_MANAGER";i:13;s:20:"LINK_CONTROL_MANAGER";i:14;s:12:"MENU_MANAGER";i:15;s:13:"MYSQL_MANAGER";i:16;s:18:"NEWSLETTER_MANAGER";i:17;s:14:"ONM_STATISTICS";i:18;s:15:"OPINION_MANAGER";i:19;s:12:"PAPER_IMPORT";i:20;s:17:"PHP_CACHE_MANAGER";i:21;s:12:"POLL_MANAGER";i:22;s:17:"PRIVILEGE_MANAGER";i:23;s:16:"SETTINGS_MANAGER";i:24;s:20:"STATIC_PAGES_MANAGER";i:25;s:21:"SYSTEM_UPDATE_MANAGER";i:26;s:13:"TRASH_MANAGER";i:27;s:18:"USER_GROUP_MANAGER";i:28;s:12:"USER_MANAGER";i:29;s:13:"VIDEO_MANAGER";i:30;s:14:"WIDGET_MANAGER";i:31;s:7:"LOG_SQL";}'),
('europapress_sync_from_limit', 's:6:"604800";'),
('album_settings', 'a:5:{s:12:"total_widget";s:1:"4";s:10:"crop_width";s:3:"300";s:11:"crop_height";s:3:"240";s:11:"total_front";s:1:"2";s:9:"time_last";s:3:"100";}'),
('video_settings', 'a:3:{s:12:"total_widget";s:1:"4";s:11:"total_front";s:1:"2";s:13:"total_gallery";s:2:"20";}'),
('poll_settings', 'a:3:{s:9:"typeValue";s:7:"percent";s:9:"widthPoll";s:3:"600";s:10:"heightPoll";s:3:"500";}'),
('opinion_settings', 'a:2:{s:14:"total_director";s:1:"2";s:15:"total_editorial";s:1:"3";}'),
('contact_name', 's:7:"tribuna";'),
('contact_IP', 's:0:"";'),
('site_name', 's:13:"Nueva Tribuna";') ;

INSERTAR;

        $rss = $this->orig->conn->Execute($sql);
        if (!$rss) {
            printf( 'sqlExecute function: '. $this->orig->conn->ErrorMsg());
            $this->log('sqlExecute function: '.$this->orig->conn->ErrorMsg() );
            
        }
    

        return true;
    }




     public function executeSqlFile($fileName) {

        $file = file_get_contents($fileName); // Leo el archivo
   
        $tokens = preg_split("/;/", $file, null, PREG_SPLIT_NO_EMPTY);
        $length = count($tokens);
 
          if ($length > 0) {
              foreach ($tokens as $sql) {
                printf(  " \n - SENTENCE: {$sql} \n ");
                $rss = $this->orig->conn->Execute($sql);
                 if (!$rss) {
                    printf( 'ERROR '.$this->orig->conn->ErrorMsg() );
                    $this->log($this->orig->conn->ErrorMsg() );
                     
                }
              }
          }


     }

          /**
     * Get in array content data title, id and type
     *
     * @return array with content data or false
     *
     */
     public function getContents() {
         $sql = "SELECT pk_content, title, fk_content_type FROM contents where fk_content_type > 0";
         $rss = $this->orig->conn->Execute($sql);

        if (!$rss) {
            printf( $this->orig->conn->ErrorMsg() );
            $this->log($this->orig->conn->ErrorMsg() );
            return false;
        } else {
            return ($rss->GetArray());
        }

     }

     /**
      * get Type data for extract table names
      *
      * @return array with type data
      *
      */
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

     /**
     * Insert translated id in refactor table.
     *
     * @param string $contentID old id.
     * @param int $newID  new id
     * @param string $type cotent type
     *
     * @return bool
     */


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
            return false;
        }
       
        return true;
    }

    /**
     * Update content table and type_content table
     * Assign new id and insert slug
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function updateContent($contentID, $newID, $slug, $content_type) {
       
        $values = array($newID, $slug, $contentID);
         
        $sql = 'UPDATE `contents` SET `pk_content`=?, `slug`=? WHERE pk_content=?';
        $contentSql =  $this->orig->conn->Prepare($sql);

        $rss = $this->orig->conn->Execute($contentSql,$values);

        if (!$rss) {
            printf( '\n updateContent function: '.  $values. $this->orig->conn->ErrorMsg() );

            $this->log('\n updateContent function: '. var_dump($values). $this->orig->conn->ErrorMsg() );
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
            printf( '\n updateContent function: '.$pk_table." - ".  $values. $this->orig->conn->ErrorMsg() );
            $this->log('\n updateContent function: '.$pk_table." - ".  $values. $this->orig->conn->ErrorMsg() );
            return false;
        }


        return true;
    }

    /**
     * prepare sql sentences for actualice ids
     *
     * @param datatype $varname Var explanation.
     *
     * @return array sqlList array with prepare staments
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function prepareSecondaryTables() {

        $sqlList = array();
    //contents_categories
        $sql = "UPDATE contents_categories SET `pk_fk_content`=?  WHERE `pk_fk_content` = ? " ;
        $sqlList[] = $this->orig->conn->Prepare($sql);


    //content_positions
        $sql = "UPDATE content_positions SET `pk_fk_content`= ? WHERE `pk_fk_content` = ?";
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

    //ratings
        $sql = "UPDATE ratings SET `pk_rating`= ? WHERE  `pk_rating`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);
    

        return $sqlList;
    }


    public function prepareImgTables() {

        $sqlList = array();

    //albums_photos
        $sql = "UPDATE albums_photos SET `pk_photo`= ? WHERE  `pk_photo`= ?";
        $sql2 =" UPDATE albums_photos SET `pk_album`= ? WHERE  `pk_album`= ?";

        $sqlList[] = $this->orig->conn->Prepare($sql);
        $sqlList[] = $this->orig->conn->Prepare($sql2);

    //author_imgs
        $sql = "UPDATE author_imgs SET `fk_photo`= ? WHERE  `fk_photo`= ?";
        $sqlList[] = $this->orig->conn->Prepare($sql);


    //images in article
        $sql = "UPDATE articles SET `img1`=? WHERE `img1`=?";
        $sqlList[] = $this->orig->conn->Prepare($sql);

        $sql = "UPDATE articles SET `img2`=? WHERE `img2`=?";
        $sqlList[] = $this->orig->conn->Prepare($sql);

        return $sqlList;
    }


    /**
     * Execute sql with some values
     *
     * @param array $sqlList array with sql prepare staments.
     * @param array $values array with old id and new id for execute sql.

     * write in log sql failed
     */

    public function updateTables($sqlList,$values) {
      
        foreach($sqlList as $sql) {
            var_dump($sql);
            $rss = $this->orig->conn->Execute($sql,$values);
            if (!$rss) {
                $error =  "\n- ".$sql." - ".$values." - ".$this->orig->conn->ErrorMsg() ;
                $this->log('-'.$error);
                printf('\n-'.$error);
            }
        }
    }


    /**
     * Update tables with new ids and update content slug.
     *
     *
     * @param array $contents with all content data.
     *
     * write log fail
     */

    public function refactorDB() {

        $content_types = $this->getContentTypes();
        
        $newID = 1;
        $values = array();
        $contents = $this->getContents(); //get utils data contents


        foreach ($contents as $content) {

            $slug = String_Utils::get_title( $content["title"] );
            $content_type = $content["fk_content_type"];
            $contentID = $content['pk_content'];

            $resp = $this->updateContent($contentID, $newID, $slug, $content_types[$content_type]);
            //   var_dump($contentID.", $newID, $slug, $content_type, ".$content_types[$content_type]);
          
            if ($resp) {

                $this->insertRefactorID($contentID, $newID, $content_types[$content_type]);
                $values[] = array($newID,  $contentID);
                 printf('.');
                $newID++;

            }else {
                printf('\n \n');
                var_dump('FAIL'. $contentID.", $newID, $slug, $content_type, ".$content_types[$content_type]);
            }

        }
        printf('\n \n finish refactor contents');

        unset ($contents);

        return true;
        
    }


    public function getIds($where = '') {


        $items = array();
        $sql = "SELECT pk_content_old, pk_content  FROM refactor_ids ".$where;

        $rs =  $this->orig->conn->Execute($sql);
        if($rs) {
            while(!$rs->EOF) {

                $items[] = array($rs->fields['pk_content'],  $rs->fields['pk_content_old']);
                $rs->MoveNext();
            }

            return $items;

        } else {

            printf( 'getIds function: '. $this->orig->conn->ErrorMsg());
            $this->log('getIds function: '.$this->orig->conn->ErrorMsg() );
            return false;

        }
    }

    public function refactorSecondaryTables() {

        
        $values =  $this->getIds();
        printf('\n \n get values ok');
        $sqlList = $this->prepareSecondaryTables();
        printf('\n \n finish prepare secondary Tables');
        $this->updateTables($sqlList,$values);
        printf('\n \n finish update secondary Tables');

        unset($values);
        unset ($sqlList);

    }

     public function refactorImgTables() {


        $values =  $this->getIds( " WHERE  `type` = 'photo' "  );
        printf('\n \n get values ok');
        $sqlList = $this->prepareImgTables();
        printf('\n \n finish prepare image Tables');
        $this->updateTables($sqlList,$values);
        printf('\n \n  finish update image Tables');

        unset($values);
        unset ($sqlList);

    }
    
    /**
     * Change openhost workers to master users
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function addMasterUsers() {
    //If there are some group with id=4
        $sql ="UPDATE  `user_groups` SET `pk_user_group` = '8' WHERE `pk_user_group` ='4'";
        $rss = $this->orig->conn->Execute($sql);
        if (!$rss) {
                $this->log( "\n- ".$sql." - ".$this->orig->conn->ErrorMsg() );
        } else{
            $sql2="UPDATE `users` SET `fk_user_group` = '8' WHERE `users`.`fk_user_group` ='4'";
            $rss = $this->orig->conn->Execute($sql2);
            if (!$rss) {
                $this->log( "\n- ".$sql." - ".$this->orig->conn->ErrorMsg() );
            }
        }
    //add Masters
        $sqlInsert= "INSERT INTO `user_groups` (`pk_user_group` ,`name`)".
                    "VALUES (4 , 'Masters' )";
        $rss = $this->orig->conn->Execute($sqlInsert);
        if (!$rss) {
                $this->log( "\n- ".$sql." - ".$this->orig->conn->ErrorMsg() );
        }
        $sqlUpdate = " UPDATE `users` SET `fk_user_group` = '4' WHERE ".
                     "`users`.`login` ='macada' ".
                     " OR `users`.`login` ='alex'".
                     " OR `users`.`login` ='fran'".
                     " OR `users`.`login` ='sandra'";

        $rss = $this->orig->conn->Execute($sqlUpdate);
        if (!$rss) {
                $this->log( "\n- Masters no updated - ".$this->orig->conn->ErrorMsg() );
        }

    }


    /**
     *  Write in log file
     */
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
 