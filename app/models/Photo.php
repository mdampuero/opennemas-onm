<?php
/**
 * Contains the Photo class definition
 *
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Model
 */
use Onm\Message as m;
use Onm\StringUtils;

/**
 * Photo class
 *
 * @package Model
 */
class Photo extends Content
{
    /**
     * Photo id
     *
     * @var int
     **/
    public $pk_photo    = null;

    /**
     * File name of the photo
     *
     * @var string
     **/
    public $name        = null;

    /**
     * Full path to the photo file
     *
     * @var string
     **/
    public $path_file   = null;

    /**
     * The size of the image
     *
     * @var int
     **/
    public $size        = null;

    /**
     * The width of the image
     *
     * @var int
     **/
    public $width       = null;

    /**
     * The height of the image
     *
     * @var int
     **/
    public $height      = null;

    /**
     * The copyright of the image
     *
     * @var string
     **/
    public $author_name = null;

    /**
     * Initializes the Photo object instance given an id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object instance
     **/
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Image');

        parent::__construct($id);
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __get($propertyName)
    {
        switch ($propertyName) {
            case 'type_img':
                $this->type_img = pathinfo($this->name, PATHINFO_EXTENSION);

                return $this->type_img;
                break;
        }
        parent::__get($propertyName);
    }

    /**
     * Creates a new photo given an array of information
     *
     * @param array $data the photo information
     *
     * @return int the photo id
     * @return boolean false if the photo was not created
     **/
    public function create($data)
    {
        $data['content_status'] = 1;

        parent::create($data);

        $sql = "INSERT INTO photos
                    (`pk_photo`, `name`, `path_file`, `size`,`width`, `height`, `nameCat`, `author_name`)
                VALUES
                    (?,?,?, ?,?,?, ?,?)";

        $values = array(
            $this->id, $data["name"], $data["path_file"],
            $data['size'], $data['width'], $data['height'],
            $data['nameCat'], $data['author_name']
        );

        $execution = $GLOBALS['application']->conn->Execute($sql, $values);

        if ($execution === false) {
            return false;
        }

        return $this->id;
    }

    /**
     * Creates one photo register in the database from data and local file
     * TODO: this function must content the photo local_file
     *
     * @param array $dataSource the data for the photo, must content the photo local_file
     * @param string $dateForDirectory the date for the directory
     *
     * @return int the id of the photo created
     * @return boolean false if the photo was not created
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
        $uploadDir = MEDIA_PATH.DS.IMG_DIR.DS.$dateForDirectory.DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) {
            FilesManager::createDirectory($uploadDir);
        }

        $filePathInfo = pathinfo($filePath);     //sacamos infor del archivo

        // Getting information for creating
        $t                  = gettimeofday();
        $micro              = intval(substr($t['usec'], 0, 5));
        $finalPhotoFileName = date("YmdHis").$micro.".".strtolower($filePathInfo['extension']);
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
            'created'      => $dataSource["created"],
            'changed'      => $dataSource["changed"],
            'date'         => $fileInformation->mtime,
            'size'         => round($fileInformation->size/1024, 2),
            'width'        => $fileInformation->width,
            'height'       => $fileInformation->height,

            'author_name'  => $dataSource['author_name'],
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

            if (!$photoID) {
                $logger = getService('logger');
                $logger->notice(
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

            $logger = getService('logger');
            $logger->notice(
                sprintf(
                    'Unable to create the photo file %s (destination: %s).',
                    $dataSource['local_file'],
                    $uploadDir.$finalPhotoFileName
                )
            );
            m::add(
                sprintf(
                    'Unable to copy the file of the photo related to the article. ',
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
     * @param array $dataSource the data for the photo, must content the photo local_file
     *
     * @return Photo the photo object
     **/
    public function createFromLocalFileAjax($dataSource)
    {
        $photo = null;
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
                'author_name'  => !isset($dataSource['author_name']) ?'':$dataSource['author_name'],
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

                if (!$photoID) {
                    $logger = getService('logger');
                    $logger->notice(
                        sprintf(
                            'EFE Importer: Unable to register the photo object %s (destination: %s).',
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
                $logger = getService('logger');
                $logger->notice(
                    sprintf(
                        'EFE Importer: Unable to creathe the photo file %s (destination: %s).',
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

    /**
     * Creates one photo using imagemagick and register in the database
     *
     * @param array $dataSource the data for the photo, must content the photo local_file
     *
     * @return int the id of the photo created
     * @return boolean false if the photo was not created
     **/
    public function createWithImageMagick($dataSource, $dateForDirectory = null)
    {
        $photo = null;
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
            $mediaDir =
                MEDIA_PATH.DS.IMG_DIR.DS.$dateForDirectory.DIRECTORY_SEPARATOR;

            if (!is_dir($mediaDir)) {
                FilesManager::createDirectory($mediaDir);
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

                'author_name'  => !isset($dataSource['author_name']) ?'':$dataSource['author_name'],
                'pk_author'    => $_SESSION['userid'],
                'fk_publisher' => $_SESSION['userid'],
                'description'  => $dataSource['description'],
                'metadata'     => $dataSource["metadata"],
                'urn_source'   => $urn,
            );

            if (is_dir($mediaDir) && !is_writable($mediaDir)) {
                throw new Exception(
                    sprintf(
                        'Upload directory doesn\'t exists or you don\'t '
                        .'have enought privileges to write files there',
                        $mediaDir.$finalPhotoFileName
                    )
                );
            }

            $imageCreated =  new Imagick($dataSource['local_file']);

            $imageCreated->writeImage(realpath($mediaDir).DIRECTORY_SEPARATOR.$finalPhotoFileName);
            //$imageCreated->destroy();

            if ($imageCreated) {

                $photo = new Photo();
                $photoID = $photo->create($data);

                if (!$photoID) {
                    $logger = getService('logger');
                    $logger->notice(
                        sprintf(
                            'Unable to register the photo object %s (destination: %s).',
                            $dataSource['local_file'],
                            $mediaDir.$finalPhotoFileName
                        )
                    );
                    throw new Exception(
                        sprintf(
                            'Unable to register the photo object.',
                            $mediaDir.$finalPhotoFileName
                        )
                    );
                }

                $photo = new Photo($photoID);

            } else {
                $logger = getService('logger');
                $logger->notice(
                    sprintf(
                        'Participa: Unable to creathe the photo file %s (destination: %s).',
                        $dataSource['local_file'],
                        $mediaDir.$finalPhotoFileName
                    )
                );
                throw new Exception(
                    sprintf(
                        'Unable to create photo.',
                        $mediaDir.$finalPhotoFileName
                    )
                );
            }
        }

        return $photo;



    }
    /**
     * Returns an instance of the Photo object given a photo id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM photos WHERE pk_photo =?';
        $values = array($id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if (!$rs) {
            return null;
        }

        $this->pk_photo    = $rs->fields['pk_photo'];
        $this->name        = $rs->fields['name'];
        $this->path_file   = $rs->fields['path_file'];
        if (!empty($rs->fields['path_file'])) {
            $this->path_img = $rs->fields['path_file'].DS.$rs->fields['name'];
        }
        $this->size        = $rs->fields['size'];
        $this->width       = $rs->fields['width'];
        $this->height      = $rs->fields['height'];
        $this->nameCat     = $rs->fields['nameCat'];
        $this->author_name = $rs->fields['author_name'];
        $this->description = ($this->description);
        $this->metadata    = ($this->metadata);
        $this->address     = $rs->fields['address'];

        if (!empty($photo->address)) {
            $positions = explode(',', $photo->address);
            if (is_array($positions)) {
                $photo->latlong = array(
                    'lat' => $positions[0],
                    'long' => $positions[1],
                );
            }
        }
        return $this;
    }

    /**
     * Returns an object with all information of a photo given a photo id
     *
     * @param int $id the photo id to load
     *
     * @return stdClass a dummy object with the photo information
     **/
    public function getMetaData()
    {
        $image = MEDIA_IMG_PATH . $this->path_file.$this->name;

        if (is_file($image)) {
            $size = getimagesize($image, $info);

            switch ($size['mime']) {
                case "image/gif":
                    $this->infor = _("The image type is GIF </br>");

                    break;
                case "image/png":
                    $this->infor = _("The image type is PNG </br>");

                    break;
                case "image/bmp":
                    $this->infor = _("The image type is BMP </br>");

                    break;
                case 'image/jpeg':

                    $exif = array();
                    if (isset($info)) {
                        foreach ($info as $key => $val) {
                            if ($key != 'APP1') {
                                $exifData = @read_exif_data($image, 0, true);
                                break;
                            }
                        }
                    }
                    if (!empty($exifData)) {
                        $this->exif = $exifData;
                    } else {
                        $this->exif = null;
                    }

                    if (empty($exif)) {
                        $this->infor .= _("No availabel EXIF data</br>");

                    } else {

                        if (empty($this->color)) {
                            if ($exifData['COMPUTED']['IsColor']==0) {
                                $this->color= 'BN';
                            } else {
                                $this->color= 'color';
                            }
                        }
                        if (isset($exifData['IFD0'])) {
                            if (empty($this->resolution)
                                && !is_null($exifData['IFD0']['XResolution'])
                            ) {
                                $this->resolution =
                                    $exifData['IFD0']['XResolution'];
                            }
                            if (empty($this->date)
                                && !is_null($exifData['FILE']['FileDateTime'])
                            ) {
                                $this->date= $exifData['FILE']['FileDateTime'];
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
                            $this->myiptc = $myiptc;

                            if (empty($this->description)) {
                                $this->description= $myiptc['Caption'];
                            }

                            if (empty($this->metadata)) {
                                $this->metadata = map_entities($keywords);
                            }

                            if (empty($this->author_name)) {
                                $this->author_name = $myiptc['Photographer'];
                            }

                            ini_set($errorReporting);

                        } else {
                            $this->infor .=  _("No availabel IPTC data</br>");
                        }
                    }
                    break;
                default:
                    break;
            } // endswitch;

        } else {
            $this->infor .=  _("Invalid image file");
        }

        return $this;
    }

    /**
     * Updates the photo object given an array with information
     *
     * @see  Photo::setData()
     *
     * @param array $data the new photo inforamtion
     *
     * @return boolean true if the photo was updated properly
     **/
    public function update($data)
    {
        $data['fk_author'] = $_SESSION['userid'];
        $data['fk_user_last_editor'] = $_SESSION['userid'];

        parent::update($data);
        $sql = "UPDATE photos
                SET `name`=?, `path_file`=?, `size`=?,
                    `width`=?, `height`=?,
                    `author_name`=?, `address`=?
                WHERE pk_photo=?";

        $values = array(
            $this->name,
            $this->path_file,
            $this->size,
            $this->width,
            $this->height,
            $data['author_name'],
            $data['address'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Removes a photo given its id
     *
     * @param int $id the photo id to delete
     *
     * @return boolean true if the photo was deleted
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM photos WHERE pk_photo=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if ($rs === false) {
            return false;
        }

        $image = MEDIA_IMG_PATH . $this->path_file.$this->name;

        if (file_exists($image)) {
            @unlink($image);
        }

        return true;
    }

    /**
     * Updates the file path of a photo
     *
     * @param string $path the new file path
     * @param int $id the photo id
     *
     * @return boolean true if the file path was changed
     **/
    public function updatePath($path, $id)
    {
        $sql = "UPDATE `photos` SET `path_file`=? WHERE `pk_photo`=?";

        $values = array($path, $id);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Returns the photo path associated to an id.
     *
     * @param string $id the photo id.
     *
     * @return int the photo path
     */
    public static function getPhotoPath($id)
    {
        $sql = 'SELECT `path_file`, `name` FROM photos WHERE pk_photo = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return false;
        }

        return (string) $rs->fields['path_file'].DS.$rs->fields['name'];
    }

    /**
     * Checks if a image is being used by another content
     *
     * @param int $id the photo id
     *
     * @return array the list of content ids that are using this photo
     **/
    public function isUsed($id)
    {
        $sqlAlbums   = 'SELECT pk_album FROM albums_photos WHERE pk_photo=?';
        $sqlAds      = 'SELECT pk_advertisement FROM advertisements WHERE path=?';
        $sqlarticles = 'SELECT pk_article FROM articles WHERE img1=? OR img2=?';

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

    /**
     * Removes photos given its id
     *
     * @param array $arrayId the photo ids to delete
     *
     * @return boolean true if the photo was deleted
     **/
    public static function batchDelete($arrayIds)
    {
        $contents = implode(', ', $arrayIds);

        $sql = 'SELECT  path_file, name  FROM photos WHERE pk_photo IN ('.$contents.')';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        while (!$rs->EOF) {
            $image      = MEDIA_IMG_PATH . $rs->fields['path_file'].$rs->fields['name'];

            if (file_exists($image)) {
                @unlink($image);
            }

            $rs->MoveNext();
        }

        $sql = 'DELETE FROM photos WHERE `pk_photo` IN ('.$contents.')';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        return true;

    }
}
