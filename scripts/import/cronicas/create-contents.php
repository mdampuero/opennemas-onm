<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of importContents
 * Import opinions with author and his photos & articles with categories,images...
 *
 * @author sandra
 */

class createContents {
    public $internalCategories = array();
    public $logFile = "log.txt";

    public function __construct ($config_newDB = array(),$config_oldDB = array())
    {

        $handle = fopen( $this->logFile , "wb");
        fclose($handle);



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

        $this->internalCategories =  array(
                3 => 7,     // ALBUM
                4=> 14, //KIOSKO
            );
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

    public function importCategories($categories)
    {
        //Drop example categories
        $sql ="DELETE FROM content_categories where pk_conten_category >= 10";
        $rss = $this->new->conn->Execute($sql);


          $valuescategories[] = array(
                    'pk_content_category' => $categories['pk_content_category'],
                    'title' => $categories['title'],
                    'name' => $categories['name'],
                    'inmenu' =>$categories['inmenu'],
                    'posmenu' => $categories['posmenu'],
                    'internal_category' => self::matchInternalCategory($categories['internal_category']),
                    'fk_content_category' => $categories['fk_content_category'],

                );

            $sql = 'INSERT INTO content_categories
                (`pk_content_category`, `title`, `name`,`inmenu`, `posmenu`, `internal_category`, `fk_content_category`  )
                VALUES (?,?,?,?,?,?,?)';


            $stmt = $this->new->conn->Prepare($sql);
            if (count($valuescategories)>0) {
                if ($this->new->conn->Execute($stmt, $valuescategories) === false) {
                    $errorMsg = $this->new->conn->ErrorMsg();
                     $this->log('importCategories: '.$errorMsg);
                     printf('importCategories: '.$errorMsg);
                    return false;
                }
            } else {
               return true;
            }

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
    public function insertAuthor($data)
    {

        $sql = "INSERT INTO authors
                (`name`, `fk_user`, `blog`,`politics`, `condition`,`date_nac`)
                VALUES ( ?,?,?,?,?,?)";

        $values = array(
            $data['name'], '0', '',
            $data['politics'], $data['condition'], $data['date_nac']
        );

        if ($this->new->conn->Execute($sql, $values) === false) {
            $errorMsg = $this->new->conn->ErrorMsg();
            $this->log('insertAuthor : '.$errorMsg);
            printf('insertAuthor : '.$errorMsg);

            return false;
        }

        //tabla author_imgs
		$pk_author = $this->new->conn->Insert_ID();

     	if($data['photos']) {
            $values= array();
            foreach($data['photos'] as $photo) {

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

        return array($newID, $photo->pk_img);
    }



    public function importOpinions($opinions)
    {


         $opinion = new Opinion();

         if(!empty($opinions)){
             foreach ($opinions as $data) {
                 $newAuthor = $this->insertAuthor($data);
                $data['fk_author'] = $newAuthor[0];
                $data['fk_author_img'] = $newAuthor[1];
                $data['fk_author_img_widget'] = $newAuthor[1];

                $id = $opinion->create($data);
                if(!empty($id) ) {
                    $this->insertRefactorID($data->pk_opinion, $id, 'opinion');
                }else{
                    $errorMsg = 'Problem '.$data->pk_opinion.' - '.$data->title;
                    $this->log('insert opinion : '.$errorMsg);
                    printf('insert opinion : '.$errorMsg);
                }
             }

             return true;
         }
         return false;
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

                    $infor  = new MediaItem( $uploaddir.$name ); 	//Para sacar todos los datos de la imag

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

    public function insertImagebyName($name) {
        //search in dir

        //defined data

        //Insert in db

        //return id

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
    public function importArticles($articles) {


         $categories = array();

         if(!empty($articles)) {
             $article = new Article();
             foreach ($articles as $data) {
                $data['img1'] = $this->insertImagebyName($data['img1']);
                $data['img2'] = $this->insertImagebyName($data['img2']);
                //id_video = fk_video
                $data['params'] = array('titleHome'=>$data['title_home']
                                        //titleHomeSize, title_home
                                        //titleSize title_size
                                        //subtitleHome, subtitle_home
                                        //summaryHome, summary_home
                                        //imageHomePosition
                                        //imagePosition 	img_pos
                                        //agencyBulletin, agency_web
                                        //withGallery, with_galery
                                        //withGalleryInt with_galery_int
                                        //imageHome => img3
                                        //imageHomeFooter => img3_footer
                                        );

                $id = $article->create($data);

                if(!empty($id) ) {
                    $this->insertRefactorID($data->pk_article, $id, 'article');
                }else{
                    $errorMsg = 'Problem '.$data->pk_article.' - '.$data->title;
                    $this->log('insert article : '.$errorMsg);
                    printf('insert article : '.$errorMsg);
                }
             }

         }

         $this->importCategories($categories);

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
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $item->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $item->{$k} = $v;
                }
            }
        }
        return $item;

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
