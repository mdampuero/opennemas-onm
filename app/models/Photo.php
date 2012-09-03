<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Message as m;
use Onm\Settings as s;
use Onm\StringUtils;

/**
 * Photo
 *
 * @package    Onm
 * @subpackage Model
 **/
class Photo extends Content
{
    public $pk_photo    = null;
    public $name        = null; //id del articulo
    public $path_file   = null;
    public $date        = null;
    public $size        = null;
    public $width       = null;
    public $height      = null;
    public $resolution  = null;
    public $type_img    = null;
    public $media_type  = null;
    public $author_name = null;

    public function __construct($id = null)
    {
        parent::__construct($id);
        if (!is_null($id)) {
            $this->read($id);
        }

        $this->content_type = 'Photo';
        $this->content_type_l10n_name = _('Image');
    }

    public function create($data)
    {
        $data['content_status'] = 1;
        /*$cat = $GLOBALS['application']->conn->
            GetOne('SELECT * FROM `content_categories` WHERE name = "'.
                    $this->content_type.'"');*/

        parent::create($data);
        //Categoria a la que pertenece.
        $sql = "INSERT INTO photos (`pk_photo`,`name`, `path_file`,
                                    `date`, `size`,`width`,
                                    `height`,`nameCat`, `type_img`,
                                    `author_name`,`media_type`) " .
                "VALUES (?,?,?, ?,?,?, ?,?,?, ?,?)";

        $values = array($this->id, $data["name"], $data["path_file"],
                        $data['date'], $data['size'], $data['width'],
                        $data['height'], $data['nameCat'], $data['type_img'],
                        $data['author_name'], $data['media_type']);

        $execution = $GLOBALS['application']->conn->Execute($sql, $values);

        if ($execution === false) {
            Application::logDatabaseError();

            return false;
        }

        return $this->id;
    }

    /**
     * Creates one photo register in the database from data and local file
     * TODO: this function must content the photo local_file
     *
     * @params array $data the data for the photo, must content the
     *                     photo local_file
     **/
    public function createFromLocalFile($dataSource, $dateForDirectory = null)
    {

        $filePath = $dataSource["local_file"];

        if (empty($filePath)) {
            return false;
        }

        // Check upload directory
        if (empty($dateForDirectory)) {
            $dateForDirectory = date("/Y/m/d/");
        }
        $uploadDir =
            MEDIA_PATH.DS.IMG_DIR.DS.$dateForDirectory.DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) {
            FilesManager::createDirectory($uploadDir);
        }

        $filePathInfo = pathinfo($filePath);     //sacamos infor del archivo

        // Getting information for creating
        $t                  = gettimeofday();
        $micro              = intval(substr($t['usec'], 0, 5));
        $finalPhotoFileName =
            date("YmdHis").$micro.".".strtolower($filePathInfo['extension']);
        $fileInformation    = new MediaItem($filePath);

        if (!array_key_exists('created', $dataSource)) {
            $dataSource['created'] = $fileInformation->mtime;
        }
        if (!array_key_exists('changed', $dataSource)) {
            $dataSource['changed'] = $fileInformation->mtime;
        }
        // Building information for the photo image
        $data = array(
            'title'        => $dataSource["title"],
            'name'         => $finalPhotoFileName,
            'path_file'    => $dateForDirectory,
            'fk_category'  => $dataSource["fk_category"],
            'category'     => $dataSource["fk_category"],
            'nameCat'      => $dataSource["category_name"],

           // 'created'      => $fileInformation->atime,
           // 'changed'      => $fileInformation->mtime,
            'created'      => $dataSource["created"],
            'changed'      => $dataSource["changed"],
            'date'         => $fileInformation->mtime,
            'size'         => round($fileInformation->size/1024, 2),
            'width'        => $fileInformation->width,
            'height'       => $fileInformation->height,
            'type_img'     => strtolower($filePathInfo['extension']),
            'media_type'   => 'image',

            'author_name'  => '',
            'pk_author'    => $_SESSION['userid'],
            'fk_publisher' => $_SESSION['userid'],
            'description'  => $dataSource['description'],
            'metadata'     => $dataSource["metadata"],
        );

        if (is_dir($uploadDir) && !is_writable($uploadDir)) {
            m::add(
                sprintf(
                    'Upload directory doesn\'t exists or you don\'t '
                    .'have enought privileges to write files there',
                    $uploadDir.$finalPhotoFileName
                ),
                m::ERROR
            );
            $importedID = null;
        }

        $fileCopied = copy(
            $dataSource['local_file'],
            realpath($uploadDir).DIRECTORY_SEPARATOR.$finalPhotoFileName
        );
        if ($fileCopied) {
            $photo = new Photo();
            $photoID = $photo->create($data);

            if ($photoID) {

                if (preg_match('/^(jpeg|jpg|gif|png)$/', strtolower($filePathInfo['extension']))) {
                    $imageThumbSize = s::get(
                        array(
                            'image_thumb_size',
                            'image_inner_thumb_size',
                            'image_front_thumb_size',
                        )
                    );

                    // Thumbnail handler
                    $thumb = new Imagick(
                        realpath($uploadDir).DIRECTORY_SEPARATOR.$finalPhotoFileName
                    );

                    // Article inner thumbnail
                    $thumb->thumbnailImage(
                        $imageThumbSize['image_front_thumb_size']['width'] ?: 480,
                        $imageThumbSize['image_front_thumb_size']['height'] ?: 250,
                        true
                    );
                    $thumb->writeImage(
                        $uploadDir.$imageThumbSize['image_thumb_size']['width']
                        . '-' . $imageThumbSize['image_thumb_size']['height']
                        . '-' . $finalPhotoFileName
                    );

                    // Generate frontpage thumbnails
                    $thumb->thumbnailImage(
                        $imageThumbSize['image_front_thumb_size']['width'] ?: 350,
                        $imageThumbSize['image_front_thumb_size']['height'] ?: 200,
                        true
                    );
                    $thumb->writeImage(
                        $uploadDir.$imageThumbSize['image_front_thumb_size']['width']
                        . '-' . $imageThumbSize['image_front_thumb_size']['height']
                        . '-' . $finalPhotoFileName
                    );

                    // Main thumbnail
                    $thumb->thumbnailImage(
                        $imageThumbSize['image_thumb_size']['width'] ?: 140,
                        $imageThumbSize['image_thumb_size']['height'] ?: 100,
                        true
                    );
                    //Write the new image to a file
                    $thumb->writeImage(
                        $uploadDir.$imageThumbSize['image_thumb_size']['width']
                        .'-'.$imageThumbSize['image_thumb_size']['height']
                        .'-'.$finalPhotoFileName
                    );

                }

            } else {
                Application::getLogger()->notice(
                    sprintf(
                        'EFE Importer: Unable to register the '
                        .'photo object %s (destination: %s).',
                        $dataSource['local_file'],
                        $uploadDir.$finalPhotoFileName
                    )
                );
                m::add(
                    sprintf(
                        'Unable to register the photo object into OpenNemas.',
                        $uploadDir.$finalPhotoFileName
                    ),
                    m::ERROR
                );
            }

            $importedID = $photoID;

        } else {

            $importedID = null;

            Application::getLogger()->notice(
                sprintf(
                    'EFE Importer: Unable to creathe the '
                    .'photo file %s (destination: %s).',
                    $dataSource['local_file'],
                    $uploadDir.$finalPhotoFileName
                )
            );
            m::add(
                sprintf(
                    'Unable to copy the file of the photo '
                    .'related in EFE importer to the article.',
                    $uploadDir.$finalPhotoFileName
                ),
                m::ERROR
            );
        }

        return $importedID;
    }

    /**
     * Creates one photo register in the database from data and local file
     * TODO: this function must content the photo local_file
     *
     * @params array $data the data for the photo,
     *                     must content the photo local_file
     **/
    public function createFromLocalFileAjax($dataSource)
    {

        $filePath = $dataSource["local_file"];
        $originalFileName = $dataSource['original_filename'];

        if (!empty($filePath)) {
             // Check upload directory
            $date = new DateTime();
            $urn = "urn:newsml:"
                .SITE
                .":"
                .$date->format("Ymd\THisO")
                .":"
                .StringUtils::cleanFileName($originalFileName)
                .":2";

            $dateForDirectory = $date->format("/Y/m/d/");
            $uploadDir =
                MEDIA_PATH.DS.IMG_DIR.DS.$dateForDirectory.DIRECTORY_SEPARATOR;

            if (!is_dir($uploadDir)) {
                FilesManager::createDirectory($uploadDir);
            }

            $filePathInfo = pathinfo($originalFileName);

            // Getting information for creating
            $t                  = gettimeofday();
            $micro              = intval(substr($t['usec'], 0, 5));
            $finalPhotoFileName = $date->format("YmdHis")
                . $micro . "." . strtolower($filePathInfo['extension']);
            $fileInformation    = new MediaItem($filePath);

            // Building information for the photo image
            $data = array(
                'title'        => $originalFileName,
                'name'         => $finalPhotoFileName,
                'path_file'    => $dateForDirectory,
                'fk_category'  => $dataSource["fk_category"],
                'category'     => $dataSource["fk_category"],
                'nameCat'      => $dataSource["category_name"],

                'created'      => $fileInformation->atime,
                'changed'      => $fileInformation->mtime,
                'date'         => $fileInformation->mtime,
                'size'         => round($fileInformation->size/1024, 2),
                'width'        => $fileInformation->width,
                'height'       => $fileInformation->height,
                'type_img'     => strtolower($filePathInfo['extension']),
                'media_type'   => 'image',

                'author_name'  => '',
                'pk_author'    => $_SESSION['userid'],
                'fk_publisher' => $_SESSION['userid'],
                'description'  => $dataSource['description'],
                'metadata'     => $dataSource["metadata"],
                'urn_source'   => $urn,
            );

            if (is_dir($uploadDir) && !is_writable($uploadDir)) {
                throw new Exception(
                    sprintf(
                        'Upload directory doesn\'t exists or you don\'t '
                        .'have enought privileges to write files there',
                        $uploadDir.$finalPhotoFileName
                    )
                );
            }

            $fileCopied = copy(
                $dataSource['local_file'],
                realpath($uploadDir).DIRECTORY_SEPARATOR.$finalPhotoFileName
            );

            if ($fileCopied) {

                $photo = new Photo();
                $photoID = $photo->create($data);

                if ($photoID) {

                    if (preg_match('/^(jpeg|jpg|gif|png)$/', strtolower($filePathInfo['extension']))) {

                        $imageThumbSize = s::get(
                            array(
                                'image_thumb_size',
                                'image_inner_thumb_size',
                                'image_front_thumb_size',
                            )
                        );

                        // Thumbnail handler
                        $thumb = new Imagick(realpath($uploadDir).DIRECTORY_SEPARATOR.$finalPhotoFileName);

                        // Article inner thumbnail
                        $thumb->thumbnailImage(
                            $imageThumbSize['image_front_thumb_size']['width'] ?: 480,
                            $imageThumbSize['image_front_thumb_size']['height'] ?: 250,
                            true
                        );
                        $thumb->writeImage(
                            $uploadDir.$imageThumbSize['image_thumb_size']['width']
                            .'-'.$imageThumbSize['image_thumb_size']['height']
                            .'-'.$finalPhotoFileName
                        );

                        // Generate frontpage thumbnails
                        $thumb->thumbnailImage(
                            $imageThumbSize['image_front_thumb_size']['width'] ?: 350,
                            $imageThumbSize['image_front_thumb_size']['height'] ?: 200,
                            true
                        );
                        $thumb->writeImage(
                            $uploadDir.$imageThumbSize['image_front_thumb_size']['width']
                            .'-'.$imageThumbSize['image_front_thumb_size']['height']
                            .'-'.$finalPhotoFileName
                        );

                        // Main thumbnail
                        $thumb->thumbnailImage(
                            $imageThumbSize['image_thumb_size']['width'] ?: 140,
                            $imageThumbSize['image_thumb_size']['height'] ?: 100,
                            true
                        );
                        //Write the new image to a file
                        $thumb->writeImage(
                            $uploadDir.$imageThumbSize['image_thumb_size']['width']
                            .'-'.$imageThumbSize['image_thumb_size']['height']
                            .'-'.$finalPhotoFileName
                        );
                    }

                } else {
                    Application::getLogger()->notice(
                        sprintf(
                            'EFE Importer: Unable to register '
                            .'the photo object %s (destination: %s).',
                            $dataSource['local_file'],
                            $uploadDir.$finalPhotoFileName
                        )
                    );
                    throw new Exception(
                        sprintf(
                            'Unable to register the photo object into OpenNemas.',
                            $uploadDir.$finalPhotoFileName
                        )
                    );
                }

                $photo = new Photo($photoID);

            } else {

                $importedID = null;

                Application::getLogger()->notice(
                    sprintf(
                        'EFE Importer: Unable to creathe the '
                        .'photo file %s (destination: %s).',
                        $dataSource['local_file'],
                        $uploadDir.$finalPhotoFileName
                    )
                );
                throw new Exception(
                    sprintf(
                        'Unable to copy the file of the photo '
                        .'related in EFE importer to the article.',
                        $uploadDir.$finalPhotoFileName
                    )
                );
            }
        }

        return $photo;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM photos WHERE pk_photo =?';
        $values = array($id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        //$this->load( $rs->fields );
        $this->pk_photo    = $rs->fields['pk_photo'];
        $this->name        = $rs->fields['name'];
        $this->path_file   = $rs->fields['path_file'];
        $this->size        = $rs->fields['size'];
        $this->resolution  = $rs->fields['resolution'];
        $this->width       = $rs->fields['width'];
        $this->height      = $rs->fields['height'];
        $this->nameCat     = $rs->fields['nameCat'];
        $this->type_img    = $rs->fields['type_img'];
        $this->author_name = $rs->fields['author_name'];
        $this->media_type  = $rs->fields['media_type'];
        $this->description = ($this->description);
        $this->metadata    = ($this->metadata);
        $this->date        = $rs->fields['date'];
        $this->color       = $rs->fields['color'];
        $this->address     = $rs->fields['address'];
    }

    public function readAllData($id)
    {
        $photo = new stdClass();
        $this->read($id);

        if (empty($this)) {
            return $photo;
        }

        $photo->pk_photo    = $this->pk_photo;
        $photo->id          = $this->pk_photo ;
        $photo->name        = $this->name ;
        $photo->title       = $this->title ;
        $photo->description = $this->description ;
        $photo->metadata    = $this->metadata ;
        $photo->path_file   = $this->path_file;
        $photo->size        = $this->size;
        $photo->resolution  = $this->resolution;
        $photo->width       = $this->width;
        $photo->height      = $this->height;
        $photo->nameCat     = $this->nameCat;
        $photo->type_img    = $this->type_img;
        $photo->category    = $this->category;
        $photo->author_name = $this->author_name;
        $photo->media_type  = $this->media_type;
        $photo->color       = $this->color;
        $photo->date        = $this->date;
        $photo->address     = $this->address;
        $photo->latlong     = '';
        $photo->infor       = '';

        if (!empty($photo->address)) {
            $positions = explode(',', $photo->address);
            if (is_array($positions)) {
                $photo->latlong = array(
                    'lat' => $positions[0],
                    'long' => $positions[1],
                );
            }
        }

        $image = MEDIA_IMG_PATH . $this->path_file.$this->name;

        if (is_file($image)) {
            $size = getimagesize($image, $info);

            switch ($size['mime']) {
                case "image/gif":
                    $photo->infor = _("The image type is GIF </br>");

                    break;
                case "image/png":
                    $photo->infor = _("The image type is PNG </br>");

                    break;
                case "image/bmp":
                    $photo->infor = _("The image type is BMP </br>");

                    break;
                case 'image/jpeg':

                    $exif = array();
                    if (isset($info)) {
                        foreach ($info as $key => $val) {
                            if ($key != 'APP1') {
                                $exifData = read_exif_data($image, 0, true);
                                break;
                            }
                        }
                    }
                    if (!empty($exifData)) {
                        $photo->exif = $exifData;
                    } else {
                        $photo->exif = null;
                    }

                    if (empty($exif)) {
                        $photo->infor .= _("No availabel EXIF data</br>");

                    } else {

                        if (empty($photo->color)) {
                            if ($exifData['COMPUTED']['IsColor']==0) {
                                $photo->color= 'BN';
                            } else {
                                $photo->color= 'color';
                            }
                        }
                        if (isset($exifData['IFD0'])) {
                            if (empty($photo->resolution)
                                && !is_null($exifData['IFD0']['XResolution'])
                            ) {
                                $photo->resolution =
                                    $exifData['IFD0']['XResolution'];
                            }
                            if (empty($photo->date)
                                && !is_null($exifData['FILE']['FileDateTime'])
                            ) {
                                $photo->date= $exifData['FILE']['FileDateTime'];
                            }
                        }
                    }

                    if (isset($info['APP13'])) {
                        $iptc = iptcparse($info['APP13']);

                        if (is_array($iptc)) {

                            $errorReporting = ini_get('error_reporting');
                            error_reporting('E_ALL');

                            if (isset($iptc["2#025"])) {
                                $keywordcount = count($iptc["2#025"]);
                                $keywords     = $iptc["2#025"][0];

                                for ($i=1; $i<$keywordcount; $i++) {
                                    $keywords .= ", ".$iptc["2#025"][$i]  ;
                                }
                            } else {
                                $keywords = '';
                            }

                            $myiptc['Keywords']            =$keywords;
                            $myiptc['Caption']             = $iptc["2#120"][0];

                            $myiptc['Graphic_name']        = $iptc["2#005"][0];
                            $myiptc['Urgency']             = $iptc["2#010"][0];
                            $myiptc['Category']            = $iptc["2#015"][0];
                            $myiptc['Program']             = $iptc["2#065"][0];
                            // note that sometimes supp_categories
                            // contans multiple entries
                            $myiptc['Supp_categories']     = $iptc["2#020"][0];
                            $myiptc['Spec_instr']          = $iptc["2#040"][0];
                            $myiptc['Creation_date']       = $iptc["2#055"][0];
                            $myiptc['Photographer']        = $iptc["2#080"][0];
                            $myiptc['Credit_byline_title'] = $iptc["2#085"][0];
                            $myiptc['City']                = $iptc["2#090"][0];
                            $myiptc['State']               = $iptc["2#095"][0];
                            $myiptc['Country']             = $iptc["2#101"][0];
                            $myiptc['Otr']                 = $iptc["2#103"][0];
                            $myiptc['Headline']            = $iptc["2#105"][0];
                            $myiptc['Source']              = $iptc["2#110"][0];
                            $myiptc['Photo_source']        = $iptc["2#183"][0];

                            $myiptc = array_map('map_entities', $myiptc);
                            $photo->myiptc = $myiptc;

                            if (empty($photo->description)) {
                                $photo->description= $myiptc['Caption'];
                            }

                            if (empty($photo->metadata)) {
                                $photo->metadata = map_entities($keywords);
                            }

                            if (empty($photo->author_name)) {
                                $photo->author_name = $myiptc['Photographer'];
                            }

                            ini_set($errorReporting);

                        } else {
                            $photo->infor .=  _("No availabel IPTC data</br>");
                        }
                    }
                    break;
                default:
                    break;
            } // endswitch;

        } else {
            $photo->infor .=  _("Invalid image file");
        }

        return $photo;
    }

    //FIXME: No se usa
    public function update($data)
    {
        parent::update($data);
        $sql = "UPDATE photos SET `pk_photo`=?, `name`=?, `path_file`=?, "
             . "`size`=?, `width`=?,`height`=?,`type_img`=?, `author_name`=?, "
             . "`date`=?, `color`=? "
             . "WHERE pk_photo=?";

        $values = array(
            $data['pk_photo'], $data['name'], $data['path_file'],
            $data['size'], $data['width'], $data['height'],
            $data['type_img'], $data['author_name'],
            $data['date'], $data['color'], $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    //FIXME: Actualiza metadata description y author de una photo
    public function setData($data)
    {
        $data['pk_author'] = $_SESSION['userid'];
        $data['fk_user_last_editor'] = $_SESSION['userid'];
        if (!isset($data['resolution'])) {
            $data['resolution'] = '';
        }
        parent::update($data);

        $sql = "UPDATE `photos` SET `author_name`=?, `address`=?, `color`=?, "
             . "`date`=?, `resolution`=? WHERE `pk_photo`=?";

        $values = array(
            $data['author_name'],
            $data['address'],
            $data['color'],
            $data['date'],
            $data['resolution'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }

        return true;
    }

    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM photos WHERE pk_photo=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }

        $image      = MEDIA_IMG_PATH . $this->path_file.$this->name;
        $thumbimage = MEDIA_IMG_PATH . $this->path_file.'140-100-'.$this->name;

        if (file_exists($image)) {
            @unlink($image);
        }
        if (file_exists($thumbimage)) {
            @unlink($thumbimage);
        }

    }

    public function updatePath($path, $id)
    {
        $sql = "UPDATE `photos` SET `path_file`=? WHERE `pk_photo`=?";

        $values = array($path, $id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return;
        }
    }

    //function check image is used by article, album, advertisement
    // return array id contents use this image.

    public function isUsed($id)
    {
        $sqlAlbums = 'SELECT pk_album FROM albums_photos WHERE pk_photo=?';
        $sqlAds = 'SELECT pk_advertisement FROM advertisements WHERE path=?';
        $sqlarticles = 'SELECT pk_article FROM articles WHERE img1=? OR img2=?';
        // $sql= "$sql3 UNION $sql1 UNION $sql2";

        $result = array();
        $values = array($id);

        $rs = $GLOBALS['application']->conn->Execute($sqlAlbums, $values);
        if ($rs) {
            while (!$rs->EOF) {
                $result[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }

        $rs = $GLOBALS['application']->conn->Execute($sqlAds, $values);
        if ($rs) {
            while (!$rs->EOF) {
                $result[] = $rs->fields[0];
                $rs->MoveNext();
            }
        }

        $values = array($id, $id);
        $rs = $GLOBALS['application']->conn->Execute($sqlarticles, $values);
        if ($rs) {
            while (!$rs->EOF) {
                $result[]=$rs->fields[0];
                $rs->MoveNext();
            }
        }

        return $result;
    }
}

