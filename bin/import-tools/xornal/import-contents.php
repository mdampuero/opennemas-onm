<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of importContents from Xornal db
 * Import opinions with author and his photos & articles with categories,images...
 *
 * @author sandra
 */

class importContents {

    public $internalCategories = array();
    public $logFile            = "log.txt";

    public function __construct ($config_newDB = array(),$config_oldDB = array())
    {

        $handle = fopen( $this->logFile , "wb");
        fclose($handle);


        if (isset($config_newDB['bd_host'])
            && isset($config_newDB['bd_database'])
            && isset($config_newDB['bd_user'])
            && isset($config_newDB['bd_pass'])
            && isset($config_newDB['bd_type']))
        {

        $this->new->conn = ADONewConnection($config_newDB['bd_type']);
        $this->new->conn->PConnect(
                                $config_newDB['bd_host'], $config_newDB['bd_user'],
                                $config_newDB['bd_pass'], $config_newDB['bd_database']
                              );
         } else {

            printf("ERROR: You must provide the connection configuration to the new database");
            $this->log("ERROR: You must provide the connection configuration to the new database" );
            die();
        }

        if (isset($config_oldDB['bd_host'])
            && isset($config_oldDB['bd_database'])
            && isset($config_oldDB['bd_user'])
            && isset($config_oldDB['bd_pass'])
            && isset($config_oldDB['bd_type']))
        {
        $this->old->conn = ADONewConnection($config_oldDB['bd_type']);
        $this->old->conn->PConnect(
                                    $config_oldDB['bd_host'], $config_oldDB['bd_user'],
                                    $config_oldDB['bd_pass'], $config_oldDB['bd_database']
                                  );

        } else {

            printf(    "ERROR: You must provide the connection configuration to the old database");
            $this->log("ERROR: You must provide the connection configuration to the old database" );
            die();
        }


        $GLOBALS['application'] = new Application();
        Application::initDatabase();

        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $GLOBALS['application']->conn->Execute('SET NAMES UTF8');

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

     /*   $this->internalCategories =  array(
                3 => 7,     // ALBUM
                4=> 14, //KIOSKO
            );
        return $this->categoriesMatches[$category];*/

        return 51;

    }


    /**
     * Extract category data of old db and insert these in new DB.
     * Drop example categories in new DB.
     *
     * @param array $categories categories ids of used contents
     *
     * @return bool
     */

    public function importCategories($categories)
    {
        //Drop example categories
        $sql ="DELETE FROM content_categories where pk_conten_category >= 10";
        $rss = $this->new->conn->Execute($sql);

        if (empty($categories)) {
            $sql = "SELECT * FROM content_categories";
        } else {
            $sql = "SELECT * FROM content_categories WHERE pk_conten_category IN ".  explode(',', $categories);
        }

        $rss    = $this->old->conn->Execute($sql);
        $values = array();
        if (!$rss) {
            $errorMsg = $this->old->conn->ErrorMsg();
            printf( 'read categories: '.$errorMsg);
            $this->log( 'read categories:'. $errorMsg );
        } else {
             while (!$rs->EOF) {
                  $values[] = array(
                        'pk_content_category' => $rs->fields['pk_content_category'],
                        'title' => $rs->fields['title'],
                        'name' => $rs->fields['name'],
                        'inmenu' => $rs->fields['inmenu'],
                        'posmenu' => $rs->fields['posmenu'],
                        'internal_category' => self::matchInternalCategory($rs->fields['internal_category']),
                        'fk_content_category' => $rs->fields['fk_content_category'],

                        );
                       $rs->MoveNext();

            }
            $sql = 'INSERT INTO content_categories
                (`pk_content_category`, `title`, `name`,`inmenu`, `posmenu`, `internal_category`, `fk_content_category`  )
                VALUES (?,?,?,?,?,?,?)';


            $stmt = $this->new->conn->Prepare($sql);
            if (count($values)>0) {
                if ($this->new->conn->Execute($stmt, $values) === false) {
                    $errorMsg = $this->new->conn->ErrorMsg();
                    $this->log('importCategories: '.$errorMsg);
                    printf('importCategories: '.$errorMsg);

                    return false;
                }
            } else {

               return true;
            }
        }
        return false;

    }

    /**
     * Get author data & photos in old DB
     *
     * @param int $oldid with author id
     *
     * @return array with author information.
     *
     */

    public function getAuthorData($oldid)
    {
        $sql = 'SELECT  `authors`.`pk_author`, `authors`.`name` , '.
           ' `authors`.`politics` ,   `authors`.`fk_user` ,'.
           ' `authors`.`condition` '.
           ' FROM authors WHERE `authors`.`pk_author` = '.($oldid);
        $rs  = $this->old->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $this->old->conn->ErrorMsg();
            $this->log('getAuthorData: '.$errorMsg);
            printf('getAuthorData: '.$errorMsg);

            return false;
        }

        $author = array();
        $author = $this->load( $rs->fields );

        //photos process
        $sql = 'SELECT author_imgs.pk_img, author_imgs.path_img, author_imgs.description
            FROM author_imgs WHERE pk_author ='.$oldid;
        if (!$rs) {
            $error_msg = $this->old->conn->ErrorMsg();
            $this->log('getAuthor photoData: '.$errorMsg);
            printf('getAuthor photoData: '.$errorMsg);

        }

        $photos = array();
        $i=0;
        while(!$rs->EOF) {
            $photos[$i] = new stdClass();
            $photos[$i]->pk_img         = $rs->fields['pk_img'];
            $photos[$i]->path_img     = $rs->fields['path_img'];
            $photos[$i]->description = $rs->fields['description'];

            $i++;
            $rs->MoveNext();
        }

        $author->photos = $photos;

        return $author;

     }


    /**
     * Insert author data & photos in old DB
     *
     * @param array $data author information
     *
     * @return bool
     *
     */
    public function insertAuthor($data)
    {

        $values = array(
            'name'=>$data->name,
            'fk_user'=>'0',
            'blog'=>'',
            'politics'=>$data->politics,
            'condition'=>$data->condition,
            'date_nac'=>$data->date_nac
        );

        $author = new Author();

        $pk_author = $author->create($values);


        printf( "Se ha insertado en $pk_author");


    /*
         if(!empty($data->photos)) {
            $values= array();
            foreach($data->photos as $photo) {

                $values[] = array( $pk_author, $photo->pk_img, $photo->path_img );

            }

            $sql = "INSERT INTO author_imgs (`fk_author`, `fk_photo`,`path_img`) VALUES (?,?,?)";
            $stmt = $this->new->conn->Prepare($sql);

            if ($this->new->conn->Execute($stmt, $values) === false) {
                $errorMsg = $this->new->conn->ErrorMsg();
                $this->log('insert photoAuthor : '.$errorMsg);
                printf('insert photoAuthor : '.$errorMsg);

            }
        }
    */
        return $pk_author;
    }

    public function getOpinionsData($authorID)
    {
        $sql = 'SELECT * FROM opinions, contents
            WHERE contents.in_litter=0 AND opinions.fk_author='.$authorID.' AND  pk_opinion = pk_content';

        $rs = $this->old->conn->Execute($sql);

        $items = array();

        $items =  $rs->GetArray();

        return( $items );
     }

    public function importOpinions($authorID)
    {

        $author = $this->getAuthorData($authorID);

        printf('Getting author data -'.$authorID. ' /n');

        $newAuthorId = $this->insertAuthor($author);

        $opinions = $this->getOpinionsData($authorID);

        $opinion = new Opinion();

        if(!empty($opinions)){
            foreach ($opinions as $data) {

                $data['fk_author'] = $newAuthorId;
                $data['fk_user'] = $newAuthorId;
                $data['fk_publisher'] = $newAuthorId;
                $data['fk_user_last_editor '] = $newAutasshorId;
                $data['category_name'] = 'opinion';
                $data['category'] = 4;
                $data['type_opinion'] = 0;  //force author opinion


                $id = $opinion->create($data);

                if(!empty($id) ) {
                    $this->insertRefactorID($data['pk_content'], $id, 'opinion');
                    printf("\n Inserting: ".$data['pk_content'].", ".$id.", opinion");
                }else{
                    $errorMsg = 'Problem '.$data['pk_content'].' - '.$data['title']. " /n";
                    $this->log('insert opinion : '.$errorMsg);
                    printf('insert opinion : '.$errorMsg);
                }


            }

            return true;
        }

        return false;
    }

    public function getArticlesbyAuthor($topic)
    {

        $_where = " (fk_author = '".$topic.
                                            "' OR fk_publisher = '".$topic.
                                            "' OR fk_user_last_editor = '".$topic."' ) ";


        $sql = " SELECT * FROM articles, contents, contents_categories ".
                " WHERE fk_content_type=1  AND in_litter=0  ".
                " AND pk_content = pk_article AND ".
                " pk_content = pk_fk_content AND ".  $_where ;



        echo "\n". $sql. "\n";

        $rss = $this->old->conn->Execute($sql);

        if (!$rss) {
            printf( 'getArticlesData function: '. $this->old->conn->ErrorMsg() );
            $this->log('getArticlesData function: '. $this->old->conn->ErrorMsg() );


        } else {

            $articles = $this->load($rs->fields);

            return $articles;
       }

    }

    public function getArticlesData($topic)
    {


        $_where = " WHERE fk_content_type=1  AND in_litter=0 AND (".
                  " title LIKE '".$topic."' OR metadata LIKE '".$topic."' ".
                  " OR description LIKE '".$topic."' OR summary LIKE '".$topic."' ".
                  " OR body LIKE '".$topic."' OR subtitle LIKE '".$topic."' ".
                  " OR agency LIKE '".$topic."' ) ";


        $sql = " SELECT * FROM articles, contents, contents_categories ".
                $_where. " AND pk_article = pk_content AND ".
                " pk_content = pk_fk_content  " ;

        echo $sql. "\n";
        $rss = $this->old->conn->Execute($sql);

        if (!$rss) {
            printf( 'getArticlesData function: '. $this->old->conn->ErrorMsg() );
            $this->log('getArticlesData function: '. $this->old->conn->ErrorMsg() );


        } else {

         //   $articles = $this->load($rs->fields);
             $articles =  $rs->GetArray();

            return $articles;
       }

    }

    /**
     * Upload images
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function moveUploadPhoto($photo)
    {
        $path_upload = INSTANCE_MEDIA."";
        $uploads = array();


            $nameFile = $photo->path_file.$photo->name;

            if(!empty($nameFile)) {
                $uploaddir =$path_upload.$path_file;

                if(!is_dir($uploaddir)) {
                    FilesManager::createDirectory($uploaddir);
                }

                $old_dir = 'media/images'.$nameFile;

                if (move_file($old_dir, $path_upload.$nameFile) ) {
                    $data['title'] = $photo->name;
                    $data['name'] = $photo->name;
                    $data['path_file'] =  $photo->path_file;;
                    $data['category'] = $photo->category;

                    $data['nameCat'] = $photo->category_name; //nombre de la category

                    $infor  = new MediaItem( $uploaddir.$name );     //Para sacar todos los datos de la imag

                    $data['created'] = $infor->atime;
                    $data['changed'] = $infor->mtime;
                    $data['date'] = $infor->mtime;
                    $data['size'] = round($infor->size/1024, 2);
                    $data['width'] = $infor->width;
                    $data['height'] = $infor->height;
                    $data['type_img'] = $extension;
                    $data['media_type'] = $_REQUEST['media_type'];

                    // Default values
                    $data['author_name']  = '';
                    $data['pk_author']    = $_SESSION['userid'];
                    $data['fk_publisher'] = $_SESSION['userid'];
                    $data['description']  = '';
                    $data['metadata']     = '';

                    $foto = new Photo();
                    $elid = $foto->create($data);

                    if($elid) {
                        if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)) {
                            // miniatura
                            $thumb = new Imagick($uploaddir.$name);

                            //ARTICLE INNER
                            $thumb->thumbnailImage(self::INNER_WIDTH, self::INNER_HEIGHT, true);
                            //Write the new image to a file
                            $thumb->writeImage($uploaddir . self::INNER_WIDTH . '-' . self::INNER_HEIGHT . '-' . $name);

                            //FRONTPAGE
                            $thumb->thumbnailImage(self::FRONT_WIDTH, self::FRONT_HEIGHT, true);
                            //Write the new image to a file
                            $thumb->writeImage($uploaddir . self::FRONT_WIDTH . '-' . self::FRONT_HEIGHT . '-' . $name);

                            //THUMBNAIL
                            $thumb->thumbnailImage(self::THUMB_WIDTH, self::THUMB_HEIGHT, true);
                            //Write the new image to a file
                            $thumb->writeImage($uploaddir . self::THUMB_WIDTH . '-' . self::THUMB_HEIGHT . '-' . $name);
                        }
                    }

                    $uploads[] = $elid;
                } else {

                    $fallos .= " '" . $nameFile . "' ";
                }
            } //if empty

        return $elid;
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

    public function insertImage($pk_photo) {

        $sql = 'SELECT * FROM photos WHERE pk_photo = '.$pk_photo;

        $rs = $this->old->conn->Execute($sql);

        $image = $this->load($rs->fields);

          //move file & insert photo
        $pk_image = $this->moveUploadPhoto($image);

        return $pk_image;
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
    public function importArticles($authorID, $newAuthorId, $topic = '', $byAuthor=false) {

        $articles = array();

        if(!empty($topic) && ($byAuthor==true) ) {
           $articles =  $this->getArticlesbyAuthor($topic);
        } else {
            //$articles = $this->getArticlesData($topic);
            $articles = $this->getOpinionsData($authorID);

        }

        if (!empty($articles)) {

            $content = new \Article();
            $data = null;
            foreach ($articles as $article) {
                if ($this->elementIsImported($article['pk_content'], 'article')) {
                    echo " image with id {$article['pk_content']} already imported\n";
                } else {
                    $data = $article;
                    $data['id'] = null;
                    $data['fk_content_type'] = 1;
                    $data['title']          = mb_convert_encoding($article['title'], 'UTF-8');
                    $data['title_int']      = mb_convert_encoding($article['title'], 'UTF-8');
                    $data['body']           = $article['body'];
                    $data['img1']           = null;// $this->insertImage($data->img1);
                    $data['img2']          = null;//$this->insertImage($data->img2);
                    $data['category_name']  = 'opinion';
                    $data['category']       = 51;
                    $data['available']      = 1;
                    $data['content_status'] = 1;
                    $data['fk_author']      = $newAuthorId;
                    $data['fk_user']        = $newAuthorId;
                    $data['fk_publisher']   = $newAuthorId;
                    $data['fk_user_last_editor']  = $newAuthorId;
                    $data['starttime']      = $data['created'];
                    $data['endtime']        = '0000-00-00 00:00:00';
                    $data['available']      = 1;
                    $data['slug']           = mb_convert_encoding(\StringUtils::get_title($article['title']) , 'UTF-8');
                    $data['summary']        = $article['summary'];
                    $data['description']    = mb_convert_encoding($article['description'], 'UTF-8');
                    if (empty($article['summary'])) {
                        $data['summary'] = substr($data['body'], 0, 120);
                    }
                    if (empty($data['description'])) {
                        $data['description'] = substr($data['body'], 0, 120);
                    }

                    $id = $content->create($data);

                    if(!empty($id) ) {

                        $this->insertRefactorID($article['pk_content'], $id, 'article');
                         var_dump('Inserting '.$article['pk_content'], $id, 'article');
                    } else {
                        $errorMsg = 'Problem '.$article['pk_content'].' - '.$data['title'];
                        $this->log('insert article : '.$errorMsg);
                        printf('insert article : '.$errorMsg);
                    }
                }
            }

        }



        return true;

    }


    /**
     * Load properties in a object.
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function load($properties) {
        $item = new stdClass();

        if (is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $item->{$k} = $v;
                }
            }
        } elseif (is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach ($properties as $k => $v) {
                if (!is_numeric($k)) {
                    $item->{$k} = $v;
                }
            }
        }

        return $item;

    }

    /**
     * Insert tranlation id's in a table
     *
     * @param datatype $varname Var explanation.
     *
     * @return datatype Explanation of returned data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */

    public function insertRefactorID($contentID, $newID, $type) {
        $sql_translation_request =
                'INSERT INTO translation_ids (`pk_content_old`, `pk_content`, `type`)
                                       VALUES (?, ?, ?)';
        $translation_values = array($contentID, $newID, $type);

        $translation_ids_request = $GLOBALS['application']->conn->Prepare($sql_translation_request);

        $rss = $GLOBALS['application']->conn->Execute($translation_ids_request,
                                                      $translation_values);

        if (!$rss) {
            printf( 'insertRefactorID function: '. $GLOBALS['application']->conn->ErrorMsg() );
            $this->log('insertRefactorID function: '.$GLOBALS['application']->conn->ErrorMsg() );
        }

    }

    public function elementIsImported($contentID, $contentType)
    {
        if (isset($contentID) && isset($contentType)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old`=? AND type=?';

            $values = array($contentID, $contentType);
            $views_update_sql = $GLOBALS['application']->conn->Prepare($sql);
            $rss = $GLOBALS['application']->conn->Execute($views_update_sql, $values);

            if (!$rss) {
                echo $GLOBALS['application']->conn->ErrorMsg();
            } else {
                return ($rss->fields['pk_content']);
            }

        } else {
            echo "There is imported {$contentID} - {$contentType}\n.";
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

/****

SELECT * FROM `contents`, articles WHERE pk_article=pk_content and    fk_content_type=1  AND in_litter=0 AND (  title LIKE '%_os_%_uis%_mez%' OR metadata LIKE '%_os_%_uis%_mez%'   OR description LIKE '%_os_%_uis%_mez%' OR summary LIKE '%_os_%_uis%_mez%'  OR body LIKE '%_os_%_uis%_mez%' OR subtitle LIKE '%_os_%_uis%_mez%' OR agency LIKE '%_os_%_uis%_mez%')


*********/
