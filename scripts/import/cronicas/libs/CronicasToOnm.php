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
                6 => 12,   // cantabria
                7 => 296,  // madrid
                8 => 21,   // Baleares
                9 => 22,   // Andalucia
                10 => 24,  // opinion
                11 => 28,   // publi
                15 => 30,  // paisvasco

            );
      $this->categoriesData = ContentCategoryManager::get_instance()->categories;

      return false;

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

        $sql="SELECT distinct(`author`), img FROM `opinions` where `type_opinion`=0 ".
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
            if($rs->fields['img']) {
                $photoID =$this->helper->imageIsImported($rs->fields['img'], 'author');
                if(empty($photoID)) {
                    $imageData = array(
                        'name' => $name,
                        'category'=> '7',
                        'title' =>$rs->fields['author'],
                        'category_name'=>'author',
                        'metadata' => StringUtils::get_tags($rs->fields['author']),
                        'description' => $rs->fields['author'],
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'oldName' => $rs->fields['img'],
                    );

                    $photoID = $this->createImage($imageData);
                    $photo = new Photo($photoID);
                    $photoPath = $photo->path_file.$photo->name;

                 $sql = "INSERT INTO author_imgs
                                        (`fk_author`, `fk_photo`,`path_img`)
                                VALUES (?,?,?)";

                 $values = array( $authorID, $photoID, $photoPath );
                 $request = self::$originConn->Prepare($sql, $values);
                 $rs2 = self::$originConn->Execute($request);
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
        $sql ="SELECT * FROM opinions, contents, contents_categories WHERE in_litter=0";

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        $opinion = new Opinion();

        if(!$rs) {
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;

            $author = array();
            while (!$rs->EOF) {
                $name = StringUtils::get_title($rs->fields['author']);
                $author['id'] = $this->helper->authorIsImported($name);

                if(empty($author['id'])) {
                    $values = array(
                        'name' => $rs->fields['author'],
                        'fk_user' =>0,
                        'blog' =>'',
                        'politics' =>'',
                        'condition' =>'',
                        'date_nac`' =>''
                    );
                    $newAuthor = new Author();
                    $author['id'] = $newAuthor->create($values);

                    echo "new id  {$author['id']}  [DONE]\n";
                    $this->helper->insertAuthorTranslated($author['id'], $name, 'author');

                }
                $author['img'] = $this->helper->imageIsImported($rs->fields['img'],'author');

                $originalOpinionID = $rs->fields['pk_opinion'];

                if ($this->helper->elementIsImported($originalOpinionID, 'opinion') ) {
                    echo "[{$current}/{$totalRows}] Opinion with id {$originalOpinionID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing opinion with id {$originalOpinionID} - ";

                    $values =
                        array(
                            'title' => $rs->fields['title'],
                            'category' => '4',
                            'type_opinion' => 0,
                            'body' => $data['body'],
                            'metadata' =>$rs->fields['metadata'].', opinion',
                            'description' => strip_tags(substr($data['body'],0,150)),
                            'fk_author' => $author['name'],
                            'fk_author_img' =>$author['img'],
                            'fk_author_widget' =>$author['img'],
                            'available' => $rs->fields['available'],
                            'with_comment' => 0,
                            'in_home' => $rs->fields['in_home'],
                            'content_status' => $rs->fields['content_status'],
                            'created' => $rs->fields['created'],
                            'starttime' => $rs->fields['startime'],
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

                        $this->helper->logElementInsert($originalOpinionID, $newOpinionID, 'opinion');

                    }
                    $this->helper->updateViews($newOpinionID,$rs->fields['views'] );
                    echo "new id {$newOpinionID} [DONE]\n";

                }

                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }
    }

    /* create new image */

    public function createImage($data) {

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
        $newimageID = $image->createFromLocalFile($values);
        if(is_string($newimageID)) {

            $this->helper->insertImageTranslated($newimageID, $data['oldName'], 'image');

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
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {

            $totalRows = $rs->_numOfRows;
            $current = 1;
            while(!$rs->EOF) {
                for($i=1; $i<=3; $i++) {
                    $img= "img{$i}";
                    $footer= "{$img}_footer";
                    $name =$rs->fields[$img];
                    $title = $rs->fields[$footer];

                    echo("$name, $title,  \n");

                    if(!empty($name)) {
                        $imageID = $this->helper->imageIsImported($name, 'image');

                        if(!empty($imageID)) {
                            echo "[{$current}/{$totalRows}] images with id {$imageID} already imported\n";
                        } else {
                            echo "[{$current}/{$totalRows}] Importing image with id {$name} - ";
                            if(empty($title)) { // if empty footer ->get title
                                $title =$rs->fields['title'];
                            }
                            $category = $rs->fields['pk_fk_content_category'];
                            $category_name = $this->categoriesData[$category]->category_name;
                            $imageData = array(
                                'title' => $title,
                                'name' => $name,
                                'category'=> $category,
                                'category_name'=>  $category_name,
                                'metadata' => StringUtils::get_tags($title.','.$category_name),
                                'description' => $title,
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
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
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
                    $data = array('title' => $rs->fields['title'],
                        'category' => $rs->fields['pk_category'],
                        'with_comment' => 0,
                        'content_status' =>$rs->fields['content_status'],
                        'available' => $rs->fields['available'],
                        'frontpage' => $rs->fields['frontpage'],
                        'in_home' => $rs->fields['in_home'],
                        'title_int' => $rs->fields['title'],
                        'metadata' => $rs->fields['title'],
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
                        $this->helper->updateViews($articleID,$rs->fields['views'] );
                    }else{
                        $errorMsg = 'Problem '.$originalArticleID.' - '.$title;
                        $this->helper->log('insert article : '.$errorMsg);
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
     * Import images from specials, albums, ...
     *
     * @param string $topic string for search articles in old database.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */


    public function importOtherImages() {
        $sql = 'SELECT pk_photo, description FROM `albums_photos`';
         $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            while(!$rs->EOF) {
                $imageID = $ih->elementIsImported($rs->fields['pk_photo'], 'image');
                if(empty($imageID)) {
                    $image = new Photo();

                    $title =$rs->fields['description'];
                    $category = $rs->fields['pk_content_category'];
                    $category_name = $this->categoriesData[$category]->category_name;
                    $imageData = array(
                        'title' => $rs->fields['pk_photo'],
                        'name' => $name,
                        'category'=> $category,
                        'category_name'=>  $category_name,
                        'metadata' => StringUtils::get_tag($title.','.$category_name),
                        'description' => $title,
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'oldName' => $name,
                    );
                    $newimageID = $this->createImage($imageData);
                }
                $current++;
                $rs->MoveNext();
            }
            $rs->Close(); # optional
        }

        $sql = 'SELECT img, img_footer FROM `img_galleries`';
        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);

        if(!$rs) {
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
            $totalRows = $rs->_numOfRows;
            $current = 1;
            while(!$rs->EOF) {
                $imageID = $ih->elementIsImported($rs->fields['img'], 'image');
                if(empty($imageID)) {
                    $image = new Photo();
                    $title =$rs->fields['img_footer'];
                    $category = $rs->fields['pk_content_category'];
                    $category_name = $this->categoriesData[$category]->category_name;
                    $imageData = array(
                        'title' => $rs->fields['pk_photo'],
                        'name' => $name,
                        'category'=> $category,
                        'category_name'=>  $category_name,
                        'metadata' => StringUtils::get_tag($title.','.$category_name),
                        'description' => $title,
                        'created' => $rs->fields['created'],
                        'changed' => $rs->fields['changed'],
                        'oldName' => $name,
                    );
                    $newimageID = $this->createImage($imageData);
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

    public function getAlbumContents($originalAlbumID) {
        $sql2= 'SELECT * FROM albums_photos WHERE pk_album = '.$originalAlbumID .' ORDER BY position ASC';
        $request = self::$originConn->Prepare($sql2);
        $rs2 = self::$originConn->Execute($request);
        $position =0;
        $album_photos_id = array();
        $album_photos_footer = array();

        while(!$rs2->EOF) {
            $photoID = $this->helper->imageIsImported($rs2->fields['pk_photo'], 'album');
            if(!empty($photoID)) {
                $album_photos_id[$position] = $photoID ;
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
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs->_numOfRows;

             $current = 1;

             $album = new Album();
             while(!$rs->EOF) {

                $originalAlbumID = $rs->fields['pk_album'];
                if ($this->helper->elementIsImported($originalAlbumID, 'image') ) {
                    echo "[{$current}/{$totalRows}] Albums with id {$originalAlbumID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing album with id {$originalAlbumID} - ";
                    $title= $rs->fields['title'];
                    $category_name = $rs->fields['catName'];

                    list($album_photos_id, $album_photos_footer) = $this->getAlbumContents($originalAlbumID);

                    $data = array('title' => $rs->fields['title'],
                        'category' => $rs->fields['pk_category'],
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
                        'cover' => $album_photos_id[0],
                        );

                    $albumID = $album->create($data);

                    if(!empty($albumID) ) {
                        $this->helper->insertRefactorID($originalAlbumID, $albumID, 'album');
                        $this->helper->updateViews($albumID, $rs->fields['views'] );
                    }else{
                        $errorMsg = 'Problem '.$originalAlbumID.' - '.$title;
                        $this->helper->log('insert article : '.$errorMsg);
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

    public function getGalleryContents($originalArticleID, $numgal) {
        $sql2= 'SELECT * FROM img_galerys WHERE pk_fk_content = '.$originalArticleID.' AND numgal = '.$numgal
                .' ORDER BY pos_des ASC, pos_int ASC';
        $request = self::$originConn->Prepare($sql2);
        $rs2 = self::$originConn->Execute($request);
        $position =0;
        $album_photos_id = array();
        $album_photos_footer = array();

        while(!$rs2->EOF) {
            $photoID = $this->helper->imageIsImported($rs2->fields['img'], 'image');
            if(!empty($photoID)) {
                $album_photos_id[$position] = $photoID ;
                $album_photos_footer[$position] = $rs2->fields['description'];
                $position++;
            }
            $rs2->MoveNext();

        }
        $rs2->Close(); # optional
        return array($album_photos_id, $album_photos_footer);
    }

    public function importGalleries() {


        $sql = 'SELECT * FROM albums, contents, contents_categories WHERE in_litter=0 AND available=1 ';

        $request = self::$originConn->Prepare($sql);
        $rs = self::$originConn->Execute($request);


        if(!$rs) {
            echo self::$originConn->ErrorMsg();
            CronicasHelper::messageStatus(self::$originConn->ErrorMsg());
        } else {
             $totalRows = $rs->_numOfRows;

             $current = 1;

             $album = new Album();
             while(!$rs->EOF) {

                $originalAlbumID = $rs->fields['pk_album'];
                if ($this->helper->elementIsImported($originalAlbumID, 'image') ) {
                    echo "[{$current}/{$totalRows}] Albums with id {$originalAlbumID} already imported\n";
                } else {
                    echo "[{$current}/{$totalRows}] Importing album with id {$originalAlbumID} - ";
                    $title= $rs->fields['title'];
                    $category_name = $rs->fields['catName'];

                    list($album_photos_id, $album_photos_footer) = $this->getGalleryContents();

                    $data = array('title' => $rs->fields['title'],
                        'category' => $rs->fields['pk_category'],
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
                        );

                    $albumID = $album->create($data);

                    if(!empty($albumID) ) {
                        $this->helper->insertRefactorID($originalAlbumID, $albumID, 'album');
                    }else{
                        $errorMsg = 'Problem '.$originalAlbumID.' - '.$title;
                        $this->helper->log('insert article : '.$errorMsg);
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
    public function importLetters() {

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
    public function importAdvertisements() {

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

    }

}
