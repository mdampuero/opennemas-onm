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
use Onm\StringUtils;
use Framework\Component\MIME\MimeTypeTool;

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
     */
    public $pk_photo = null;

    /**
     * File name of the photo
     *
     * @var string
     */
    public $name = null;

    /**
     * Full path to the photo file
     *
     * @var string
     */
    public $path_file = null;

    /**
     * The size of the image
     *
     * @var int
     */
    public $size = null;

    /**
     * The width of the image
     *
     * @var int
     */
    public $width = null;

    /**
     * The height of the image
     *
     * @var int
     */
    public $height = null;

    /**
     * The copyright of the image
     *
     * @var string
     */
    public $author_name = null;

    /**
     * The photo information.
     *
     * @var string
     */
    public $infor = null;

    /**
     * Initializes the Photo object instance given an id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object instance
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Image');

        parent::__construct($id);
    }

    /**
     * Overloads the object properties with an array of the new ones
     *
     * @param array $properties the list of properties to load
     *
     * @return void
     */
    public function load($properties)
    {
        parent::load($properties);

        $this->pk_photo  = $properties['pk_photo'];
        $this->name      = $properties['name'];
        $this->path_file = $properties['path_file'];

        if (!empty($properties['path_file'])) {
            $this->path_img = $properties['path_file'] . DS . $properties['name'];
        }

        $this->size        = $properties['size'];
        $this->width       = $properties['width'];
        $this->height      = $properties['height'];
        $this->author_name = $properties['author_name'];
        $this->description = ($this->description);
        $this->metadata    = ($this->metadata);
        $this->address     = $properties['address'];
        $this->type_img    = pathinfo($this->name, PATHINFO_EXTENSION);

        if (!empty($properties['address'])) {
            $positions = explode(',', $properties['address']);
            if (is_array($positions)
                && array_key_exists(0, $positions)
                && array_key_exists(1, $positions)
            ) {
                $this->latlong = [
                    'lat' => $positions[0],
                    'long' => $positions[1],
                ];
            }
        }
    }

    /**
     * Returns an instance of the Photo object given a photo id
     *
     * @param int $id the photo id to load
     *
     * @return Photo the photo object
     */
    public function read($id)
    {
        if ((int) $id <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN photos ON pk_content = pk_photo WHERE pk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        $this->load($rs);

        return $this;
    }

    /**
     * Creates a new photo given an array of information
     *
     * @param array $data the photo information
     *
     * @return int the photo id
     * @return boolean false if the photo was not created
     */
    public function create($data)
    {
        $data['content_status'] = 1;
        try {
            parent::create($data);

            getService('dbal_connection')->insert(
                "photos",
                [
                    'pk_photo'    => (int) $this->id,
                    'name'        => $data["name"],
                    'path_file'   => $data["path_file"],
                    'size'        => $data['size'],
                    'width'       => (int) $data['width'],
                    'height'      => (int) $data['height'],
                    'author_name' => $data['author_name']
                ]
            );

            return $this->id;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Creates one photo register in the database from data and local file
     * TODO: this function must content the photo local_file
     *
     * @param array $data the data for the photo, must content the photo local_file
     * @param string $dateForDirectory the date for the directory
     *
     * @return int the id of the photo created
     * @return boolean false if the photo was not created
     */
    public function createFromLocalFile($data, $dateForDirectory = null, $uploadPath = null)
    {
        $filePath         = $data["local_file"];
        $originalFileName = $data['original_filename'];

        if (empty($filePath)) {
            throw new \Exception(_('Image data not valid'));
        }

        // Check upload directory
        $date = new DateTime();

        if (array_key_exists('created', $data)) {
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $data['created']);
        }

        if (empty($dateForDirectory)) {
            $dateForDirectory = $date->format("/Y/m/d/");
        }

        $uploadDir = MEDIA_PATH . DS . IMG_DIR . DS . $dateForDirectory . DIRECTORY_SEPARATOR;
        if (!is_null($uploadPath)) {
            $uploadDir = $uploadPath;
        }

        if (!is_dir($uploadDir)) {
            \Onm\FilesManager::createDirectory($uploadDir);
        }

        if (is_dir($uploadDir) && !is_writable($uploadDir)) {
            throw new Exception(
                _('Unable to save your image file in the Opennemas storage target')
            );
        }

        $filePathInfo = pathinfo($originalFileName);

        // Getting information for creating
        $t                  = gettimeofday();
        $micro              = intval(substr($t['usec'], 0, 5));
        $finalPhotoFileName = $date->format("YmdHis") . $micro . "."
            . MimeTypeTool::getExtension($filePath);
        $fileInformation    = new MediaItem($filePath);

        if (!array_key_exists('urn_source', $data)
            || empty($data['urn_source'])
        ) {
            $data['urn_source'] = "urn:newsml:" . SITE . ":" . $date->format("YmdHis")
                . ":" . StringUtils::cleanFileName($originalFileName) . ":2";
        }

        $date = new \DateTime();
        $date->setTimeStamp($fileInformation->mtime);
        $dateString = $date->format('Y-m-d H:i:s');

        if (!array_key_exists('created', $data)) {
            $data['created'] = $dateString;
        }

        if (!array_key_exists('changed', $data)) {
            $data['changed'] = $dateString;
        }

        if (!array_key_exists('content_status', $data)) {
            $data['content_status'] = 1;
        }

        // Building information for the photo image
        $dataPhoto = [
            'title'               => isset($data['title']) ? $data['title'] : $originalFileName,
            'name'                => $finalPhotoFileName,
            'path_file'           => $dateForDirectory,
            'created'             => $data["created"],
            'changed'             => $data["changed"],
            'content_status'      => $data['content_status'],
            'description'         => $data['description'],
            'metadata'            => $data["metadata"],
            'urn_source'          => $data['urn_source'],
            'size'                => round($fileInformation->size / 1024, 2),
            'date'                => $dateString,
            'width'               => $fileInformation->width,
            'height'              => $fileInformation->height,
            'author_name'         => isset($data['author_name']) ? $data['author_name'] : '',
            'fk_author'           => (!array_key_exists('fk_author', $data)) ? null : $data['fk_author'],
            'fk_user_last_editor' => getService('core.user')->id,
            'fk_publisher'        => getService('core.user')->id,
        ];

        if (array_key_exists('extension', $filePathInfo) &&
            $filePathInfo['extension'] != 'swf'
        ) {
            $imageCreated = new \Imagine\Imagick\Imagine();
            $image        = $imageCreated->open($data['local_file']);

            // Doesn't work as expected. Commented for now
            // $filter = new \Onm\Imagine\Filter\CorrectExifRotation();
            // $image = $filter->apply($image);

            try {
                if ($filePathInfo['extension'] == 'gif') {
                    $image->save(
                        realpath($uploadDir) . DIRECTORY_SEPARATOR . $finalPhotoFileName,
                        [ 'flatten' => false ]
                    );
                } else {
                    $image->save(
                        realpath($uploadDir) . DIRECTORY_SEPARATOR . $finalPhotoFileName,
                        [
                            'resolution-units' => \Imagine\Image\ImageInterface::RESOLUTION_PIXELSPERINCH,
                            'resolution-x'     => 72,
                            'resolution-y'     => 72,
                            'quality'          => 85,
                        ]
                    );
                }
            } catch (\RuntimeException $e) {
                $logger = getService('application.log');
                $logger->notice(
                    sprintf(
                        'Unable to create the photo file %s (destination: %s).',
                        $data['local_file'],
                        $uploadDir . $finalPhotoFileName
                    )
                );

                throw new Exception(_('Unable to copy your image file'));
            }
        } else {
            // Check source and target
            $fileCopied = false;
            $targetPath = realpath($uploadDir) . DS . $finalPhotoFileName;
            if (is_file($data['local_file']) && is_writable($targetPath)) {
                $fileCopied = copy($data['local_file'], $targetPath);
            }

            if (!$fileCopied) {
                $logger = getService('application.log');
                $logger->notice(
                    sprintf(
                        'Unable to create the photo file %s (destination: %s).',
                        $data['local_file'],
                        $uploadDir . $finalPhotoFileName
                    )
                );
                throw new Exception(_('Unable to copy your image file'));
            }
        }

        $photoID = $this->create($dataPhoto);

        if (!$photoID) {
            $logger = getService('application.log');
            $logger->notice(
                sprintf(
                    'Unable to save the image object %s (destination: %s).',
                    $data['local_file'],
                    $uploadDir . $finalPhotoFileName
                )
            );
            throw new Exception(_('Unable to save your image information.'));
        }

        return $photoID;
    }

    /**
     * Updates the photo object given an array with information
     *
     * @param array $data the new photo information
     *
     * @return boolean true if the photo was updated properly
     */
    public function update($data)
    {
        try {
            parent::update($data);

            getService('dbal_connection')->update(
                'photos',
                [
                    'name'        => $this->name,
                    'path_file'   => $this->path_file,
                    'size'        => $this->size,
                    'width'       => (int) $this->width,
                    'height'      => (int) $this->height,
                    'author_name' => $data['author_name'],
                    'address'     => $data['address'],
                ],
                [ 'pk_photo' => (int) $data['id'] ]
            );

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Removes a photo given its id
     *
     * @param int $id the photo id to delete
     *
     * @return boolean true if the photo was deleted
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        $image = MEDIA_IMG_PATH . $this->path_file . $this->name;

        if (file_exists($image) && !@unlink($image)) {
            return false;
        }

        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                "photos",
                [ 'pk_photo' => $id ]
            );

            if (!$rs) {
                return false;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Completes the EXIF, IPTC information for the current Photo object
     *
     * @return Photo the Photo object with all the information
     */
    public function getPhotoMetaData()
    {
        $image = MEDIA_IMG_PATH . $this->path_file . $this->name;

        if (is_file($image)) {
            $size = getimagesize($image, $info);

            switch ($size['mime']) {
                case "image/gif":
                    $this->infor = sprintf(_("Image type: %s"), 'GIF');

                    break;
                case "image/png":
                    $this->infor = sprintf(_("Image type: %s"), 'PNG');

                    break;
                case "image/bmp":
                    $this->infor = sprintf(_("Image type: %s"), 'BMP');

                    break;
                case 'image/jpeg':
                    $exif = [];
                    if (isset($info)) {
                        foreach (array_keys($info) as $key) {
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
                        $this->infor .= _("No available EXIF data");
                    } else {
                        if (empty($this->color)) {
                            if ($exifData['COMPUTED']['IsColor'] == 0) {
                                $this->color = 'BN';
                            } else {
                                $this->color = 'color';
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
                                $this->date = $exifData['FILE']['FileDateTime'];
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

                                for ($i = 1; $i < $keywordcount; $i++) {
                                    $keywords .= ", " . $iptc["2#025"][$i];
                                }
                            } else {
                                $keywords = '';
                            }

                            $myiptc['Keywords']     = $keywords;
                            $myiptc['Caption']      = $iptc["2#120"][0];
                            $myiptc['Graphic_name'] = $iptc["2#005"][0];
                            $myiptc['Urgency']      = $iptc["2#010"][0];
                            $myiptc['Category']     = $iptc["2#015"][0];
                            $myiptc['Program']      = $iptc["2#065"][0];

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

                            $myiptc = array_map('\Onm\StringUtils::convertToUTF8AndStrToLower', $myiptc);

                            $this->myiptc = $myiptc;

                            if (empty($this->description)) {
                                $this->description = $myiptc['Caption'];
                            }

                            if (empty($this->metadata)) {
                                $this->metadata = \Onm\StringUtils::convertToUTF8AndStrToLower($keywords);
                            }

                            if (empty($this->author_name)) {
                                $this->author_name = $myiptc['Photographer'];
                            }

                            ini_set($errorReporting);
                        } else {
                            $this->infor .= _("No available IPTC data");
                        }
                    }
                    break;
                default:
                    break;
            } // endswitch;
        } else {
            $this->infor .= _("Invalid image file");
        }

        return $this;
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
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT `path_file`, `name` FROM photos WHERE pk_photo = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            return (string) $rs['path_file'] . $rs['name'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
