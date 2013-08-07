<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ContentsImporter
{

    public static $originConn = '';

    public $idsMatches = array();

    public $categoriesMatches = array();

    public $categoriesData = array();

    public $helper = null;

    public function __construct ($configOldDB = array(), $configNewDB = array())
    {

        // Application::initInternalConstants();

        self::initDatabaseConnections($configOldDB, $configNewDB);
        $this->helper = new OnmHelper();

    }

    public static function initDatabaseConnections($configOriginDB = array(), $configNewDB = array())
    {

        echo "Initialicing source database connection...".PHP_EOL;
        if (isset($configOriginDB['host'])
            && isset($configOriginDB['database'])
            && isset($configOriginDB['user'])
            && isset($configOriginDB['password'])
            && isset($configOriginDB['type'])) {

            self::$originConn = ADONewConnection($configOriginDB['type']);
            self::$originConn->PConnect(
                $configOriginDB['host'],
                $configOriginDB['user'],
                $configOriginDB['password'],
                $configOriginDB['database']
            );
        }

        echo "Initialicing Onm database connection...".PHP_EOL;
        define('BD_HOST', $configNewDB['host']);
        define('BD_USER', $configNewDB['user']);
        define('BD_PASS', $configNewDB['password']);
        define('BD_TYPE', $configNewDB['type']);
        define('BD_DATABASE', $configNewDB['database']);

        $GLOBALS['application'] = new Application();

        Application::initDatabase();
    }


    public function importOpinions()
    {

        echo "IMPORTING OPINIONS\n";
        $sql ="SELECT * FROM opinions, contents WHERE in_litter=0 ".
                " AND pk_opinion = pk_content";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $opinion = new Opinion();

        if (!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            $authorData = array();
            while (!$rs->EOF) {
                 $originalOpinionID = $rs->fields['pk_opinion'];

                if ($this->helper->elementIsImported($originalOpinionID, 'opinion') ) {
                    echo "[{$current}/{$totalRows}] Opinion with id {$originalOpinionID} already imported\n";
                } else {

                    //Check opinion data
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$originalOpinionID} - ";
                    $values =
                        array(
                            'title'                => $rs->fields['title'],
                            'category'             => '4',
                            'category_name'        => 'opinion',
                            'type_opinion'         => $rs->fields['type_opinion'],
                            'body'                 => $rs->fields['body'],
                            'description'          => $rs->fields['description'],
                            'fk_author'            => $rs->fields['fk_author'],
                            'fk_author_img'        => $rs->fields['fk_author_img'],
                            'fk_author_img_widget' => $rs->fields['fk_author_img_widget'],
                            'available'            => $rs->fields['available'],
                            'with_comment'         => $rs->fields['with_comment'],
                            'in_home'              => $rs->fields['in_home'],
                            'content_status'       => $rs->fields['content_status'],
                            'created'              => $rs->fields['created'],
                            'starttime'            => $rs->fields['starttime'],
                            'changed'              => $rs->fields['changed'],
                            'fk_user'              => $rs->fields['fk_user'],
                            'fk_publisher'         => $rs->fields['fk_publisher'],
                            'fk_user_last_editor'  => $rs->fields['fk_user_last_editor'],
                            'views'                => $rs->fields['views'],
                            'frontpage'            => $rs->fields['frontpage'],
                            'slug'                 => $rs->fields['slug'],
                            'metadata'             => $rs->fields['metadata'],
                        );

                    $opinion = new Opinion();
                    $newOpinionID = $opinion->create($values);

                    if (is_string($newOpinionID)) {
                        $this->helper->insertRefactorID($originalOpinionID, $newOpinionID, 'opinion');
                    } else {
                        $this->helper->log("\n Problem opinion {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                    $this->helper->updateViews($newOpinionID, $rs->fields['views']);
                    echo "new id {$newOpinionID} [DONE]\n";
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }

    public function updateOpinionAuthors()
    {
        echo "IMPORTING OPINIONS\n";
        $sql ="SELECT pk_opinion, fk_author FROM opinions";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $opinion = new Opinion();

        if (!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            $authorData = array();
            while (!$rs->EOF) {
                $originalOpinionID = $rs->fields['pk_opinion'];
                $newOpinionID = $this->helper->elementTranslate($originalOpinionID);
                if (!empty($newOpinionID)) {
                    $sql = 'UPDATE `opinions` SET `fk_author`=? WHERE pk_opinion=?';

                    $values = array($rs->fields['fk_author'],  $newOpinionID);
                    $update_sql = $GLOBALS['application']->conn->Prepare($sql);
                    $rss = $GLOBALS['application']->conn->Execute($update_sql, $values);
                    if (!$rss) {
                        echo $GLOBALS['application']->conn->ErrorMsg();
                    }
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }

    public function importNewsstand()
    {
        echo "IMPORTING NEWSSTAND\n";
        $sql ="SELECT * FROM kioskos, contents_categories, contents WHERE in_litter=0 ".
                " AND pk_kiosko = pk_content AND pk_fk_content = pk_content";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $opinion = new Opinion();

        if (!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            $authorData = array();
            while (!$rs->EOF) {
                 $originalID = $rs->fields['pk_kiosko'];

                if ($this->helper->elementIsImported($originalID, 'kiosko') ) {
                    echo "[{$current}/{$totalRows}] kiosko with id {$originalOpinionID} already imported\n";
                } else {

                    //Check opinion data
                    echo "[{$current}/{$totalRows}] Importing kiosko with id {$originalID} - ";
                    $values =
                        array(
                            'title'                => $rs->fields['title'],
                            'category'             => $rs->fields['pk_fk_content_category'],
                            'content_type'         => $rs->fields['content_type'],
                            'category_name'        => $rs->fields['catName'],
                            'description'          => $rs->fields['description'],
                            'available'            => $rs->fields['available'],
                            'in_home'              => $rs->fields['in_home'],
                            'content_status'       => $rs->fields['content_status'],
                            'created'              => $rs->fields['created'],
                            'starttime'            => $rs->fields['starttime'],
                            'changed'              => $rs->fields['changed'],
                            'fk_user'              => $rs->fields['fk_user'],
                            'fk_publisher'         => $rs->fields['fk_publisher'],
                            'fk_user_last_editor'  => $rs->fields['fk_user_last_editor'],
                            'views'                => $rs->fields['views'],
                            'favorite'             => $rs->fields['favorite'],
                            'slug'                 => $rs->fields['slug'],
                            'metadata'             => $rs->fields['metadata'],
                            'name'                 => $rs->fields['name'],
                            'path'                 => $rs->fields['path'],
                            'date'                 => $rs->fields['date'],
                            'price'                => $rs->fields['price'],
                            'type'                 => $rs->fields['type'],
                        );

                    $kiosko = new Kiosko();
                    $newID = $kiosko->create($values);

                    if (is_string($newID)) {
                        $this->helper->insertRefactorID($originalID, $newID, 'kiosko');
                    } else {
                        $this->helper->log("\n Problem kiosko {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                    $this->helper->updateViews($newID, $rs->fields['views']);
                    echo "new id {$newID} [DONE]\n";
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }
}

