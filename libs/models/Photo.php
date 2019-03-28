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
            'created'             => $data['created'],
            'changed'             => $data['changed'],
            'content_status'      => $data['content_status'],
            'description'         => $data['description'],
            'tag_ids'             => empty($data['tag_ids']) ?
                [] :
                $data['tag_ids'],
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
            $target = realpath($uploadDir) . DS . $finalPhotoFileName;

            try {
                getService('core.image.image')
                    ->open($data['local_file'])
                    ->optimize()
                    ->save($target);
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
     * Returns the photo relative path.
     *
     * @return string The photo relative path.
     */
    public function getRelativePath()
    {
        return $this->path_file . $this->name;
    }
}
