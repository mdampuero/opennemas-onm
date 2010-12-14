<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Class MediaItem, represents a media file.
 * This class manipulate a swf, jpg, png, ... files. It's possible extract
 * all information to print this resource. By example: generate tags <embed ...
 * to the swf file.
*/
class MediaItem {
    /* Absolute path and file name */
    var $filename = null;
    var $basename = null;

    /* Details of media resource */
    var $size   = null;
    var $width  = null;
    var $height = null;
    var $attrs  = null;
    var $type   = null;
    var $internalType = null;

    /* Details of file */
    var $atime = null;
    var $mtime = null;

    /* Metadata */
    var $description = null;
    var $tags = null;

    function __construct($file) {
        $this->filename = realpath( $file );
        $this->basename = basename($this->filename);

        // Details of file
        $details = @stat( $this->filename );

		$this->mtime	= $details['mtime'];
		$this->size     = $details['size'];
		$dimensions     = $this->getDimensions($this->filename);
		$this->width    = $dimensions[0];
		$this->height   = $dimensions[1];
		$this->attrs    = $dimensions[3];
        $this->type     = $this->getExtension();
        $this->internalType = $dimensions[2];
    }

    function getDimensions($filename=null) {
        if(is_null($filename)) {
            if(is_null($this->filename)) {
                return(null);
            }
		$filename = $this->filename;
        }

        $details = array();
        $details = @getimagesize($filename);

        return($details);
    }

    function getExtension($filename=null) {
        if(is_null($filename)) {
            if(is_null($this->filename)) {
                return(null);
            }

			$filename = $this->filename;
        }

        $_d = pathinfo($filename);

        return( strtoupper($_d['extension']) );
    }

    function getHTMLTag() {

    }
}
 