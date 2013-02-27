<?php
/**
 * Defines the MediaItem class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Core
 */

/**
 * Class MediaItem, represents a media file.
 *
 * This class manipulate a swf, jpg, png, ... files. It's possible extract
 * all information to print this resource. By example: generate tags <embed ...
 * to the swf file.
 *
 * @package Core
 */
class MediaItem
{

    /* Absolute path and file name */
    /**
     * The file name of the item to parse
     *
     * @var string
     **/
    public $filename = null;

    /**
     * The base path of the file to parse
     *
     * @var string
     **/
    public $basename = null;

    /**
     * The size of the item
     *
     * @var int
     **/
    public $size   = null;

    /**
     * The width of the file (image)
     *
     * @var int
     **/
    public $width  = null;

    /**
     * The height of the file (image)
     *
     * @var int
     **/
    public $height = null;

    /**
     * Miscelaneous attributes of the file
     *
     * @var string
     **/
    public $attrs  = null;

    /**
     * The type of the image
     *
     * @var string
     **/
    public $type   = null;

    /**
     * The media type extracted from the file itself
     *
     * @var string
     **/
    public $internalType = null;

    /**
     * The last access time of the file
     *
     * @var string
     **/
    public $atime = null;

    /**
     * The last modification time of the file
     *
     * @var string
     **/
    public $mtime = null;

    /**
     * The description of the fie
     *
     * @var string
     **/
    public $description = null;

    /**
     * The tags of the file
     *
     * @var string
     **/
    public $tags = null;

    /**
     * Initializes the object from a file path
     *
     * @param string $file the file path
     *
     * @return MediaItem the object initialized
     **/
    public function __construct($file)
    {
        $this->filename = realpath($file);
        $this->basename = basename($this->filename);

        // Details of file
        $details = @stat($this->filename);

        $this->mtime	= $details['mtime'];
        $this->size     = $details['size'];
        $dimensions     = $this->getDimensions($this->filename);
        $this->width    = $dimensions[0];
        $this->height   = $dimensions[1];
        $this->attrs    = $dimensions[3];
        $this->type     = $this->getExtension();
        $this->internalType = $dimensions[2];
    }

    /**
     * Returns the height and width of the image file
     *
     * @param string $filename the absolute path of the file
     *
     * @return array an array with the height and width of the file
     **/
    public function getDimensions($filename = null)
    {
        if (is_null($filename)) {
            if (is_null($this->filename)) {
                return(null);
            }
            $filename = $this->filename;
        }

        $details = array();
        $details = @getimagesize($filename);

        return $details;
    }

    /**
     * Returns the extensions of a file
     *
     * @param string $filename the absolute path of the file
     *
     * @return string the extension of the file
     **/
    public function getExtension($filename = null)
    {
        if (is_null($filename)) {
            if (is_null($this->filename)) {
                return(null);
            }

            $filename = $this->filename;
        }

        $_d = pathinfo($filename);

        if (array_key_exists('extension', $_d)) {
            $extension = $_d['extension'];
        } else {
            $extension = '';
        }

        return $extension;
    }
}
