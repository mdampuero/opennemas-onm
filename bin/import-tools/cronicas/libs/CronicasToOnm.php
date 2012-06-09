<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


class CronicasToOnm {

    public static $originConn = '';

    public $idsMatches = array();

    public $categoriesMatches = array();

    public $categoriesData = array();

    public $helper = null;

    public function __construct ($configOldDB=array(),$configNewDB=array())
    {

        Application::initInternalConstants();

        self::initDatabaseConnections($configOldDB,$configNewDB);
        $this->helper = new CronicasHelper();

    }

    public static function initDatabaseConnections($configOriginDB=array(),$configNewDB=array())
    {

        echo "Initialicing source database connection...".PHP_EOL;
        if (isset($configOriginDB['host'])
            && isset($configOriginDB['database'])
            && isset($configOriginDB['user'])
            && isset($configOriginDB['password'])
            && isset($configOriginDB['type']))
        {

            self::$originConn = ADONewConnection($configOriginDB['type']);
            self::$originConn->PConnect(
                $configOriginDB['host'], $configOriginDB['user'],
                $configOriginDB['password'], $configOriginDB['database']
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
        Application::initLogger();
    }

  /**
     * Explanation for this function.
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function matchInternalCategory($category)
    {
        return $this->categoriesMatches[$category];

    }

    /**
     * Extract category data of old db and insert these in new DB.
     * Drop example categories in new DB.
     *
     * @param array $categories categories ids of used contents
     *
     * @return bool
     */

    public function importCategories()
    {
        //Drop example categories
      $this->categoriesMatches = array(
                1 => 21,   // Cronicas
                2 => 22,   // Galicia
                3 => 24,  // asturias
                4 => 28,   // canarias
                5 => 23,  // castillaleon
                6 => 27,   // cantabria
                7 => 25,  // madrid
                8 => 29,   // Baleares
                9 => 26,   // Andalucia
                10 => 4,  // opinion
                11 => 2,   // publi
                15 => 30,  // paisvasco
            );

      $this->categoriesData = ContentCategoryManager::get_instance()->categories;

      return false;

    }


    /* create new image */

    public function createImage($data, $dateForDirectory =NULL) {

        $oldPath = OLD_MEDIA.'images/'.$data['oldName'] ;

        $values = array(
                'title' =>$data['name'],
                'category' => $data['category'],
                'fk_category' => $data['category'],
                'category_name'=> $data['category_name'],
                'content_status' => 1,
                'frontpage' => 0,
                'in_home' => 0,
                'metadata' => $data['metadata'],
                'description' => $data['title'],
                'id' => 0,
                'created' => $data['created'],
                'starttime' => $data['created'],
                'changed' => $data['changed'],
                'fk_user' => USER_ID,
                'fk_author' => USER_ID,
                'local_file' => $oldPath,
            );

        $image = new Photo();
        if(is_null($dateForDirectory)) {
            $date = new DateTime($data['created']);
            $dateForDirectory = date_format($date, "/Y/m/d/");
        }
        $newimageID = $image->createFromLocalFile($values, $dateForDirectory);
        if(is_string($newimageID)) {

            $this->helper->insertImageTranslated($newimageID, $data['oldName'], 'image');

        } else{
            $this->helper->log(" Problem with image: {$oldPath} \n ");
        }

        echo "new id {$newimageID} [DONE]\n";
        return $newimageID;
    }
    /**
     * Insert image data in DB
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function importImagesArticles() {

        $sql = 'SELECT img1, img1_footer, img2, img2_footer, img3, img3_footer, '
              .' created, changed, title, pk_fk_content_category '
              .' FROM articles, contents, contents_categories '
              .'WHERE pk_article = pk_content AND pk_content = pk_fk_content';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while(!$rs->EOF) {
                for($i=1; $i<=3; $i++) {
                    $img= "img{$i}";
                    $footer= "{$img}_footer";
                    $name =$rs->fields[$img];
                    $title = $rs->fields[$footer];

                   // echo("$name, $title,  \n");

                    if(!empty($name)) {
                        $imageID = $this->helper->imageIsImported($name, 'image');

                        if(!empty($imageID)) {
                            echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                        } else {
                            echo "[{$current}/{$totalRows}] Importing image with id {$name} - ";
                            if(empty($title)) { // if empty footer ->get title
                                $title =$rs->fields['title'];
                            }
                            $category =$this->matchInternalCategory($rs->fields['pk_fk_content_category']);
                            $category_name = $this->categoriesData[$category]->name;
                            $slug =StringUtils::get_title($title);
                            $imageData = array(
                                'title' => $title,
                                'name' => $name,
                                'available' =>1,
                                'category'=> $category,
                                'category_name'=>  $category_name,
                                'metadata' => StringUtils::get_tags($slug.', '.$category_name),
                                'description' => $title.' '.$category_name,
                                'created' => $rs->fields['created'],
                                'changed' => $rs->fields['changed'],
                                'oldName' => $name,
                            );

                            $this->createImage($imageData );

                        }
                    }
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }


    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importArticles($limit) {

        $sql = 'SELECT * FROM articles, contents, contents_categories '
                .' WHERE contents.fk_content_type=1 AND contents.in_litter=0 AND contents.available=1 '
                .'AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .'AND  `articles`.`pk_article` = `contents`.`pk_content` '
                .$limit;

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs->_numOfRows;

             $current = 1;

             $article = new Article();
             while(!$rs->EOF) {

                $originalArticleID = $rs->fields['pk_article'];
                if ($this->helper->elementIsImported($originalArticleID, 'article') ) {
                    echo "[{$current}/{$totalRows}] Article with id {$originalArticleID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing article with id {$originalArticleID} - ";
                    $title= $rs->fields['title'];
                    $category_name = $rs->fields['catName'];
                    $data['params'] = array('titleHome'=>$rs->fields['title_home'],
                                        'subtitleHome'=>$rs->fields['subtitle_home'],
                                        'summaryHome'=>$rs->fields['summary_home'],
                                        'agencyBulletin'=> $rs->fields['agency_web'],
                                        );
                    $category = $this->matchInternalCategory($rs->fields['pk_fk_content_category']);
                    $data = array('title' => $rs->fields['title'],
                        'category' => $category,
                        'subtitle' => $rs->fields['subtitle'],
                        'agency' => $rs->fields['agency'],
                        'summary' => $rs->fields['summary'],
                        'body' => $rs->fields['body'],
                        'img1' =>$this->helper->imageIsImported($rs->fields['img1'],'image'),
                        'img1_footer' =>$rs->fields['img1_footer'],
                        'img2' =>$this->helper->imageIsImported($rs->fields['img2'],'image'),
                        'img2_footer' =>$rs->fields['img2_footer'],
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'frontpage' => $rs->fields['frontpage'],
                        'content_status' => $rs->fields['content_status'],
                        'available' => $rs->fields['available'],
                        'in_home' => $rs->fields['in_home'],
                        'position' => $rs->fields['position'],
                        'home_pos' => $rs->fields['home_pos'],
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'starttime' => $rs->fields['created'],
                        'views' => $rs->fields['views'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $rs->fields['slug'],
                        );

                    $articleID = $article->create($data);

                    if(!empty($articleID) ) {
                        $this->helper->insertRefactorID($originalArticleID, $articleID, 'article');
                        $this->helper->updateViews($articleID, $rs->fields['views'] );


                    }else{
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('\n insert article : '.$errorMsg);
                        $this->helper->log("\n Problem inserting article {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                }

                $current++;
                $rs->MoveNext();
             }

        }
        $rs->Close(); # optional
        return true;

    }

    /**
     * Insert author data & photos in old DB
     *
     * @param array $data author information
     *
     * @return bool
     *
     */
    public function importAuthorsOpinion()
    {

        $sql="SELECT distinct(`author`) FROM `opinions` WHERE `type_opinion`=0 ".
                "ORDER BY `opinions`.`author`  DESC";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            CronicasHelper::log('insertAuthor : '.self::$originConn->ErrorMsg());

            return false;
        }

        $author = new Author();

        while (!$rs->EOF) {
            $name = StringUtils::get_title($rs->fields['author']);
            $authorID = $this->helper->authorIsImported($name);
            if(!$authorID) {
                    $values = array(
                        'name' => $rs->fields['author'],
                        'fk_user' =>0,
                        'blog' =>'',
                        'politics' =>'',
                        'condition' =>$name,
                        'date_nac`' =>''
                    );

                    $authorID = $author->create($values);
                    echo "new id {$authorID} [DONE]\n";
                    $this->helper->insertAuthorTranslated($authorID, $name);

            }
            $rs->MoveNext();
        }
        $rs->Close(); # optional
        echo "\n Please Check duplicate entries or similar texts in author names. \n";
        return true;
    }

    public function importPhotoAuthorsOpinion()
    {

        $sql="SELECT distinct(`author`), img FROM `opinions` WHERE `type_opinion`=0 ".
                "ORDER BY `opinions`.`author`  DESC";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if (!$rs) {
            CronicasHelper::log('insertAuthor : '.self::$originConn->ErrorMsg());

            return false;
        }

        $author = new Author();

        while (!$rs->EOF) {
            $name = StringUtils::get_title($rs->fields['author']);
            $authorID = $this->helper->authorIsImported($name);

            if($rs->fields['img'] && !empty($authorID) ) {
                $photoID = $this->helper->imageIsImported($rs->fields['img'], 'image');
                $author = new Author($authorID);
                $authorName = StringUtils::get_title($author->name);
                if(empty($photoID)) {
                    $imageData = array(
                        'name' => $name,
                        'category'=> '7',
                        'title' =>$rs->fields['author'],
                        'category_name'=>'author',
                        'metadata' => StringUtils::get_tags($rs->fields['author']).', opinion',
                        'description' => $rs->fields['author'],
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'oldName' => $rs->fields['img'],
                    );

                    $photoID = $this->createImage($imageData, '/authors/'.$authorName.'/');
                    $photo = new Photo($photoID);
                    $photoPath = $photo->path_file.$photo->name;

                    $sql2 = "INSERT INTO author_imgs (`fk_author`, `fk_photo`,`path_img`)".
                                " VALUES ( ?, ?, ?)";
                    $values2 = array( $authorID, $photoID, $photoPath );
                    if(!empty($authorID) && !empty($photoID) ) {
                        $rs2 =$GLOBALS['application']->conn->Execute($sql2, $values2);
                        if(!$rs2) {
                            echo($sql2.' '. $GLOBALS['application']->conn->ErrorMsg());
                             $this->helper->log($sql2.' '. $GLOBALS['application']->conn->ErrorMsg()."\n ");
                        }
                    }

                }
            }
            $rs->MoveNext();
        }
        $rs->Close(); # optional
        return true;
    }

    public function importOpinions()
    {

        echo "IMPORTING OPINIONS\n";
        $sql ="SELECT * FROM opinions, contents WHERE in_litter=0 AND `type_opinion`=0".
                " AND pk_opinion = pk_content";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $opinion = new Opinion();

        if(!$rs) {
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

                    echo " Check author ";
                    $name = StringUtils::get_title($rs->fields['author']);
                    $authorData['id'] = $this->helper->authorIsImported($name);

                    if(empty($authorData['id'])) {
                        $values = array(
                            'name' => $rs->fields['author'],
                            'fk_user' =>0,
                            'blog' =>'',
                            'politics' =>'',
                            'condition' =>'',
                            'date_nac`' =>''
                        );
                        $newAuthor = new Author();
                        $authorData['id'] = $newAuthor->create($values);

                        echo "new id  {$authorData['id']}  [DONE]\n";
                        $this->helper->insertAuthorTranslated($authorData['id'], $name, 'author');
                        if(empty($authorData['id'])) {
                            echo "\n Author not exists {$name}\n ";
                             $this->helper->log("\n Author not exists {$name}\n ");
                        }
                    }
                     echo " Check image ";
                    $authorData['img'] = $this->helper->imageIsImported($rs->fields['img'], 'image');
                    if(empty($authorData['img'])) {

                        $sql2 = "SELECT pk_img FROM author_imgs WHERE fk_author = ? ORDER BY fk_photo DESC  LIMIT 1 ";
                        $values2 = array( $authorData['id'] );
                        $rs2 = $GLOBALS['application']->conn->Execute($sql2, $values2);
                        if(!$rs2) {
                            echo($sql2.' '. $GLOBALS['application']->conn->ErrorMsg());
                        }
                        $authorData['img'] = $rs2->fields['fk_photo'];
                    }
                    //Check opinion data
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$originalOpinionID} - ";
                    $values =
                        array(
                            'title' => $rs->fields['title'],
                            'category' => '4',
                            'category_name' => 'opinion',
                            'type_opinion' => 0,
                            'body' => $rs->fields['body'],
                            'metadata' =>  StringUtils::get_tags($rs->fields['author']).', '.$rs->fields['metadata'].', opinion',
                            'description' => 'opinion '.$rs->fields['author'].' '.strip_tags(substr($data['body'],0,100)),
                            'fk_author' => $authorData['id'],
                            'fk_author_img' =>$authorData['img'],
                            'fk_author_img_widget' =>$authorData['img'],
                            'available' => $rs->fields['available'],
                            'with_comment' => 0,
                            'in_home' => $rs->fields['in_home'],
                            'content_status' => $rs->fields['content_status'],
                            'created' => $rs->fields['created'],
                            'starttime' => $rs->fields['created'],
                            'changed' => $rs->fields['changed'],
                            'fk_user' => USER_ID,
                            'fk_publisher' => USER_ID,
                            'views' => $rs->fields['views'],
                            'frontpage' => $rs->fields['frontpage'],
                            'slug' => $rs->fields['slug'],

                        );

                    $opinion = new Opinion();
                    $newOpinionID = $opinion->create($values);

                    if(is_string($newOpinionID)) {

                        $this->helper->insertRefactorID($originalOpinionID, $newOpinionID, 'opinion');

                    } else{
                         $this->helper->log("\n Problem inserting opinion {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                    $this->helper->updateViews($newOpinionID, $rs->fields['views'] );
                    echo "new id {$newOpinionID} [DONE]\n";
                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }

    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importLetters() {

        echo "IMPORTING OPINIONS\n";
        $sql ="SELECT * FROM opinions, contents WHERE in_litter=0 AND `type_opinion` != 0 ".
                 " AND pk_opinion = pk_content";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $letter = new Letter();

        if(!$rs) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            while (!$rs->EOF) {
                $originalLetterID = $rs->fields['pk_opinion'];
                $name = StringUtils::get_title($rs->fields['author']);

                if ($this->helper->elementIsImported($originalLetterID, 'opinion') ) {
                    echo "[{$current}/{$totalRows}] Letter with id {$originalLetterID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing Letter with id {$originalLetterID} - ";

                    $values =
                        array(
                            'title' => $rs->fields['title'],
                            'category' => '0',
                            'category_name' => 'letter',
                            'email' => '',
                            'body' => $rs->fields['body'],
                            'metadata' =>  StringUtils::get_tags($rs->fields['author']).', '.$rs->fields['metadata'].', opinion',
                            'description' => 'opinion '.$rs->fields['author'].' '.strip_tags(substr($data['body'],0,100)),
                            'author' => $rs->fields['author'],
                            'available' => $rs->fields['available'],
                            'with_comment' => 0,
                            'in_home' => $rs->fields['in_home'],
                            'content_status' => $rs->fields['content_status'],
                            'created' => $rs->fields['created'],
                            'starttime' => $rs->fields['created'],
                            'changed' => $rs->fields['changed'],
                            'fk_user' => USER_ID,
                            'fk_publisher' => USER_ID,
                            'views' => $rs->fields['views'],
                            'frontpage' => $rs->fields['frontpage'],
                            'slug' => $rs->fields['slug'],

                        );

                    $letter = new Letter();
                    $newLetterID = $letter->create($values);

                    if(is_string($newLetterID)) {

                        $this->helper->insertRefactorID($originalLetterID, $newLetterID, 'letter');

                    } else{
                        $this->helper->log("\n Problem inserting letter {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                    $this->helper->updateViews($newLetterID, $rs->fields['views'] );
                    echo "new id {$newLetterID} [DONE]\n";

                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

    }

  /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function getAlbumContents($originalAlbumID, $fields) {

        $sql2= 'SELECT * FROM albums_photos WHERE pk_album = '.$originalAlbumID .' ORDER BY position ASC';
        $request = self::$originConn->Prepare($sql2);
        $rs2 = self::$originConn->Execute($request);
        $position =1;
        $album_photos_id = array();
        $album_photos_footer = array();

        while(!$rs2->EOF) {
            $imageID = $this->helper->imageIsImported($rs2->fields['pk_photo'], 'image');
            if(empty($imageID)) {

                $category = $this->matchInternalCategory($fields['pk_fk_content_category']);
                $category_name = $this->categoriesData[$category]->name;
                $name =$rs2->fields['pk_photo'];
                $albumTitle = $rs->fields['title'];
                $imageData = array(
                    'title' => $rs2->fields['pk_photo'],
                    'name' => $name,
                    'category'=> $category,
                    'category_name'=>  $category_name,
                    'metadata' => StringUtils::get_tags($albumTitle.','.$category_name),
                    'description' => $albumTitle,
                    'created' => $fields['created'],
                    'starttime' => $fields['created'],
                    'changed' => $fields['changed'],
                    'oldName' => $name,
                );
                $imageID = $this->createImage($imageData);

            }

            if(!empty($imageID)) {
                $album_photos_id[$position] = $imageID;
                $album_photos_footer[$position] = $rs2->fields['description'];
                $position++;
            }
            $rs2->MoveNext();

        }

        $rs2->Close(); # optional
        return array($album_photos_id, $album_photos_footer);
    }

    public function importAlbums() {

        $sql = 'SELECT * FROM albums, contents, contents_categories '
        .'WHERE  in_litter=0 AND available=1 '
        .'AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
        .'AND  `albums`.`pk_album` = `contents`.`pk_content` ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs->_numOfRows;

             $current = 1;

             $album = new Album();
             while(!$rs->EOF) {

                $originalAlbumID = $rs->fields['pk_album'];
                if ($this->helper->elementIsImported($originalAlbumID, 'album') ) {
                    echo "[{$current}/{$totalRows}] Albums with id {$originalAlbumID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing album with id {$originalAlbumID}  \n";
                    $title= $rs->fields['title'];
                    $category_name = $rs->fields['catName'];

                    list($album_photos_id, $album_photos_footer) = $this->getAlbumContents($originalAlbumID, $rs->fields);

                    $data = array('title' => $rs->fields['title'],
                        'category' => $this->matchInternalCategory($rs->fields['pk_fk_content_category']),
                        'with_comment' => 0,
                        'content_status' =>$rs->fields['content_status'],
                        'available' => $rs->fields['available'],
                        'frontpage' => $rs->fields['frontpage'],
                        'in_home' => $rs->fields['in_home'],
                        'metadata' => $rs->fields['title'],
                        'subtitle' => $rs->fields['subtitle'],
                        'agency' => $rs->fields['agency'],
                        'summary' => $rs->fields['summary'],
                        'fuente' => $rs->fields['fuente'],
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'starttime' => $rs->fields['created'],
                        'views' => $rs->fields['views'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $rs->fields['slug'],
                        'album_photos_id' => $album_photos_id,
                        'album_photos_footer'=> $album_photos_footer,
                        'cover_id' => $album_photos_id[1],
                        );
                    $album->cover_id = $album_photos_id[1];

                    $albumID = $album->create($data);

                    if(!empty($albumID) ) {
                        $this->helper->insertRefactorID($originalAlbumID, $albumID->id, 'album');
                        $this->helper->updateViews($albumID->id, $rs->fields['views'] );
                        $this->helper->updateCover($albumID->id,  $album_photos_id[1] );
                    }else{
                        $errorMsg = 'Problem '.$originalAlbumID.' - '.$title;
                        $this->helper->log('insert album : '.$errorMsg);
                        $this->helper->log("\n Problem inserting album {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                }

                $current++;
                $rs->MoveNext();
             }
             $rs->Close(); # optional
        }

         return true;

    }

    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function getGalleryContents($numgal ,$originalArticleID, $article) {

        $sql2= 'SELECT * FROM img_galerys WHERE fk_pk_content = '.$originalArticleID.' AND numgal = '.$numgal
                .' ORDER BY pos_des ASC, pos_int ASC';
        $request = self::$originConn->Prepare($sql2);
        $rs2 = self::$originConn->Execute($request);

        $position =1;
        $album_photos_id = array();
        $album_photos_footer = array();
        if(!$rs2) {
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {

            while(!$rs2->EOF) {
                $imageID = $this->helper->elementIsImported($rs2->fields['img'], 'image');
                if(empty($imageID)) {
                    $title =$rs2->fields['img_footer'];
                    $category = $article->category;
                    $category_name = $this->categoriesData[$category]->name;
                    $name =$rs2->fields['img'];
                    $imageData = array(
                        'title' => $rs2->fields['description'],
                        'name' => $name,
                        'category'=> $category,
                        'category_name'=>  $category_name,
                        'metadata' => StringUtils::get_tags($title.','.$category_name),
                        'description' => $title,
                        'created' => $article->created,
                        'changed' => $article->changed,
                        'oldName' => $name,
                    );

                    $imageID = $this->createImage($imageData);
                }
                if(!empty($imageID)) {
                    $album_photos_id[$position] = $imageID ;
                    $album_photos_footer[$position] = $rs2->fields['img_footer'];
                    $position++;
                }
                $rs2->MoveNext();

            }
        }
        $rs2->Close(); # optional
        return array($album_photos_id, $album_photos_footer);
    }

    public function importGalleries() {
        $sql2="SELECT DISTINCT(fk_pk_content) FROM img_galerys";
        $request = self::$originConn->Prepare($sql2);
        $rs2 = self::$originConn->Execute($request);

        if(!$rs2) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log('Gallery problem '. self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs2->_numOfRows;

             $current = 1;
             $album = new Album();
             $related = new RelatedContent();
             while(!$rs2->EOF) {
                $idGallery = $rs2->fields['fk_pk_content'];
                $originalArticleID =$rs2->fields['fk_pk_content'];
                $articleID = $this->helper->elementTranslate($originalArticleID);

                $article = new Article($articleID);
                if(empty($article)) {
                     $this->helper->log('Problem gallery '.$idGallery.' with article :'.$articleID .'\n ');
                } else {

                    $title= $article->title;
                    $category_name = $article->category_name;
                    $params = $article->params;
                    $data = array('title' => $title,
                            'category' => $article->category,
                            'with_comment' => 0,
                            'content_status' =>$article->content_status,
                            'available' => $article->available,
                            'frontpage' => $article->frontpage,
                            'in_home' => 0,
                            'metadata' => $article->metadata.', album',
                            'subtitle' => $article->subtitle,
                            'agency' => '',
                            'summary' => $article->summary,
                            'fuente' => '',
                            'category_name'=>  $category_name,
                            'description' => $title. " Gallery from article. ",
                            'created' => $article->created,
                            'changed' => $article->changed,
                            'starttime' => $article->created,
                            'views' => $article->views,
                            'fk_user' => USER_ID,
                            'fk_author' => USER_ID,
                            'slug' => $article->slug,
                            );

                    $numGal =1;
                    $params['withGallery'] =0;
                    $originalAlbumID = $article->pk_article.$numGal;
                    if ($this->helper->elementIsImported($originalAlbumID, 'gallery') ) {
                        echo "[{$current}/{$totalRows}] Gallery with id {$originalAlbumID} already imported as \n";
                    } else {

                        echo "[{$current}/{$totalRows}] Importing Gallery with album id {$originalAlbumID} - ";
                        list($album_photos_id, $album_photos_footer) = $this->getGalleryContents($numGal, $originalArticleID, $article);
                        if(!empty($album_photos_id)) {
                            $album_photos_id[0] = $article->img1;
                            $album_photos_footer[0] = $article->img1_footer;
                            $data['album_photos_id'] = $album_photos_id;
                            $data['album_photos_footer']= $album_photos_footer;
                            $album->cover_id = $album_photos_id[0];
                            $alb= $album->create($data);
                            $albumID =$alb->id;

                            if(!empty($albumID) ) {
                                $this->helper->insertRefactorID($originalAlbumID, $albumID, 'gallery');
                                $this->helper->updateViews($albumID, $article->views);
                                if(!empty($albumID) && !empty($articleID)) {
                                    $related->create($articleID, $albumID, 1, 0, 1, 0);
                                    $params['withGallery'] =$albumID;
                                }
                            }else{
                                $errorMsg = 'Problem '.$originalAlbumID.' - '.$title;
                                $this->helper->log('insert gallery as album : '.$errorMsg);
                                $this->helper->log("\n Problem inserting gallery front  {$article->title}-{$articleID} \n ");
                            }

                        }
                    }

                    $numGal =2;
                    $params['withGalleryInt'] = 0;
                    $originalAlbumID = $article->pk_article.$numGal;
                    if ($this->helper->elementIsImported($originalAlbumID, 'gallery') ) {
                        echo "[{$current}/{$totalRows}] Gallery with id {$originalAlbumID} already imported as \n";
                    } else {

                        echo "[{$current}/{$totalRows}] Importing Gallery with album id {$originalAlbumID} - ";
                        list($album_photos_id, $album_photos_footer) = $this->getGalleryContents($numGal, $originalArticleID, $article);
                         if(!empty($album_photos_id)) {
                            $album_photos_id[0] = $article->img2;
                            $album_photos_footer[0] = $article->img2_footer;
                            $data['album_photos_id'] = $album_photos_id;
                            $data['album_photos_footer']= $album_photos_footer;
                            $data['cover_id'] = $album_photos_id[0];
                            $album->cover_id = $album_photos_id[0];

                            $alb = $album->create($data);
                            $albumID =$alb->id;
                            if(!empty($albumID) ) {
                                $this->helper->insertRefactorID($originalAlbumID, $albumID, 'gallery');
                                $this->helper->updateViews($albumID, $article->views );

                                if(!empty($albumID) && !empty($articleID)) {
                                    $related->create($articleID, $albumID, 0, 1, 0, 1);
                                    $params['withGalleryInt'] = $albumID;
                                }
                            }else{
                                $errorMsg = 'Problem '.$originalAlbumID.' - '.$title;
                                $this->helper->log('insert gallery as album : '.$errorMsg);
                                $this->helper->log("\n Problem inserting gallery inner {$article->title}-{$articleID}\n ");
                            }
                        }
                    }
                    if(!empty($params['withGallery']) || !empty($params['withGalleryInt']) ) {
                         $this->helper->updateParams($articleID, serialize($params) );
                    }
                }

                $current++;
                $rs2->MoveNext();
             }
             $rs2->Close(); # optional
        }

         return true;

    }


  /**
     * create advertisements & import image ads in new DB.
     *
     * @param string $topic string for search articles in old database.

     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importAdvertisements() {
           $sql = 'SELECT * FROM advertisements, contents, contents_categories '
                .' WHERE contents.fk_content_type=2 AND contents.in_litter=0 AND contents.available=1 '
                .'AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .'AND  `pk_advertisement` = `contents`.`pk_content` '
                .$limit;

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs->_numOfRows;

             $current = 1;

             $ad = new Advertisement();
             while(!$rs->EOF) {

                $originalAdID = $rs->fields['pk_content'];
                if ($this->helper->elementIsImported($originalAdID, 'advertisement') ) {
                    echo "[{$current}/{$totalRows}] Ads with id {$originalAdID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing ads with id {$originalAdID} - ";
                    $title= $rs->fields['title'];
                    $category =2;//$this->matchInternalCategory($rs->fields['pk_fk_content_category']);
                    $category_name = $this->categoriesData[$category]->name;

                    $imageID = ""; //$this->helper->elementIsImported($rs->fields['path'], 'image');
                    if(empty($imageID)) {
                        $name =$rs->fields['path'];
                        var_dump($name);
                        $imageData = array(
                            'title' => $rs->fields['title'],
                            'name' => $name,
                            'category'=> $category,
                            'category_name'=>  'advertisement',
                            'metadata' => StringUtils::get_tags($title.','.$category_name,' publicidad'),
                            'description' => $title,
                            'created' => $rs->fields['created'],
                            'changed' => $rs->fields['changed'],
                            'oldName' => $name,
                        );
                        $newimageID = $this->createImage($imageData);
                    }


                    $data = array('title' => $rs->fields['title'],
                        'category' => $category,
                        'summary' => $rs->fields['summary'],
                        'body' => $rs->fields['body'],
                        'path' => $newimageID, //$this->helper->imageIsImported($rs->fields['path'],'image'),
                        'url' => $rs->fields['url'],
                        'num_clic_count' =>$rs->fields['num_clic_count'],
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'starttime' => $rs->fields['created'],
                        'views' => $rs->fields['views'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $rs->fields['slug'],
                        );

                    $adID = $ad->create($data)->id;

                    if(!empty($adID) ) {
                        $this->helper->insertRefactorID($originalAdID, $adID, 'advertisement');
                        $this->helper->updateViews($adID, $rs->fields['views'] );


                    }else{
                        $errorMsg = 'Problem '.$originalAdID.' - '.$title;
                        $this->helper->log('insert ads : '.$errorMsg);
                        $this->helper->log("\n Problem inserting ads {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                }

                $current++;
                $rs->MoveNext();
             }

        }
        $rs->Close(); # optional
        return true;

    }


  /**
     * create advertisements & import image ads in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importAttachments() {
        $sql = 'SELECT * FROM attachments';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;

            $current = 1;
            $oldPath = OLD_MEDIA.'files/' ;
            $directoryDate =date("/Y/m/d/");
            $basePath = MEDIA_PATH.'/'.FILE_DIR.$directoryDate ;
             // Create folder if it doesn't exist
            if( !file_exists($basePath) ) {
                mkdir($basePath, 0777, true);
            }

           $att = new Attachment();
           $p=0;
           while(!$rs->EOF) {

                $originalAdID = $rs->fields['pk_attachment'];
                if ($this->helper->elementIsImported($originalAdID, 'attachment') ) {
                    echo "[{$current}/{$totalRows}] attachment with id {$originalAdID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing attachment with id {$originalAdID} - ";
                    $title= $rs->fields['title'];
                    $name =$rs->fields['path'];
                    $data = array();
                    $category =$this->matchInternalCategory($rs->fields['category']);

                    $category_name = $this->categoriesData[$category]->name;
                    $fileName = preg_replace('/[^a-z0-9_\-\.]/i', '-', strtolower($name));
                    $data['path'] = $directoryDate.$fileName;
                    $data['title'] = $title;
                    $data['category'] = $category;
                    $data['category_name'] = $category_name;
                    $data['available'] = 1;
                    $data['description'] = $title;
                    $data['metadata'] = StringUtils::get_tags($title.', '.$category_name);
                    $data['fk_publisher'] = $_SESSION['userid'];
                    $data['fk_user'] =  USER_ID;
                    $data['fk_author'] = USER_ID;


                    // Move uploaded file
                     $uploadStatus = copy($oldPath.$category_name."/".$name, $basePath.$fileName);

                    if ($uploadStatus !== false) {

                        if ($att->create($data)) {
                            $adID = $att->id;
                            $this->helper->insertRefactorID($originalAdID, $adID, 'attachment');
                        } else {
                            $errorMsg = 'Problem '.$originalAdID.' - '.$title;
                            $this->helper->log('insert att : '.$errorMsg);
                        }
                    } else {

                        $this->helper->log($p.' - Problem copying attachment '." $name ->  $fileName \n");
                        $this->helper->log($oldPath.$category_name."/".$name.", ".$basePath.$fileName." \n ");
                        $p++;
                    }
                }
                $current++;
                $rs->MoveNext();
             }

        }
        echo "\n fail $p attachments \n";
        $rs->Close(); # optional
        return true;

    }


    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importSpecialItems($originalID, $id) {
           $sql = 'SELECT * FROM special_contents '
                .' WHERE fk_special = '.$originalID;

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
             $pos= 1;
             $special = new Special();
             $name='';

             while(!$rs->EOF) {
                 $contentID = $this->helper->elementIsImported($rs->fields['fk_content'], 'article');
                 if(!empty($contentID) ) {
                   $res =$special->set_contents($id, $contentID, $pos, $name, 'article');
                   if(!$res) {
                       $this->helper->log("\n Problem inserting special  item-{$rs->fields['fk_content']}\n ");
                   }
                 }
                 $pos++;
                 $rs->MoveNext();
             }
        }

    }
   /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importSpecials() {
           $sql = 'SELECT * FROM specials, contents, contents_categories '
                .' WHERE contents.fk_content_type=10 AND contents.in_litter=0 AND contents.available=1 '
                .'AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '
                .'AND  `specials`.`pk_special` = `contents`.`pk_content` '
                .$limit;

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            $this->helper->log(self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs->_numOfRows;

             $current = 1;

             $special = new Special();
             while(!$rs->EOF) {

                $originalSpecialID = $rs->fields['pk_special'];
                if ($this->helper->elementIsImported($originalSpecialID, 'special') ) {
                    echo "[{$current}/{$totalRows}] Special with id {$originalSpecialID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing special with id {$originalSpecialID} - ";
                    $title= $rs->fields['title'];
                    $category =$this->matchInternalCategory($rs->fields['fk_content_categories']);
                    $category_name = $this->categoriesData[$category]->name;

                    $imageID = $this->helper->elementIsImported($rs->fields['img1'], 'image');
                    if(empty($imageID) && !empty($rs->fields['img1'])) {
                        $title = $rs->fields['title'];
                        $imageData = array(
                            'title' => $rs->fields['title'],
                            'name' => $rs->fields['img1'],
                            'category'=> $category,
                            'category_name'=>  $category_name,
                            'metadata' => StringUtils::get_tags($title.','.$category_name),
                            'description' => $title,
                            'created' => $rs->fields['created'],
                            'changed' => $rs->fields['changed'],
                            'oldName' => $rs->fields['img1'],
                        );
                        $newimageID = $this->createImage($imageData);
                    }

                    $data = array('title' => $rs->fields['title'],
                        'category' => $category,
                        'subtitle' => $rs->fields['subtitle'],
                        'agency' => $rs->fields['agency'],
                        'summary' => $rs->fields['summary'],
                        'body' => $rs->fields['body'],
                        'img1' =>$this->helper->imageIsImported($rs->fields['img1'],'image'),
                        'pdf_path' =>$rs->fields['pdf'],
                        'category_name'=>  $category_name,
                        'description' => $title,
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'starttime' => $rs->fields['created'],
                        'views' => $rs->fields['views'],
                        'fk_user' => USER_ID,
                        'fk_author' => USER_ID,
                        'slug' => $rs->fields['slug'],
                        );

                    $specialID = $special->create($data);

                    if(!empty($specialID) ) {
                        $this->helper->insertRefactorID($originalSpecialID, $specialID, 'special');
                        $this->helper->updateViews($specialID, $rs->fields['views'] );
                        $this->importSpecialItems($originalSpecialID, $specialID);

                    }else{
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title."\n";
                        $this->helper->log('insert special : '.$errorMsg);
                        $this->helper->log("\n Problem inserting special {$rs->fields['title']}-{$rs->fields['pk_content']}\n ");
                    }
                }

                $current++;
                $rs->MoveNext();
             }

        }
        $rs->Close(); # optional
        return true;

    }

    /**
     * create articles in new DB.
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function importRelatedContents() {
        $sql = 'SELECT * FROM related_contents ';
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);
        $related = new RelatedContent();

        while(!$rs->EOF) {
            $contentID1 = $this->helper->elementTranslate($rs->fields['pk_content1']);
            $contentID2 = $this->helper->elementTranslate($rs->fields['pk_content2']);
            if(!empty($contentID1) && !empty($contentID2)) {
                $related->create($contentID1, $contentID2,$rs->fields['position'],
                        $rs->fields['posinterior'], $rs->fields['verportada'], $rs->fields['verinterior']);
            }
            $rs->MoveNext();

        }
        $rs->Close(); # optional

        $sql = 'SELECT * FROM attachments_contents';
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);
        $related = new RelatedContent();

        while(!$rs->EOF) {
            $contentID1 = $this->helper->elementTranslate($rs->fields['pk_attachment']);
            $contentID2 = $this->helper->elementTranslate($rs->fields['pk_content']);
            if(!empty($contentID1) && !empty($contentID2)) {
                $related->create($contentID1, $contentID2,$rs->fields['position'],
                        $rs->fields['posinterior'], $rs->fields['verportada'], $rs->fields['verinterior']);
            }
            $rs->MoveNext();

        }
        $rs->Close(); # optional
    }


    public function updateFrontpageArticles() {

        $sql = "SELECT pk_content, pk_fk_content_category, placeholder, position ".
                " FROM contents, contents_categories ".
                " WHERE frontpage=1 AND contents.fk_content_type=1 ".
                " AND pk_content = pk_fk_content AND content_status=1 AND available=1 ";

        $rs =  $GLOBALS['application']->conn->Execute($sql);
        $values= array();
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

        $sql = "SELECT pk_content, pk_fk_content_category, home_placeholder, home_pos ".
                " FROM contents, contents_categories ".
                " WHERE in_home=1 AND frontpage=1  AND contents.fk_content_type=1 ".
                " AND pk_content = pk_fk_content AND content_status=1 AND available=1 ";
echo $sql;
        $rs =  $GLOBALS['application']->conn->Execute($sql);

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

        $rs->Close(); # optional

        $sql= "INSERT INTO `content_positions` ".
              " (`pk_fk_content`, `fk_category`, `placeholder`, `position`, `params`, `content_type`)".
              " VALUES ( ?, ?, ?, ?, ?, ?)";

        $insert_sql = $GLOBALS['application']->conn->Prepare($sql);
        $rss = $GLOBALS['application']->conn->Execute($insert_sql, $values);

        if (!$rss) {

            $error =  "\n-  ".$sql." -- ".self::$originConn->ErrorMsg() ;
            $this->helper->log('-'.$error);
            printf('\n-'.$error);
        }else{
             printf('\n- Articles are added in frontpages');
        }

        //UPDATE `contents` SET available=1  WHERE `fk_content_type` =8
    }

}
