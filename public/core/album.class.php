<?php
//album de fotos

class Album extends Content{
    public $pk_album = NULL;
    public $subtitle = NULL;
    public $agency = NULL;
    public $fuente = NULL;
    public $cover = NULL;
    public $widthCover = 300;
    public $heightCover = 240;


    /**
      * Constructor PHP5
    */

    function __construct($id=NULL){

        parent::__construct($id);

        if (!is_null($id)) {
            $this->read($id);
        }
       	$this->content_type = __CLASS__;
    }

	public function __get($name)
    {

        switch ($name) {

            case 'uri': {
                $uri =  Uri::generate( 'album',
                            array(
                                'id' => $this->id,
                                'date' => date('Y-m-d', strtotime($this->created)),
                                'category' => $this->category_name,
                                'slug' => $this->slug,
                            )
                        );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            }
            case 'slug': {
                return String_Utils::get_title($this->title);
                break;
            }

            case 'content_type_name': {
				$contentTypeName = $GLOBALS['application']->conn->
                    Execute('SELECT * FROM `content_types` WHERE pk_content_type = "'. $this->content_type.'" LIMIT 1');
                    if(isset($contentTypeName->fields['name'])) {
                        $returnValue = $contentTypeName;
                    } else {
                        $returnValue = $this->content_type;
                    }
					$this->content_type_name = $returnValue;
                    return $returnValue;

                break;
            }

            default: {
                break;
            }
        }

		parent::__get($name);
    }

	/**
     * Explanation for this function.
     *
     * @param array $data  .
     *
     * @return bool If create in database
     */

    function create($data) {

        parent::create($data);

        $this->cover = $this->cropImageFront($data);

        $data['subtitle'] = (empty($data['subtitle']))? '': $data['subtitle'];
        $data['fuente'] = (empty($data['fuente']))? '': $data['fuente'];

		$sql = "INSERT INTO albums ".
               " (`pk_album`,`subtitle`, `agency`,`fuente`,`cover`) " .
			   " VALUES (?,?,?,?,?)";

        $values = array($this->id,$data["subtitle"],$data["agency"],$data["fuente"],$this->cover);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        $data['id']=$this->id;

        if(isset($data['ordenAlbum'])){
            $tok = strtok($data['ordenAlbum'],"++");
            $pos=1;
            $album=new Album_photo();
            while (($tok !== false) AND ($tok !=" ")) {
                    $infor=explode("::",$tok);
                    $album->create($this->id,$infor[0],$pos,$infor[1]);

                    $tok = strtok("++");
                    $pos++;
            }
        }

        return true;
    }

    function read($id) {

        parent::read($id);

        $sql = 'SELECT * FROM albums WHERE pk_album = '.($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->subtitle = $rs->fields['subtitle'];
        $this->agency = $rs->fields['agency'];
        $this->fuente = $rs->fields['fuente'];
        $this->cover = $rs->fields['cover'];

    }

    function update($data) {

        parent::update($data);

        $data['subtitle'] = (empty($data['subtitle']))? 0: $data['subtitle'];
        $data['fuente'] = (empty($data['fuente']))? 0: $data['fuente'];

        $sql = "UPDATE albums SET  `subtitle`=?, `agency`=?, `fuente`=?, `cover`=? ".
        		" WHERE pk_album=".($data['id']);

        if (!empty($data['name_img'])) {
            $this->cover =  $this->cropImageFront($data);
        }

        $values = array( $data['subtitle'],$data['agency'], $data['fuente'], $this->cover );

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        $album=new Album_photo();
		$album->delete_album($data['id']);

        if(isset($data['ordenAlbum'])){
            $tok = strtok($data['ordenAlbum'],"++");
            $pos=1;
            while (($tok !== false) AND ($tok !=" ")) {
                $infor = preg_split("@::@",$tok);
                $album->create($this->id,$infor[0],$pos,$infor[1]);
                $tok = strtok("++");
                $pos++;
            }
        }
	}

    /**
     * Create a cover for frontpage widget
     *
     * @param array $data image
     *
     * @return bool if cover was create
     */
    function cropImageFront($data) {


        if ($data['path_img'] && $data['name_img']) {

            $uploaddir = MEDIA_IMG_PATH.$data['path_img'];
            $image =   $uploaddir.$data['name_img'];

            $picture = new Imagick($image);
            $width = $picture->getImageWidth();
            $height = $picture->getImageHeight();

            // Scale original image for crop with visor size
            if ($height>0 and $width>0) {
                if($height>400 OR $width>600){
                    if( $width > $height) {
                        $w = 600;
                        $h = floor( ($height*$w) / $width );
                        $picture->scaleImage($w, $h, true);
                    } else {
                        $h = 400;
                        $w = floor( ($width*$h) / $height );
                        $picture->scaleImage($w, $h, true);
                    }
                    $height = $h;
                    $width = $w;
                }
            }

            $picture->resizeImage($width, $height,Imagick::FILTER_LANCZOS,1);
            $picture->cropImage($data['width'], $data['height'], $data['x1'], $data['y1']);
            $picture->resizeImage($this->widthCover, $this->heightCover,Imagick::FILTER_LANCZOS,1);


            $datos = pathinfo($image);     //sacamos infor del archivo
            $extension = strtolower($datos['extension']);
            $t = gettimeofday(); //Sacamos los microsegundos
            $micro = intval(substr($t['usec'], 0, 5)); //Le damos formato de 5digitos a los microsegundos
            $name = date("YmdHis") . $micro . "." . $extension;
            $cover = '/'.$this->widthCover."-".$this->heightCover.'-'. $name;

            $path = $uploaddir.$cover;
            if (!$picture->writeImage($path)) {
                return false;
            }
            chmod($path, 0777);

            return $data['path_img'].$cover;
        }

    }


    function remove($id) {

        parent::remove($id);

        $sql = 'DELETE FROM albums WHERE pk_album='.($id);

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $album=new Album_photo();
		$album->delete_album($id);
	}

    function set_favorite($status) {
        $GLOBALS['application']->dispatch('onBeforeSetFavorite', $this);

        if ($this->id == NULL) {
            return(false);
        }

        $sql = "UPDATE albums SET `favorite`=? WHERE pk_album=".$this->id;
        $values = array($status);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
        $GLOBALS['application']->dispatch('onAfterSetFavorite', $this);

        return(true);

    }

	//Lee de la tabla la relacion de galeria y fotos
    function get_album($id_album) {

        if ($id_album == NULL) {
            return(false);
        }
        $album_photos = array();

    	$sql = 'SELECT DISTINCT pk_photo, description, position FROM albums_photos '.
               'WHERE pk_album = ' .($id_album).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i=0;
        while (!$rs->EOF) {
        	$album_photos[$i][] = $rs->fields['pk_photo'];
        	$album_photos[$i][] = $rs->fields['position'];
        	$album_photos[$i][] = $rs->fields['description'];
          	$rs->MoveNext();
          	$i++;
        }

        return($album_photos);

    }


    function get_firstfoto_album($id_album) {

        $album_photo = array();
        $sql = 'SELECT * from albums_photos WHERE pk_album = ' .($id_album).
               ' ORDER BY position ASC LIMIT 1';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->fields) {
        	$album_photo['pk_photo'] = $rs->fields['pk_photo'];
        	$album_photo['description'] = $rs->fields['description'];

        	$sql = 'SELECT * FROM photos WHERE pk_photo = '.($rs->fields['pk_photo']);
       		$rs2 = $GLOBALS['application']->conn->Execute( $sql );

			if (!$rs2) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

                return;
			}

            $album_photo['name'] = $rs2->fields['name'];
            $album_photo['path_file'] = $rs2->fields['path_file'];
        }

        return $album_photo;

    }
}
