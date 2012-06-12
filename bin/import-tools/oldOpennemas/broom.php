<?php

/**
 * Clear content data which can't refactor id's
 *
 * @author sandra
 */

class Broom
{

    public function __construct($config = array())
    {
        $this->logFile  = "log-broom.txt";
        $this->dbConfig = $config;
        $handle         = fopen($this->logFile, "wb");
        fclose($handle);

        if (isset($config['bd_host'])
            && isset($config['bd_database'])
            && isset($config['bd_user'])
            && isset($config['bd_pass'])
            && isset($config['bd_type'])) {

            $this->orig->conn = ADONewConnection($config['bd_type']);
            $this->orig->conn->PConnect($config['bd_host'], $config['bd_user'],
                                    $config['bd_pass'], $config['bd_database']);

        } else {
            printf("ERROR: You must provide the connection configuration to the database");
            die();
        }

    }

    /**
     * Get data before drop  contents.
     * This contents can't refactor id's
     */

    public function writeDataInLog()
    {

        //$value = "200701010000000000";
        $value = "201102010000000000"; //tribuna was in 03-2011

        $sqls   = array();
        $sqls[] = "SELECT * FROM `contents_categories` WHERE ".
                    "`contents_categories`.`pk_fk_content` >".$value;
        $sqls[] = 'SELECT * FROM contents  WHERE `pk_content` > '.$value;
        $sqls[] = 'SELECT * FROM articles  WHERE `pk_article`  > '.$value .
                    '  OR `img1`  > '.$value .'  OR `img2`  > '.$value;
        $sqls[] = 'SELECT * FROM opinions  WHERE `pk_opinion`  > '.$value;
        $sqls[] = 'SELECT * FROM advertisements  WHERE `pk_advertisement` > '.$value;
        $sqls[] = 'SELECT * FROM albums  WHERE `pk_album`  > '.$value;
        $sqls[] = 'SELECT * FROM albums_photos  WHERE `pk_album`  > '.$value.
                    '  OR `pk_photo`  > '.$value;
        $sqls[] = 'SELECT * FROM videos  WHERE `pk_video`  > '.$value;
        $sqls[] = 'SELECT * FROM photos  WHERE `pk_photo`  > '.$value;
        $sqls[] = 'SELECT * FROM comments  WHERE `pk_comment`  > '.$value;
        $sqls[] = 'SELECT * FROM votes  WHERE `pk_vote`  > '.$value;
        $sqls[] = 'SELECT * FROM ratings  WHERE `pk_rating`  > '.$value;
        $sqls[] = 'SELECT * FROM attachments  WHERE `pk_attachment`  > '.$value;
        $sqls[] = 'SELECT * FROM polls  WHERE `pk_poll`  > '.$value;
        $sqls[] = 'SELECT * FROM poll_items  WHERE `fk_pk_poll`  > '.$value;
        $sqls[] = 'SELECT * FROM related_contents  WHERE `pk_content1` > '.$value.
                     '   OR `pk_content2`  > '.$value;
        $sqls[] = 'SELECT * FROM kioskos  WHERE `pk_kiosko`  > '.$value;
        $sqls[] = 'SELECT * FROM static_pages  WHERE `pk_static_page` > '.$value;

        foreach ($sqls as $sql) {
            $rs = $this->orig->conn->Execute($sql);
            $this->log("GET content data before drop ". $sql. " \n");
            while (!$rs->EOF) {
                $this->log(" -  ". json_encode($rs->fields). " \n");
                $rs->MoveNext();
            }
            $this->log("\n\n");

        }

        return  true;

    }

    /**
     * Delete dirty fields from all tables.
     *
     */

    public function clearExecute()
    {
        $value  = "201102010000000000";

        $sqls   = array();
        $sqls[] = "DELETE FROM `contents_categories` WHERE ".
                    " `contents_categories`.`pk_fk_content` >".$value;
        $sqls[] = 'DELETE FROM contents  WHERE `pk_content` > '.$value;
        $sqls[] = 'DELETE FROM articles  WHERE `pk_article`  > '.$value;
        $sqls[] = 'DELETE FROM opinions  WHERE `pk_opinion`  > '.$value;
        $sqls[] = 'DELETE FROM advertisements WHERE `pk_advertisement`  > '.$value;
        $sqls[] = 'DELETE FROM albums  WHERE `pk_album`  > '.$value;
        $sqls[] = 'DELETE FROM albums_photos  WHERE `pk_album`  > '.$value.
                    '  OR `pk_photo`  > '.$value;
        $sqls[] = 'DELETE FROM videos  WHERE `pk_video`  > '.$value;
        $sqls[] = 'DELETE FROM photos  WHERE `pk_photo`  > '.$value;
        $sqls[] = 'DELETE FROM comments  WHERE `pk_comment`  > '.$value;
        $sqls[] = 'DELETE FROM votes  WHERE `pk_vote`  > '.$value;
        $sqls[] = 'DELETE FROM ratings  WHERE `pk_rating`  > '.$value;
        $sqls[] = 'DELETE FROM attachments  WHERE `pk_attachment`  > '.$value;
        $sqls[] = 'DELETE FROM polls  WHERE `pk_poll`  > '.$value;
        $sqls[] = 'DELETE FROM poll_items  WHERE `fk_pk_poll`  > '.$value;
        $sqls[] = 'DELETE FROM related_contents  WHERE `pk_content1` > '.$value.
                    '   OR `pk_content2`  > '.$value;
        $sqls[] = 'DELETE FROM kioskos  WHERE `pk_kiosko`  > '.$value;
        $sqls[] = 'DELETE FROM static_pages  WHERE `pk_static_page`  > '.$value;

        $fail = true;
        foreach ($sqls as $sql) {
            $rss = $this->orig->conn->Execute($sql);
            if (!$rss) {
                printf($this->orig->conn->ErrorMsg());
                $this->log($sql."-". $this->orig->conn->ErrorMsg());
                $fail = false;
            }
        }
        return $fail;

    }

    public function alterAutoincrements()
    {
        $sqlList = array();

        $sqlList[] = "ALTER TABLE `contents` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `advertisements` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `albums` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `attachments` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `opinions` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `photos` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `polls` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `ratings` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `videos` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `votes` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `widgets` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `videos` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `votes` AUTO_INCREMENT =1";
        $sqlList[] = "ALTER TABLE `widgets` AUTO_INCREMENT =1";

        foreach ($sqlList as $sql) {

            $rss = $this->orig->conn->Execute($sql, $values);
            if (!$rss) {
                $error = "\n- ".$sql." - ".$values." - ".$this->orig->conn->ErrorMsg() ;
                $this->log('-'.$error);
                printf('\n-'.$error);
            }
        }

        $rss->Close();
    }
    /**
     *  Write in log file
     */

    public function log($text = null)
    {
        if (isset($text) && !is_null($text)) {
            $handle = fopen($this->logFile, "a");

            if ($handle) {
                $datawritten = fwrite($handle, $text);
                fclose($handle);
            } else {
                echo "There was a problem while trying to log your message.";
            }
        }
    }
}
