<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the CRUD actions over albums.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Album extends Content
{
    public $pk_album = NULL;
    public $subtitle = NULL;
    public $agency = NULL;
    public $fuente = NULL;
    public $cover = NULL;
    public $widthCover = 300;
    public $heightCover = 240;


    /**
     * Initializes the Album class.
     *
     * @param strin $id the id of the album.
     **/
    public function __construct($id=NULL)
    {
        parent::__construct($id);

        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = __CLASS__;
    }

    /**
     * Magic function to get uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     **/
    public function __get($name)
    {

        switch ($name) {

            case 'uri': {
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'album',
                    array(
                        'id' => sprintf('%06d',$this->id),
                        'date' => date('YmdHis', strtotime($this->created)),
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
                $contentTypeName = $GLOBALS['application']->conn->Execute(
                    'SELECT * FROM `content_types` '
                    .'WHERE pk_content_type = "'. $this->content_type
                    .'" LIMIT 1'
                );

                if (isset($contentTypeName->fields['name'])) {
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
     * Creates an album from array and stores it in db
     *
     * @param array $data the data of the album
     *
     * @return bool true if the object was stored
     */
    public function create($data)
    {

        parent::create($data);

        $this->cover = $this->cropImageFront($data);

        $data['subtitle'] = (empty($data['subtitle']))? '': $data['subtitle'];
        $data['fuente'] = (empty($data['fuente']))? '': $data['fuente'];

        $sql = "INSERT INTO albums "
                ." (`pk_album`,`subtitle`, `agency`,`fuente`,`cover`) "
                ." VALUES (?,?,?,?,?)";

        $values = array(
            $this->id,
            $data["subtitle"],
            $data["agency"],
            $data["fuente"],
            $this->cover
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return(false);
        }

        $data['id']=$this->id;

        if (isset($data['ordenAlbum'])) {
            $tok = strtok($data['ordenAlbum'], "++");
            $pos=1;
            $albumPhoto=new Album_photo();
            while (($tok !== false) AND ($tok !=" ")) {
                    $infor=explode("::", $tok);
                    $albumPhoto->create($this->id, $infor[0], $pos, $infor[1]);

                    $tok = strtok("++");
                    $pos++;
            }
        }

        return true;
    }

    /**
     * Fetches one Album by its id.
     *
     * @param string $id the album id to get info from.
     **/
    public function read($id)
    {

        parent::read($id);

        $sql = 'SELECT * FROM albums WHERE pk_album = '.($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

        $this->pk_album = $rs->fields['pk_album'];
        $this->subtitle = $rs->fields['subtitle'];
        $this->agency = $rs->fields['agency'];
        $this->fuente = $rs->fields['fuente'];
        $this->cover = $rs->fields['cover'];

    }

    /**
     * Updates the information of the album given an array of key-values
     *
     * @param array $data the new data to update the album
     **/
    public function update($data)
    {

        parent::update($data);

        $data['subtitle'] = (empty($data['subtitle']))? 0: $data['subtitle'];
        $data['fuente'] = (empty($data['fuente']))? 0: $data['fuente'];

        $sql = "UPDATE albums "
                ."SET  `subtitle`=?, `agency`=?, `fuente`=?, `cover`=? "
                ." WHERE pk_album=".($data['id']);

        if (!empty($data['name_img'])) {
            $this->cover =  $this->cropImageFront($data);
        }

        $values = array(            $data['subtitle'],
            $data['agency'],
            $data['fuente'],
            $this->cover
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }

        $album=new Album_photo();
        $album->delete_album($data['id']);

        if (isset($data['ordenAlbum'])) {
            $tok = strtok($data['ordenAlbum'], "++");
            $pos=1;
            while (($tok !== false) AND ($tok !=" ")) {
                $infor = preg_split("@::@", $tok);
                $album->create($this->id, $infor[0], $pos, $infor[1]);
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
    public function cropImageFront($data)
    {

        $configurations = \Onm\Settings::get('album_settings');
        $this->widthCover = $configurations['crop_width'];
        $this->heightCover = $configurations['crop_height'];

        if ($data['path_img'] && $data['name_img']) {

            $uploaddir = MEDIA_IMG_PATH.$data['path_img'];
            $image =   $uploaddir.$data['name_img'];

            $picture = new Imagick($image);
            $width = $picture->getImageWidth();
            $height = $picture->getImageHeight();

            // Scale original image for crop with visor size
            if ($height>0 and $width>0) {
                if ($height>400 OR $width>600) {
                    if ($width > $height) {
                        $w = 600;
                        $h = floor(($height*$w) / $width);
                        $picture->scaleImage($w, $h, true);
                    } else {
                        $h = 400;
                        $w = floor(($width*$h) / $height);
                        $picture->scaleImage($w, $h, true);
                    }
                    $height = $h;
                    $width = $w;
                }
            }

            $picture->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
            $picture->cropImage(
                $data['width'], $data['height'],
                $data['x1'], $data['y1']
            );
            $picture->resizeImage(
                $this->widthCover, $this->heightCover,
                Imagick::FILTER_LANCZOS,
                1
            );

            $datos = pathinfo($image);     // Fetch file info
            $extension = strtolower($datos['extension']);
            $t = gettimeofday();             // Fetch the actual microsecs
            $micro = intval(substr($t['usec'], 0, 5)); // Get just 5 digits
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


    /**
     * Removes an album by a given id.
     *
     * @param string $id the album id
     **/
    public function remove($id)
    {

        parent::remove($id);

        $sql = 'DELETE FROM albums WHERE pk_album='.($id);
        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
        $album = new Album_photo();
        $album->delete_album($id);
    }

    //Lee de la tabla la relacion de galeria y fotos
    public function get_album($albumID)
    {

        if ($albumID == NULL) {
            return(false);
        }
        $photosAlbum = array();

        $sql = 'SELECT DISTINCT pk_photo, description, position'
                .' FROM albums_photos '
               .'WHERE pk_album = ' .($albumID).' ORDER BY position ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $i=0;
        while (!$rs->EOF) {
            $photosAlbum[$i][] = $rs->fields['pk_photo'];
            $photosAlbum[$i][] = $rs->fields['position'];
            $photosAlbum[$i][] = $rs->fields['description'];
              $rs->MoveNext();
              $i++;
        }

        return($photosAlbum);

    }

    /**
     * Gets the first photo from album.
     *
     * @param string $albumID the id of the album.
     *
     * @return array key-value array with properties of the image
     **/
    public function get_firstfoto_album($albumID)
    {

        $photoAlbum = array();
        $sql = 'SELECT * from albums_photos WHERE pk_album = ' .($albumID).
               ' ORDER BY position ASC LIMIT 1';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->fields) {

            $photoAlbum['pk_photo'] = $rs->fields['pk_photo'];
            $photoAlbum['description'] = $rs->fields['description'];

            $sql = 'SELECT * FROM photos'
                    .' WHERE pk_photo = '.($rs->fields['pk_photo']);
            $queryExec = $GLOBALS['application']->conn->Execute($sql);

            if (!$queryExec) {
                $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
                $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
                return;
            }

            $photoAlbum['name'] = $queryExec->fields['name'];
            $photoAlbum['path_file'] = $queryExec->fields['path_file'];

        }

        return $photoAlbum;

    }
}
