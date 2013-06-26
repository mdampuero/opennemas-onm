<?php
class WordPressToOnm
{
    const CONF_FILE = 'source/config-data.ini';

    public static $configuration = '';

    public static $originMedia ='';

    public static $originConn = '';

    public static $reflector;

    public $idsMatches = array();

    public $originalCategories = array();

    public $categoriesOpinion = array();

    public function __construct ()
    {

        self::loadConfiguration();

        define('INSTANCE_UNIQUE_NAME', self::$configuration['instanceData']['INSTANCE_UNIQUE_NAME']);

        Application::initInternalConstants();

        self::initDatabaseConnections();

        $this->originalCategories = self::$configuration['categories'];
        self::$originMedia = self::$configuration['media']['directory'];
    }


    /**
     * Parses the configuration file and loads it
     *
     * @return void
     **/
    public static function loadConfiguration()
    {
        echo "Loading script configuration...".PHP_EOL;
        $configurationFile = realpath(__DIR__.'/../'.self::CONF_FILE);
        if (!is_file($configurationFile)) {
            echo "Configuration file doesn't exists $configurationFile".PHP_EOL;
            die();
        }
        self::$configuration = parse_ini_file($configurationFile, true);


    }


    public static function initDatabaseConnections()
    {
        $configOriginDB = self::$configuration['original-database'];
        echo "Initialicing source database connection...".PHP_EOL;
        if (isset($configOriginDB['host'])
            && isset($configOriginDB['database'])
            && isset($configOriginDB['user'])
            && isset($configOriginDB['password'])
            && isset($configOriginDB['type'])
        ) {

            self::$originConn = ADONewConnection($configOriginDB['type']);
            self::$originConn->PConnect(
                $configOriginDB['host'],
                $configOriginDB['user'],
                $configOriginDB['password'],
                $configOriginDB['database']
            );
        }

        $config = self::$configuration['onm-database'];
        echo "Initialicing Onm database connection...".PHP_EOL;
        define('BD_HOST', $config['host']);
        define('BD_USER', $config['user']);
        define('BD_PASS', $config['password']);
        define('BD_TYPE', $config['type']);
        define('BD_DATABASE', $config['database']);

        $GLOBALS['application'] = new Application();
        Application::initDatabase();
        Application::initLogger();
    }



    /*************CATEGORIES MANAGE*************************/

    public function importCategories()
    {

        $ih = new ImportHelper();


        foreach ($this->originalCategories as $originalID => $newID) {

            if ($ih->elementIsImported($originalID, 'category')) {
                echo "Category with id {$originalID} already imported\n";
            } else {
                if ($newID==0) {
                    $newID = $this->insertNewCategory($originalID);
                    $this->originalCategories[$originalID]= $newID;
                }
                echo "Importing category with id {$originalID} - ";
                $ih->logElementInsert($originalID, $newID, 'category');
                echo "new id {$newID} [DONE]\n";
            }

        }

        $this->categoriesOpinion = $this->getOpinionCategories();

    }

    /**
     * Fetches the original categories
     *
     **/
    public function getOriginalCategories()
    {
        return array_keys(self::$configuration['categories']);
    }

    /**
     * Fetches the opinion categories
     *
     **/
    public function getOpinionCategories()
    {
        $categoriesOpinion= array();
        foreach ($this->originalCategories as $originalID => $newID) {
            if ($newID =='4') {
                $categoriesOpinion[$originalID] = $newID;
            }
        }
        return $categoriesOpinion;
    }


    /**
     * Fetches the target categories
     *
     **/
    public function getTargetCategories()
    {
        return array_values(self::$configuration['categories']);
    }


    public function matchCategory($category)
    {

        return $this->originalCategories[$category];

    }

    public function insertNewCategory($originalID)
    {

        $categories = self::$configuration['newCategories'];
        foreach ($categories as $key => $categoryData) {
            if ($key == $originalID) {
                $elem= explode(':', $categoryData);

                $data = array(
                    'title'             => $elem[0],
                    'name'              => (StringUtils::get_title(ImportHelper::convertoUTF8($elem[0]))),
                    'inmenu'            => 1,
                    'internal_category' => 0,
                    'subcategory'       => $this->matchCategory($elem[1]),
                );

                $data['params']['title']  = $elem[0];
                $data['params']['inrss']  = 1;

                $category = new ContentCategory();

                if ($category->create($data)) {
                    echo "Creating category with id {$originalID} - newid: {$category->pk_content_category} ";
                    $user = new User();
                    $user->addCategoryToUser(self::$configuration['idUser']['id'], $category->pk_content_category);

                    return $category->pk_content_category;
                }
            }
        }
    }

    public function importArticles()
    {

        $_sql_where = ' `wp_term_relationships`.`term_taxonomy_id` IN ('
            .implode(', ', array_keys($this->originalCategories)).") ";
        $_limit = '';

        $sql = "SELECT * FROM `wp_posts`, `wp_term_relationships` WHERE ".
            "`post_type` = 'post' AND `ID`=`object_id` AND post_status='publish' ".
            " AND ".$_sql_where." ".$_limit;

        // Fetch the list of Articles available in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['ID'], 'article')) {
                    echo "[{$current}/{$totalRows}] Article with id {$rs->fields['ID']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$rs->fields['ID']} - ";

                    $originalArticleID = $rs->fields['ID'];
                    $data = $this->clearLabelsInBodyArticle($rs->fields['post_content']);
                    $values = array(
                        'title'          => ImportHelper::convertoUTF8($rs->fields['post_title']),
                        'category'       => $this->matchCategory($rs->fields['term_taxonomy_id']),
                        'with_comment'   => 1,
                        'available'      => 1,
                        'content_status' => 0,
                        'frontpage'      => 0,
                        'in_home'        => 0,
                        'title_int'      => ImportHelper::convertoUTF8($rs->fields['post_title']),
                        'metadata'       => StringUtils::get_tags(
                            ImportHelper::convertoUTF8($rs->fields['post_title'])
                        ),
                        'subtitle'       => '',
                        'agency'         => self::$configuration['agency']['name'],
                        'summary'        => substr($data['body'], 0, 250 . '...'),
                        'description'    => strip_tags(substr($data['body'], 0, 150)),
                        'body'           => $data['body'],
                        'posic'          => 0,
                        'id'             => 0,
                        'img1'           =>$data['img'],
                        'img2'           =>$data['img'],
                        'created'        => $rs->fields['post_date'],
                        'starttime'      => $rs->fields['post_date'],
                        'changed'        => $rs->fields['post_modified'],
                        'fk_user'        => self::$configuration['idUser']['id'],
                        'fk_publisher'   => self::$configuration['idUser']['id'],
                    );

                    $article = new Article();
                    $newArticleID = $article->create($values);
                    if (is_string($newArticleID)) {

                        $ih->logElementInsert($originalArticleID, $newArticleID, 'article');

                    }
                    echo "new id {$newArticleID} [DONE]\n";
                    //ImportHelper::messageStatus("Importing Articles: $current/$totalRows");
                    //sleep(0.12);
                }

                $current++;

                $rs->MoveNext();
            }

            $rs->Close(); # optional

        }

    }

    public function importOpinions()
    {
        echo "IMPORTING OPINIONS\n";
        $_filter_by_section = ' `wp_term_relationships`.`term_taxonomy_id` IN ('.
                implode(', ', array_keys($this->categoriesOpinion)).") ";

        $sql = "SELECT * FROM `wp_posts`, `wp_term_relationships` WHERE ".
        "`post_type` = 'post' AND `ID`=`object_id` ".
        " AND ".$_filter_by_section;

        // Fetch the list of Opinions available for one author in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['ID'], 'opinion')) {
                    echo "[{$current}/{$totalRows}] Opinion with id {$rs->fields['ID']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$rs->fields['ID']} - ";

                    $originalOpinionID = $rs->fields['ID'];
                    $data = $this->clearLabelsInBodyArticle($rs->fields['post_content']); //extract image html in body
                    $values =
                        array(
                            'title' => ImportHelper::convertoUTF8($rs->fields['post_title']),
                            'category' => '4',
                            'type_opinion' => self::$configuration['opinion']['typeOpinion'],
                            'body' => $data['body'],
                            'metadata' => strip_tags(
                                StringUtils::get_tags(ImportHelper::convertoUTF8($rs->fields['post_title']))
                            ),
                            'description' => strip_tags(substr($data['body'], 0, 150)),
                            'fk_author' => self::$configuration['opinion']['authorOpinion'],
                            'available' => 1,
                            'with_comment' => 1,
                            'in_home' => 0,
                            'content_status' => 1,
                            'created' => $rs->fields['post_date'],
                            'starttime' => $rs->fields['post_date'],
                            'changed' => $rs->fields['post_modified'],
                            'fk_user' => self::$configuration['idUser']['id'],
                            'fk_publisher' => self::$configuration['idUser']['id'],
                        );



                    $opinion = new Opinion();
                    $newOpinionID = $opinion->create($values);

                    if (is_string($newOpinionID)) {
                        $ih->logElementInsert($originalOpinionID, $newOpinionID, 'opinion');

                    }
                    echo "new id {$newOpinionID} [DONE]\n";
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

    public function importImages()
    {
        echo "IMPORTING IMAGES\n";
        if (!isset(self::$configuration['mediaCategory'])) {
            $data = array(
                'title'=> 'WP Old',
                'name' => 'wp-old',
                'inmenu'=> 1,
                'internal_category' => 7,
                'fk_content_category' => '0',
                );

            $data['params']['title']  = 'Imagenes WordPress';
            $data['params']['inrss']  = 0;

            $category = new ContentCategory();
            $category->create($data);
            $IDCategory = $category->pk_content_category;
        } else {
             $IDCategory =self::$configuration['mediaCategory'];
        }

        if (!$IDCategory) {
            echo "Needs category id for images. can't import images\n";
            die();
        }
        echo "Created new category WP Old with {$IDCategory} id, for import images\n";


        $sql = "SELECT * FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ";

        // Fetch the list of post type attachment = images
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                if ($ih->elementIsImported($rs->fields['ID'], 'image')) {
                    echo "[{$current}/{$totalRows}] image with id {$rs->fields['ID']} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing image with id {$rs->fields['ID']} - ";

                    $originalImageID = $rs->fields['ID'];

                    $local_file =
                        str_replace('http://www.correodiplomatico.com/wp-content/uploads', '', $rs->fields['guid']);

                    $values = array(
                        'title' => ImportHelper::convertoUTF8(strip_tags($rs->fields['post_title'])),
                        'category' => $IDCategory,
                        'fk_category' => $IDCategory,
                        'category_name'=> '',
                        'content_status' => 1,
                        'frontpage' => 0,
                        'in_home' => 0,
                        'metadata' => StringUtils::get_tags(
                            ImportHelper::convertoUTF8($rs->fields['post_title'].$rs->fields['post_excerpt'])
                        ),
                        'description' => ImportHelper::convertoUTF8(
                            strip_tags(substr($rs->fields['post_excerpt'], 0, 150))
                        ),
                        'id' => 0,
                        'created' => $rs->fields['post_date'],
                        'starttime' => $rs->fields['post_date'],
                        'changed' => $rs->fields['post_modified'],
                        'fk_user' => self::$configuration['idUser']['id'],
                        'fk_author' => self::$configuration['idUser']['id'],
                        'local_file' => self::$originMedia.$local_file,
                    );

                    $image = new Photo();
                    $newimageID= $image->createFromLocalFile($values);
                    if (is_string($newimageID)) {

                        $ih->logElementInsert($originalImageID, $newimageID, 'image');

                    }
                    echo "new id {$newimageID} [DONE]\n";
                    //ImportHelper::messageStatus("Importing Articles: $current/$totalRows");
                    //sleep(0.12);
                }
                $current++;

                $rs->MoveNext();
            }

            $rs->Close(); # optional
        }
    }


    public function importComments()
    {
        echo "IMPORTING COMMENTS\n";

        $sql = "SELECT * FROM `wp_comments`, `wp_posts` WHERE ".
        " `comment_approved`=1 AND `ID`=`comment_post_ID` ";

        // Fetch the list of Opinions available for one author in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            $ih = new ImportHelper();

            while (!$rs->EOF) {

                $originalCommentID = $rs->fields['comment_ID'];

                if ($ih->elementIsImported($originalCommentID, 'comment')) {
                    echo "[{$current}/{$totalRows}] Comments with id {$originalCommentID} already imported\n";
                } else {

                    $articleID =$ih->elementIsImported($rs->fields['comment_post_ID'], 'article');
                    $ccm = ContentCategoryManager::get_instance();

                    if (!empty($articleID)) {
                        $article= new Article($articleID);
                        if (!empty($article->pk_content)) {

                            echo "[{$current}/{$totalRows}] Importing comment with id {$originalCommentID} - ";

                            $comment = ImportHelper::convertoUTF8(strip_tags($rs->fields['comment_content']));
                            $title   =  ImportHelper::convertoUTF8(substr($comment, 0, 100));
                            $data = array(
                                'title' => $title,
                                'category' => $article->category,
                                'category_name' =>$article->loadCategoryName($articleID),
                                'body' =>  strip_tags($rs->fields['comment_content']),
                                'metadata' => StringUtils::get_tags($title),
                                'description' => substr($comment, 0, 150),
                                'content_status' => 1,
                                'created' => $rs->fields['comment_date'],
                                'starttime' => $rs->fields['comment_date'],
                                'changed' => $rs->fields['comment_date'],
                                'published' => $rs->fields['comment_date'],
                                'fk_publisher' => self::$configuration['idUser']['id'],
                                'fk_user' => self::$configuration['idUser']['id'],
                                'author' => ImportHelper::convertoUTF8(strip_tags($rs->fields['comment_author'])),
                                'ip'=>$rs->fields['comment_author_IP'],
                                'email'=>ImportHelper::convertoUTF8($rs->fields['comment_author_email']),
                                'ciudad'=>'',
                            );

                            $values = array(
                                'id' =>   $articleID,
                                'data' => $data,
                                'ip' =>   $rs->fields['comment_author_IP'],
                            );

                            $comment = new Comment();
                            $newCommentID = $comment->create($values);

                            if (is_string($newCommentID)) {
                                $ih->logElementInsert($originalCommentID, $newCommentID, 'comment');
                                // $ih->updateCreateDate($newOpinionID, $rs->fields['fecha']);
                            }
                            echo "new id {$newCommentID} [DONE]\n";
                        }
                    }
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }

    public function getOnmIdImage($guid)
    {
          $sql = "SELECT ID FROM `wp_posts` WHERE ".
            "`post_type` = 'attachment'  AND post_status !='trash' ".
            " AND guid= '".$guid."'";

        // Fetch the list of Opinions available for one author in EditMaker
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $imageID='';
        if (!$rs) {
            ImportHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $ih = new ImportHelper();
            $imageID = $ih->elementIsImported($rs->fields['ID'], 'image');

        }
        return $imageID;

    }

    public function clearLabelsInBodyArticle($body)
    {

        $result = array();

        $newBody='';
        $img='';
        preg_match_all('/(<a .*?href="(.+?)".*?><img .*?src=".+?".*?><\/a>)/', $body, $result);
        if (!empty($result)) {
            $guid = $result[2][0];

            $newBody = preg_replace('/(<a .*?href="(.+?)".*?><img .*?src=".+?".*?><\/a>)/', '', $body);
            $newBody = ImportHelper::convertoUTF8(strip_tags($newBody, '<p><a><br>'));
            $img     = self::getOnmIdImage($guid);
        }
        $newBody= preg_replace('/(\[caption .*?\]\[\/caption\])/', '', $newBody);
        return array('img'=>$img, 'body'=>$newBody);

    }

    public function printResults()
    {
        ImportHelper::printResults();
    }
}
