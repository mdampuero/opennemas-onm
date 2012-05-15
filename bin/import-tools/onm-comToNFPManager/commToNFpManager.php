<?php

/**
 * Added contents in content_positions table
 * Change startime in content table
 *
 * @author sandra
 */
class CommToNFpManager {

	public function modifySchema() {

	        $sql="UPDATE `contents` SET `starttime`=created WHERE `starttime`='0000-00-00 00:00:00'";

	        $rss = $GLOBALS['application']->conn->Execute($sql);
	        if (!$rss) {
	            printf(    "ERROR: Can't modify database");
	            die();
	        }
	}


    public function updateFrontpageArticles() {

        $sql = "SELECT pk_content, pk_fk_content_category, placeholder, position ".
                " FROM contents, contents_categories ".
                " WHERE frontpage=1 AND contents.fk_content_type=1 ".
                " AND pk_content = pk_fk_content AND content_status=1 AND available=1 ";

        $rs =  $GLOBALS['application']->conn->Execute($sql);

        $values= array();
        // contents in section frontpages
        while(!$rs->EOF) {
            $values[] =  array(
                                $rs->fields['pk_content'],
                                $rs->fields['pk_fk_content_category'],
                                $rs->fields['placeholder'],
                                $rs->fields['position'],
                                NULL,
                                'Article'
                   );

            $rs->MoveNext();
        }

        // contents for home frontpage
        $sql = "SELECT pk_content, pk_fk_content_category, home_placeholder, home_pos ".
                " FROM contents, contents_categories ".
                " WHERE in_home=1 AND frontpage=1  AND contents.fk_content_type=1 ".
                " AND pk_content = pk_fk_content AND content_status=1 AND available=1 ";

        $rs =   $GLOBALS['application']->conn->Execute($sql);

        while(!$rs->EOF) {
            $values[] =  array(
                            $rs->fields['pk_content'],
                            0,
                            $rs->fields['home_placeholder'],
                            $rs->fields['home_pos'],
                            NULL,
                            'Article',
                   );

            $rs->MoveNext();
        }

        $rs->Close();

        //Insert articles in table content_positions
        $sql= "INSERT INTO `content_positions` ".
              " (`pk_fk_content`, `fk_category`, `placeholder`, `position`, `params`, `content_type`)".
              " VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($stmt, $values);

        if (!$rss) {
            $error =  "\n-  ".$sql." - ".$values." - ".$this->orig->conn->ErrorMsg() ;
            $this->log('-'.$error);
            printf('\n-'.$error);
        }else{
            printf('\n- Articles are added in frontpages section & home \n');
        }


    }
}