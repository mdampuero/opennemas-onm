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

class Broom {

      public function __construct ($config = array())
    {
        $this->logFile ="log-broom.txt";
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
        $value = "200701010000000000";

        $sqls = array();
        $sqls[] ="DELETE FROM `contents_categories` WHERE `contents_categories`.`pk_fk_content` >".$value;
        $sqls []= 'DELETE FROM contents  WHERE `pk_content` > '.$value;
        $sqls []= 'DELETE FROM articles  WHERE `pk_article`  > '.$value;
        $sqls []= 'DELETE FROM opinions  WHERE `pk_opinion`  > '.$value;
        $sqls []= 'DELETE FROM advertisements  WHERE `pk_advertisement`  > '.$value;
        $sqls []= 'DELETE FROM albums  WHERE `pk_album`  > '.$value;
        $sqls []= 'DELETE FROM albums_photos  WHERE `pk_album`  > '.$value.'  OR `pk_photo`  > '.$value;
        $sqls []= 'DELETE FROM videos  WHERE `pk_video`  > '.$value;
        $sqls []= 'DELETE FROM photos  WHERE `pk_photo`  > '.$value;
        $sqls []= 'DELETE FROM comments  WHERE `pk_comment`  > '.$value;
        $sqls []= 'DELETE FROM votes  WHERE `pk_vote`  > '.$value;
        $sqls []= 'DELETE FROM ratings  WHERE `pk_rating`  > '.$value;
        $sqls []= 'DELETE FROM attachments  WHERE `pk_attachment`  > '.$value;
        $sqls []= 'DELETE FROM polls  WHERE `pk_poll`  > '.$value;
        $sqls []= 'DELETE FROM poll_items  WHERE `fk_pk_poll`  > '.$value;
        $sqls []= 'DELETE FROM related_contents  WHERE `pk_content1` > '.$value. '   OR `pk_content2`  > '.$value;
        $sqls []= 'DELETE FROM kioskos  WHERE `pk_kiosko`  > '.$value;
        $sqls []= 'DELETE FROM static_pages  WHERE `pk_static_page`  > '.$value;

        $fail = true;
        foreach ($sqls as $sql) {
            $rss = $this->orig->conn->Execute($sql);
            if (!$rss) {
                    printf( $this->orig->conn->ErrorMsg() );
                    $this->log($sql."-". $this->orig->conn->ErrorMsg() );
                    $fail= false;
                }
           

        }
        return $fail;

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
 