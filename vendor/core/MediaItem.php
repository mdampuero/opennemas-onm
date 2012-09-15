<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class MediaItem, represents a media file.
 *
 * This class manipulate a swf, jpg, png, ... files. It's possible extract
 * all information to print this resource. By example: generate tags <embed ...
 * to the swf file.
 *
 * @package Onm
 * @subpackage Model
 */
class MediaItem
{

    /* Absolute path and file name */
    public $filename = null;
    public $basename = null;

    /* Details of media resource */
    public $size   = null;
    public $width  = null;
    public $height = null;
    public $attrs  = null;
    public $type   = null;
    public $internalType = null;

    /* Details of file */
    public $atime = null;
    public $mtime = null;

    /* Metadata */
    public $description = null;
    public $tags = null;

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

        return($details);
    }

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

    public function getHTMLTag()
    {

    }
}

