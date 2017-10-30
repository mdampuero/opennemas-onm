<?php
/**
 * Defintes the Video class
 *
 * @package    Model
 */
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Handles video CRUD actions.
 *
 * @package    Model
 */
class Video extends Content
{
    /**
     * The video id
     *
     * @var  int
     */
    public $pk_video = null;

    /**
     * Serialized array with the video information
     *
     * @var string
     */
    public $information = null;

    /**
     * The original video url, if it comes from an external source
     *
     * @var string
     */
    public $video_url = null;

    /**
     * The video author name
     *
     * @var string
     */
    public $author_name = null;

    /**
     * Initializes the Video object
     *
     * @param int $id the video id to load
     *
     * @return Video the video object instance
     */
    public function __construct($id = null)
    {
        $this->content_type_l10n_name = _('Video');

        parent::__construct($id);
    }

    /**
     * Magic function to get uninitilized object properties.
     *
     * @param string $name the name of the property to get.
     *
     * @return mixed the value for the property
     */
    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                return $this->getUri();
            case 'slug':
                return \Onm\StringUtils::getTitle($this->title);
            case 'content_type_name':
                return 'Video';
            default:
                return parent::__get($name);
        }

        return parent::__get($name);
    }

    /**
     * Loads a video identified by a the given id
     *
     * @param int $id the video id to load
     *
     * @return Video the video object instance
     */
    public function read($id)
    {
        // If no valid id then return
        if (((int) $id) <= 0) {
            return;
        }

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM contents LEFT JOIN contents_categories ON pk_content = pk_fk_content '
                . 'LEFT JOIN videos ON pk_content = pk_video WHERE pk_content = ?',
                [ $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->load($rs);
            $this->information = unserialize($rs['information']);
            $this->thumb       = $this->getThumb();

            return $this;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties
     */
    public function load($properties)
    {
        parent::load($properties);

        if (array_key_exists('pk_video', $properties)) {
            $this->category_title = $this->loadCategoryTitle($properties['pk_video']);
        }

        $this->thumb = $this->getThumb();
    }

    /**
     * Creates a new video from a given data array
     *
     * @param array $data the video data
     *
     * @return boolean true if the videos was created
     */
    public function create($data)
    {
        try {
            parent::create($data);

            $this->pk_video   = $this->id;
            $this->pk_content = $this->id;

            $rs = getService('dbal_connection')->insert(
                "videos",
                [
                  'pk_video'    => $this->id,
                  'video_url'   => $data['video_url'],
                  'information' => array_key_exists('information', $data) ?
                      serialize($data['information']) : null,
                  'author_name' => $data['author_name'],
                ]
            );

            return $this->id;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Updates the video given an array of data
     *
     * @param array $data the new video data
     *
     * @return boolean true if the video was updated
     */
    public function update($data)
    {
        try {
            parent::update($data);

            $rs = getService('dbal_connection')->update(
                "videos",
                [
                    'video_url' => $data['video_url'],
                    'information' => serialize($data['information']),
                    'author_name' => $data['author_name'],
                ],
                [ 'pk_video' => (int) $data['id'] ]
            );

            $this->load($data);

            return $this;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Removes permanently a video given an id
     *
     * @param int $id the video id
     *
     * @return boolean true if the video was removed
     */
    public function remove($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        parent::remove($id);

        try {
            $rs = getService('dbal_connection')->delete(
                "videos",
                [ 'pk_video' => $id ]
            );

            if (!$rs) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }


    /**
     * Removes videos given its id
     *
     * @param array $arrayId the video ids to delete
     *
     * @return boolean true if the videos was deleted
     */
    public static function batchDelete($arrayIds)
    {
        if (!is_array($arrayIds)) {
            return false;
        }

        $contents = implode(', ', $arrayIds);
        try {
            $conn = getService('dbal_connection');
            $rs   = $conn->fetchAll(
                "SELECT video_url, information FROM videos WHERE author_name='internal' " .
                " AND pk_video IN (" . $contents . ")"
            );

            if ($rs) {
                return false;
            }

            foreach ($rs as $element) {
                $image  = MEDIA_PATH . DS . $element['video_url'];
                $thumbs = unserialize($element['information']);
                if (!array_key_exists('thumbnails', $thumbs)) {
                    continue;
                }

                $sizes = ['small', 'normal', 'big' ];
                foreach ($sizes as $size) {
                    if (array_key_exists($size, $thumbs['thumbnails'])) {
                        $fileName = MEDIA_PATH . DS . $thumbs['thumbnails'][$size];
                        if (file_exists($fileName)) {
                            @unlink($image);
                        }
                    }
                }
            }

            $result = $conn->executeUpdate('DELETE FROM videos WHERE `pk_video` IN (' . $contents . ')');
            if (!$result) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Creates a video from a local file
     *
     * @param array $videoFileData the new video file data
     *
     * @return true
     * @throws Exception, if something goes wrong
     */
    public function createFromLocalFile($videoFileData = [])
    {
        $pathUpload    = MEDIA_PATH . DS;
        $processedFile = $this->upload($videoFileData, $pathUpload);

        // If video file was converted/copied successfully insert
        // the video into database
        if (!empty($processedFile)) {
            $videoInformation = array_merge(
                $videoFileData,
                [
                    "video_url"   => $processedFile['flvFile'],
                    "information" => ['thumbnails' => $processedFile['thumbnails']],
                    "author_name" => 'internal',
                ]
            );
            $videoId          = $this->create($videoInformation);
            if (!$videoId) {
                throw new \Exception($this->errors);
            }
        } else {
            $message = _('There was an error while processing your video file');
            throw new \Exception($message);
        }

        return $videoId;
    }

    /**
     * Stores and generates one FLV video given the original file name and the
     * target path
     *
     * @param string $file the file path to the original FLV file
     * @param string $baseUploadpath the path where to save the video
     *
     * @return string the video uri
     */
    public function upload($file, $baseUploadpath)
    {
        $videoInformation = [];

        if (empty($file["file_path"])) {
            throw new Exception(
                sprintf(
                    _(
                        'The server limits file uploads up to %s Mb. ' .
                        'Try to upload files smaller than that size.'
                    ),
                    (int) ini_get('upload_max_filesize')
                )
            );
        }

        $convertedVideo = $this->convertVideotoFLV($file, $baseUploadpath);

        if (array_key_exists('relative_path', $convertedVideo)) {
            $videoInformation['flvFile'] = $convertedVideo['relative_path'];

            $videoAbsolutePath = $convertedVideo['absolute_path'];

            $thumbnails = self::createThumbnailsfromFLV($videoAbsolutePath);

            // We need to add relative path for every thumbnail
            $relativeUploadDir = $convertedVideo['relative_dir'];
            foreach ($thumbnails as $name => $value) {
                $videoInformation['thumbnails'][$name] =
                    $relativeUploadDir . DIRECTORY_SEPARATOR . $value;
            }
        }

        return $videoInformation;
    }

    /**
     * Converts a video to flv given a local path
     *
     * @param array $originalVideo the video file information
     * @param string $baseUploadpath the target directory where upload the video file
     *
     * @return string the converted file path
     */
    public function convertVideotoFLV($originalVideo, $baseUploadpath)
    {
        $return = [];

        $ffmpgePath = exec("which ffmpeg");

        // $originalVideoPath  = $originalVideo['file_path'];
        $fileType           = $originalVideo['file_type'];
        $temporaryVideoPath = $originalVideo['file_path'];

        // Calculate upload directory and create it if not exists
        $relativeUploadDir  = 'video' . DS . date("Y/m/d");
        $absoluteUploadpath = $baseUploadpath . DS . $relativeUploadDir . DS;
        if (!is_dir($absoluteUploadpath)) {
            \Onm\FilesManager::createDirectory($absoluteUploadpath);
        }

        // Calculate the final video name by its extension, current data, ...
        $t        = gettimeofday();
        $micro    = intval(substr($t['usec'], 0, 5));
        $fileName = date("YmdHis") . $micro . "." . 'flv';

        // Compose absolute path to the new video file
        $videoSavePath = realpath($absoluteUploadpath) . DS . $fileName;

        switch ($fileType) {
            case 'video/x-ms-wmv':
            case 'video/avi':
            case 'video/msvideo':
            case 'video/x-msvideo':
                // Dropped option -s 320x240
                $shellCommand = escapeshellcmd(
                    $ffmpgePath . " -i " .
                    $temporaryVideoPath . " -f flv  " . $videoSavePath
                ) . " 2>&1";
                exec($shellCommand, $outputExec, $returnExec);
                unset($outputExec);
                if ($returnExec !== 0) {
                    throw new \Exception(_('There was a problem while converting your video. '));
                };
                break;
            case 'video/x-flv':
                copy($temporaryVideoPath, $videoSavePath);
                break;
            default:
                $message = sprintf(_('Video format "%s" not supported'), $fileType);
                throw new \Exception($message);
        }

        $return['relative_dir']  = $relativeUploadDir;
        $return['relative_path'] = $relativeUploadDir . DS . $fileName;
        $return['absolute_path'] = $videoSavePath;

        return $return;
    }

    /**
     * Creates thumbnails from one flv vídeo
     *
     * @param string $flvPath the path to the FLV file
     * @param array $sizes list of thumbnail sizes to generate
     *
     * @return array the list of paths to the generated thumbnails
     */
    public static function createThumbnailsfromFLV($flvPath, $sizes = [])
    {
        $defaultThumbnailSizes = [
            'small'  => ['width' => 150, 'height' => 150],
            'normal' => ['width' => 300, 'height' => 300],
            'big'    => ['width' => 450, 'height' => 450],
        ];

        // Get the thumbnail sizes
        $sizes = array_merge($defaultThumbnailSizes, $sizes);

        $ffmpeg = \FFMpeg\FFMpeg::create([
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ]);
        $video  = $ffmpeg->open($flvPath);

        foreach (array_keys($sizes) as $name) {
            // Getting file information from flv file
            // for building  save path and final filename for the thumbnail
            $flvFileInfo = pathinfo($flvPath);

            $basePath  = $flvFileInfo['dirname'];
            $baseName  = $flvFileInfo['filename'] . "-" . $name . '.jpg';
            $imageFile = $basePath . DIRECTORY_SEPARATOR . $baseName;

            $frame = $video->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(10));
            $frame->save($imageFile);
            $thumbs[$name] = $baseName;
        }

        return $thumbs;
    }

    /**
     * Returns the uri for this video
     *
     * @return string the video uri
     */
    public function getUri()
    {
        if (empty($this->category_name)) {
            $this->category_name = $this->loadCategoryName($this->pk_content);
        }

        $uri = Uri::generate(
            'video',
            [
                'id'       => sprintf('%06d', $this->id),
                'date'     => date('YmdHis', strtotime($this->created)),
                'category' => urlencode($this->category_name),
                'slug'     => urlencode($this->slug),
            ]
        );

        return ($uri !== '') ? $uri : $this->permalink;
    }

    /**
     * Returns the thumb url of this video
     *
     * @return string the thumb url
     */
    public function getThumb()
    {
        if (!is_array($this->information)) {
            $information = unserialize($this->information);
        } else {
            $information = $this->information;
        }

        if ($this->author_name == 'internal') {
            $thumbnail =
                MEDIA_IMG_PATH_WEB . "/../" . $information['thumbnails']['normal'];
        } elseif (!empty($information)
            && is_array($information)
            && array_key_exists('thumbnail', $information)
        ) {
            if ($this->author_name == 'external' || $this->author_name == 'script') {
                $this->thumb_image = new \Photo($information['thumbnail']);
                if (!empty($this->thumb_image->name)) {
                    $thumbnail = MEDIA_IMG_PATH_WEB . $this->thumb_image->path_file . $this->thumb_image->name;
                } else {
                    $thumbnail = '/assets/images/transparent.png';
                }
            } else {
                $thumbnail = $information['thumbnail'];
            }
        } else {
            $thumbnail = '';
        }

        return $thumbnail;
    }

    /**
     * Renders the video object in frontpage
     *
     * @param array $params the parameters for changing the rendering behaviour
     *
     * @return string the final HTML for this video
     */
    public function render($params)
    {
        $tpl = getService('core.template');

        $params['item'] = $this;
        $template       = 'frontpage/contents/_video.tpl';

        if ($params['custom'] == 1) {
            $template = $params['tpl'];
        }

        try {
            $html = $tpl->fetch($template, $params);
        } catch (\Exception $e) {
            $logger = getService('error.log');
            $logger->error($e->getMessage() . ' ' . $e->getTraceAsString());
            $html = _('Video not available');
        }

        return $html;
    }
}
