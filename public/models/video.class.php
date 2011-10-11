<?php
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
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
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
    function __construct($id = null)
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
                if (empty($this->category_name)) {
                    $this->category_name = $this->loadCategoryName($this->pk_content);
                }
                $uri =  Uri::generate(
                    'video',
                    array(
                        'id' => sprintf('%06d',$this->id),
                        'date' => date('YmdHis', strtotime($this->created)),
                        'category' => $this->category_name,
                        'slug' => $this->slug,
                    )
                );

                return ($uri !== '') ? $uri : $this->permalink;

                break;
            case 'slug':
                return String_Utils::get_title($this->title);
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
                
                if (!is_array($this->information)) {
                    $information = unserialize($this->information);
                } else {
                    $information = $this->information;
                }
                if ($this->author_name == 'internal') {
                    $thumbnail = MEDIA_URL.DIRECTORY_SEPARATOR.$information['small'];
                } else {
                    $thumbnail = $information['thumbnail'];
                }
                
                return $thumbnail;

            default:
                break;
        }
        parent::__get($name);
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
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return false;
        }

        return true;
    }

    public function read($id)
    {
        parent::read($id);

        $sql = 'SELECT * FROM videos WHERE pk_video = '.$id;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
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
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }

    public function remove($id)
    {
        parent::remove($id);

        $sql = 'DELETE FROM videos WHERE pk_video='.$id;

        if ($GLOBALS['application']->conn->Execute($sql)===false) {
            $errorMsg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$errorMsg);
            $GLOBALS['application']->errors[] = 'Error: '.$errorMsg;
            return;
        }
    }
    
    static public function uploadFLV($file, $baseUploadpath) {
    
        $return = array();
        
        if (empty($file["tmp_name"])) {
            m::add(sprintf(
                _('Seems that the server limits file uploads up to %s Mb.'
                    .' Try to upload files smaller than that size or contact with your administrator'),
                (int)(ini_get('upload_max_filesize'))
            ));
            return $return;
        }
        $uploads = array();
    
        $originalFileName = $file["name"];
        if(!empty($originalFileName)) {
            
            // Calculate upload directory and create it if not exists
            $relativeUploadDir = 'video'.DS.date("Y/m/d");
            $absoluteUploadpath = $baseUploadpath.DS.$relativeUploadDir.DS;
            if(!is_dir($absoluteUploadpath)) FilesManager::createDirectory($absoluteUploadpath);
            
            // Calculate the final video name by its extension, current data, ...
            $fileData = pathinfo($originalFileName);     //sacamos infor del archivo
            $fileExtension = strtolower($fileData['extension']);
            $t = gettimeofday(); //Sacamos los microsegundos
            $micro = intval(substr($t['usec'], 0, 5)); //Le damos formato de 5digitos a los microsegundos
            $fileName = date("YmdHis") . $micro . "." . $fileExtension;
            
            // Compose absolute path to the new video file
            $videoSavePath = $absoluteUploadpath.$fileName;
    
            // Finally move uploaded file to the new location
            if (move_uploaded_file($file["tmp_name"], $videoSavePath)) {
                $return['flvFile'] = $relativeUploadDir.DIRECTORY_SEPARATOR.$fileName;
            }
            
            $thumbnails = self::createThumbnailsfromFLV($videoSavePath);
            // We need to add relative path for every thumbnail
            foreach ($thumbnails as $name => $value ) {
                $return['thumbnails'][$name] = $relativeUploadDir.DIRECTORY_SEPARATOR.$value;
            }
            
            
        } //if empty
    
        return $return;
    }
    
    /*
     * Function that creates thumbnails for one flv vídeo
     * @param $flvPath, $sizes
     */
    
    static public function createThumbnailsfromFLV($flvPath, $sizes = array())
    {
        $defaultThumbnailSizes = array(
            'small'  =>  array( 'width' => 150, 'height' => 150 ),
            'normal' =>  array( 'width' => 300, 'height' => 300 ),
            'big'    =>  array( 'width' => 450, 'height' => 450 ),
        );
        
        
        // Create thumbs in the same directory as the video
        $uploadDir = dirname($flvPath);
        
        // Get the thumbnail sizes
        $sizes = array_merge($defaultThumbnailSizes, $sizes);
        
        // init ffmpeg object from flv for getting its thumbnail
        $movie = new ffmpeg_movie($flvPath);
        // Get the number of frames the video has
        $frameCount = $movie->getFrameCount();
        // Get The duration of the video in seconds
        $duration = round($movie->getDuration(), 0);  
        // Get the number of frames of the video  
        $totalFrames = $movie->getFrameCount();  
        
        //$height = $movie->getFrameHeight();  
        //$width = $movie->getFrameWidth();
        
        foreach ($sizes as $name => $sizeValues) {
            
            $thumbnailFrame = (int) round($totalFrames/2);
        
            // Need to create a GD image ffmpeg-php to work on it  
            // Choose the frame you want to save as jpeg
            $image = imagecreatetruecolor($sizeValues['width'], $sizeValues['height']);  
            // Receives the frame  
            $frame = $movie->getFrame($thumbnailFrame);  
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

}
