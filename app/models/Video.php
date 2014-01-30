<?php
/**
 * Defintes the Video class
 *
 * @package    Model
 **/
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Message as m;

/**
 * Handles video CRUD actions.
 *
 * @package    Model
 **/
class Video extends Content
{
    /**
     * The video id
     *
     * @var  int
     **/
    public $pk_video = null;

    /**
     * Serialized array with the video information
     *
     * @var string
     **/
    public $information  = null;

    /**
     * The original video url, if it comes from an external source
     *
     * @var string
     **/
    public $video_url  = null;

    /**
     * The video author name
     *
     * @var string
     **/
    public $author_name = null;

    /**
     * Initializes the Video object
     *
     * @param int $id the video id to load
     *
     * @return Video the video object instance
     **/
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
     **/
    public function __get($name)
    {
        switch ($name) {
            case 'uri':
                return $this->getUri();
                break;
            case 'slug':
                return StringUtils::get_title($this->title);

                break;
            case 'content_type_name':
                return 'Video';

                break;
            case 'thumb':
                return $this->getThumb();

                break;
            default:
                return parent::__get($name);
                break;
        }

        return parent::__get($name);
    }

    /**
     * Creates a new video from a given data array
     *
     * @param array $data the video data
     *
     * @return boolean true if the videos was created
     **/
    public function create($data)
    {
        parent::create($data);

        $sql = "INSERT INTO videos
                    (`pk_video`,`video_url`, `information`, `author_name`) " .
                "VALUES (?,?,?,?)";

        $values = array(
            $this->id, $data['video_url'],
            serialize($data['information']), $data['author_name']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return $this->id;
    }

    /**
     * Loads a video identified by a the given id
     *
     * @param int $id the video id to load
     *
     * @return Video the video object instance
     **/
    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM videos WHERE pk_video = '.$id;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            return;
        }

        $this->pk_video = $rs->fields['pk_video'];
        $this->video_url = $rs->fields['video_url'];
        $this->author_name = $rs->fields['author_name'];
        $this->information = unserialize($rs->fields['information']);
    }

    /**
     * Updates the video given an array of data
     *
     * @param array $data the new video data
     *
     * @return boolean true if the video was updated
     **/
    public function update($data)
    {
        parent::update($data);

        $sql =  "UPDATE videos"
                ." SET  `video_url`=?, `information`=?, `author_name`=?  "
                ." WHERE pk_video=".$data['id'];
        $values = array(
            $data['video_url'],
            serialize($data['information']),
            $data['author_name']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        return true;
    }

    /**
     * Removes permanently a video given an id
     *
     * @param int $id the video id
     *
     * @return boolean true if the video was removed
     **/
    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM videos WHERE pk_video='.$id;

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            return false;
        }

        return true;
    }


    /**
     * Removes videos given its id
     *
     * @param array $arrayId the video ids to delete
     *
     * @return boolean true if the videos was deleted
     **/
    public static function batchDelete($arrayIds)
    {

        $contents = implode(', ', $arrayIds);

        $sql = "SELECT  video_url, information  FROM videos WHERE author_name='internal' "
            ." AND pk_video IN (".$contents.")";

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        while (!$rs->EOF) {
            $image       = MEDIA_PATH.DS . $rs->fields['video_url'];
            $thumbs      = unserialize($rs->fields['information']);
            $thumbSmall  = MEDIA_PATH.DS .$thumbs['thumbnails']['small'];
            $thumbNormal = MEDIA_PATH.DS .$thumbs['thumbnails']['normal'];
            $thumbBig    = MEDIA_PATH.DS .$thumbs['thumbnails']['big'];

            if (file_exists($image)) {
                @unlink($image);
            }
            if (file_exists($thumbSmall)) {
                @unlink($thumbSmall);
            }
            if (file_exists($thumbNormal)) {
                @unlink($thumbNormal);
            }
            if (file_exists($thumbBig)) {
                @unlink($thumbBig);
            }

            $rs->MoveNext();
        }

        $sql = 'DELETE FROM videos '
                .'WHERE `pk_video` IN ('.$contents.')';

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs === false) {
            return false;
        }

        return true;

    }

    /**
     * Creates a video from a local file
     *
     * @param array $videoFileData the new video file data
     *
     * @return true
     * @throws Exception, if something goes wrong
     **/
    public function createFromLocalFile($videoFileData = array())
    {
        $pathUpload = MEDIA_PATH.DS;
        $processedFile = $this->upload($videoFileData, $pathUpload);

        // If video file was converted/copied successfully insert
        // the video into database
        if (!empty($processedFile)) {

            $videoInformation = array_merge(
                $videoFileData,
                array(
                    "video_url" => $processedFile['flvFile'],
                    "information" =>
                        array('thumbnails' => $processedFile['thumbnails']),
                    "author_name" => 'internal',
                )
            );
            $videoId = $this->create($videoInformation);
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
     **/
    public function upload($file, $baseUploadpath)
    {
        $videoInformation = array();

        if (empty($file["file_path"])) {
            throw new Exception(
                sprintf(
                    _(
                        'Seems that the server limits file uploads up to %s Mb. '
                        .'Try to upload files smaller than that size or '
                        .'contact with your administrator'
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
                    $relativeUploadDir.DIRECTORY_SEPARATOR.$value;
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
     **/
    public function convertVideotoFLV($originalVideo, $baseUploadpath)
    {
        $return = array();

        $ffmpgePath = exec("which ffmpeg");

        // $originalVideoPath  = $originalVideo['file_path'];
        $fileType           = $originalVideo['file_type'];
        $temporaryVideoPath = $originalVideo['file_path'];

        // Calculate upload directory and create it if not exists
        $relativeUploadDir  = 'video'.DS.date("Y/m/d");
        $absoluteUploadpath = $baseUploadpath.DS.$relativeUploadDir.DS;
        if (!is_dir($absoluteUploadpath)) {
            FilesManager::createDirectory($absoluteUploadpath);
        }

        // Calculate the final video name by its extension, current data, ...
        $t        = gettimeofday();
        $micro    = intval(substr($t['usec'], 0, 5));
        $fileName = date("YmdHis") . $micro . "." . 'flv';

        // Compose absolute path to the new video file
        $videoSavePath = realpath($absoluteUploadpath).DS.$fileName;

        switch ($fileType) {
            case 'video/x-ms-wmv':
            case 'video/avi':
            case 'video/msvideo':
            case 'video/x-msvideo':
                // Dropped option -s 320x240
                $shellCommand = escapeshellcmd(
                    $ffmpgePath." -i "
                    .$temporaryVideoPath." -f flv  ".$videoSavePath
                ). " 2>&1";
                exec($shellCommand, $outputExec, $returnExec);
                unset($outputExec);
                if ($returnExec !== 0) {
                    throw new \Exception(
                        _(
                            'There was a problem while converting your video. '
                            .'Please contact with your administrator.'
                        )
                    );
                };

                break;
            case 'video/x-flv':
                copy($temporaryVideoPath, $videoSavePath);

                break;
            default:
                $message = sprintf(_('Video format "%s" not supported'), $fileType);
                throw new \Exception($message);

                break;
        }

        $return['relative_dir']  = $relativeUploadDir;
        $return['relative_path'] = $relativeUploadDir.DS.$fileName;
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
    public static function createThumbnailsfromFLV($flvPath, $sizes = array())
    {
        $defaultThumbnailSizes = array(
            'small'  =>  array( 'width' => 150, 'height' => 150 ),
            'normal' =>  array( 'width' => 300, 'height' => 300 ),
            'big'    =>  array( 'width' => 450, 'height' => 450 ),
        );

        // Get the thumbnail sizes
        $sizes = array_merge($defaultThumbnailSizes, $sizes);

        $ffmpeg = \FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries'  => '/usr/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/bin/ffprobe',
            'timeout'          => 3600, // The timeout for the underlying process
            'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
        ));
        $video = $ffmpeg->open($flvPath);

        foreach ($sizes as $name => $sizeValues) {
            // Getting file information from flv file
            // for building  save path and final filename for the thumbnail
            $flvFileInfo = pathinfo($flvPath);

            $basePath = $flvFileInfo['dirname'];
            $baseName = $flvFileInfo['filename']."-".$name.'.jpg';
            $imageFile = $basePath.DIRECTORY_SEPARATOR.$baseName;

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
     **/
    public function getUri()
    {
        if (empty($this->category_name)) {
            $this->category_name = $this->loadCategoryName($this->pk_content);
        }
        $uri =  Uri::generate(
            'video',
            array(
                'id'       => sprintf('%06d', $this->id),
                'date'     => date('YmdHis', strtotime($this->created)),
                'category' => $this->category_name,
                'slug'     => $this->slug,
            )
        );

        return ($uri !== '') ? $uri : $this->permalink;
    }

    /**
     * Returns the thumb url of this video
     *
     * @return string the thumb url
     **/
    public function getThumb()
    {
        if (!is_array($this->information)) {
            $information = unserialize($this->information);
        } else {
            $information = $this->information;
        }
        if ($this->author_name == 'internal') {
            $thumbnail =
                MEDIA_IMG_PATH_WEB."/../".$information['thumbnails']['normal'];
        } elseif (!empty($information)
            && is_array($information)
            && array_key_exists('thumbnail', $information)) {
            $thumbnail = $information['thumbnail'];
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
     **/
    public function render($params)
    {
        //  if (!isset($tpl)) {
            $tpl = new Template(TEMPLATE_USER);
        //}

        $tpl->assign('item', $this);
        $tpl->assign('cssclass', $params['cssclass']);

        try {
            $html = $tpl->fetch('frontpage/contents/_video.tpl');
        } catch (\Exception $e) {
            $html = 'Video not available';
        }

        return $html;
    }
}
