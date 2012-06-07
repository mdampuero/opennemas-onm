<?php
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
 * @package    Onm
 * @subpackage Model
 **/
class Video extends Content
{

    public $pk_video = null;
    public $information  = null;
    public $video_url  = null;
    public $author_name = null;
    public $content_type = null;
    public $content_type_name = null;

    /**
     * Initializes the Video object
     **/
    public function __construct($id = null)
    {
        parent::__construct($id);
        if (!is_null($id)) {
            $this->read($id);
        }
        $this->content_type = 'Video';
    }

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
                $sql = 'SELECT * FROM `content_types`"
                            ." WHERE pk_content_type = "?" LIMIT 1';
                $values = array($this->content_type);
                $contentTypeName = $GLOBALS['application']->conn->Execute(
                    $sql,
                    $values
                );
                if (isset($contentTypeName->fields['name'])) {
                    $returnValue = $contentTypeName;
                } else {
                    $returnValue = $this->content_type;
                }
                $this->content_type_name = $returnValue;

                return $returnValue;

                break;
            case 'thumb':

                return $this->getThumb();

            default:
                break;
        }

        return parent::__get($name);
    }

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
            \Application::logDatabaseError();

            return false;
        }

        return $this->id;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM videos WHERE pk_video = '.$id;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->pk_video = $rs->fields['pk_video'];
        $this->video_url = $rs->fields['video_url'];
        $this->author_name = $rs->fields['author_name'];
        $this->information = unserialize($rs->fields['information']);
    }

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
            \Application::logDatabaseError();

            return;
        }
    }

    public function remove($id)
    {

        //if ($this->author_name == 'internal') {
        //    var_dump($this->);
        //    die();
        //}
        //unlink($this->information['thumbnails'])

        parent::remove($id);

        $sql = 'DELETE FROM videos WHERE pk_video='.$id;

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            \Application::logDatabaseError();

            return;
        }
    }

    /**
     * Creates a video from a local file
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

    public function upload($file, $baseUploadpath)
    {
        $videoInformation = array();

        if (empty($file["file_path"])) {
            throw new Exception(sprintf(
                _('Seems that the server limits file uploads up to %s Mb. '
                  .'Try to upload files smaller than that size or '
                  .'contact with your administrator'),
                (int) (ini_get('upload_max_filesize'))
            ));
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
     * @return string the converted file path
     **/
    public function convertVideotoFLV($originalVideo, $baseUploadpath)
    {
        $return = array();

        $ffmpgePath = exec("which ffmpeg");

        $originalVideoPath  = $originalVideo['file_path'];
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
                $shellCommand = escapeshellcmd($ffmpgePath." -i "
                    .$temporaryVideoPath." -f flv  ".$videoSavePath). " 2>&1";
                exec($shellCommand, $outputExec, $returnExec);
                unset($outputExec);
                if ($returnExec !== 0) {
                    throw new \Exception(
                        _('There was a problem while converting your video. '
                            .'Please contact with your adminstrator.')
                    );
                };
                break;

            case 'video/x-flv':
                copy($temporaryVideoPath, $videoSavePath);
                break;

            default:
                $message =
                    sprintf(_('Video format "%s" not supported'), $fileType);
                throw new \Exception($message);
                break;
        }

        $return['relative_dir']  = $relativeUploadDir;
        $return['relative_path'] = $relativeUploadDir.DS.$fileName;
        $return['absolute_path'] = $videoSavePath;

        return $return;
    }

    /*
     * Function that creates thumbnails for one flv vídeo
     * @param $flvPath, $sizes
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

        // init ffmpeg object from flv for getting its thumbnail
        $movie = new ffmpeg_movie($flvPath);
        // Get the number of frames of the video
        $totalFrames = $movie->getFrameCount();
        //$height = $movie->getFrameHeight();
        //$width = $movie->getFrameWidth();

        foreach ($sizes as $name => $sizeValues) {

            $thumbnailFrameNumber = (int) round($totalFrames*2/5);

            // Need to create a GD image ffmpeg-php to work on it
            // Choose the frame you want to save as jpeg
            $image = imagecreatetruecolor(
                $sizeValues['width'],
                $sizeValues['height']
            );
            // Receives the frame

            $frame = $movie->getFrame($thumbnailFrameNumber);
            if (gettype($frame) != 'object') {
                $thumbnailFrameNumber = 1;
                do {
                    if ($thumbnailFrameNumber > $totalFrames) {
                        break 1;
                    }
                    $frame = $movie->getFrame($thumbnailFrameNumber);
                    // $valid = gettype($frame);
                    $thumbnailFrameNumber++;
                } while (gettype($frame) != 'object');
            }

            // Convert to a GD image
            $image = $frame->toGDImage();

            // Getting file information from flv file
            // for building  save path and final filename for the thumbnail
            $flvFileInfo = pathinfo($flvPath);

            $basePath = $flvFileInfo['dirname'];
            $baseName = $flvFileInfo['filename']."-".$name.'.jpg';

            // Save to disk.
            $imageFile = $basePath.DIRECTORY_SEPARATOR.$baseName;
            imagejpeg($image, $imageFile, 90);
            $thumbs[$name] = $baseName;
        }

        return $thumbs;
    }

    /**
     * Return the uri for this video
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
        } else {
            $thumbnail = $information['thumbnail'];
        }

        return $thumbnail;
    }

    /**
     * Renders the video object in frontpage
     *
     * @return string the final HTML for this video
     **/
    public function render($params)
    {
        unset($params);

        return "\n<!-- video rendering not implemented -->\n";
    }

}
