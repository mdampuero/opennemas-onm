<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
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
 * @category   OpenNemas
 * @package    OpenNemas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Generate a thumbnail (if not exists) and return thumbnail web path
 * <code>
 * <img src="{thumbnail src=$params.IMAGE_DIR|cat:"main-logo.big.png" width="50" cachedir="/cache/thumbnails"}" />
 * <img src="{thumbnail src="http://www.google.es/intl/en_com/images/srpr/logo1w.png" width="100" height="20"}" />
 * </code>
 * 
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_thumbnail($params, &$smarty)
{        
    // Check src & width
    if(!isset($params['src'])) {
        $smarty->trigger_error("thumbnail: missing 'src' parameter");
        return;
    }
    
    if(!isset($params['width'])) {
        $smarty->trigger_error("thumbnail: missing 'width' parameter");
        return;
    }    
    
    $src = $params['src'];    
    $width = $params['width'];
    
    // height param is optional
    $height = (isset($params['height']))? $params['height']: $width;
    
    // Relative to SITE_PATH
    $cachedir = (isset($params['cachedir']))? $params['cachedir']: 'cache';
    
    // Thumbanail file name
    $thumbnailFilename = md5($src) . '_' . $width . 'x' . $height . '.' . pathinfo($src, PATHINFO_EXTENSION);            
    
    // Absolute path to thumbnail
    $thumbnailFile = SITE_PATH . '/' . $cachedir . '/' . $thumbnailFilename;    
    
    if(!file_exists($thumbnailFile)) {
        // Generate thumbnail
        try {
            $thumb = new Imagick();
            
            if(preg_match('@^http://@i', $src)) {
                // Load file (powered by stream wrappers)
                $fp = @fopen($src, 'rb');
            } else {
                $filename = realpath(SITE_PATH . '/' . $src);                
                $fp = @fopen($filename, 'rb');
            }
            
            if($fp !== false) {
                $thumb->readImageFile($fp);
                
                $thumb->thumbnailImage($width, $height, true);    
                $thumb->writeImage($thumbnailFile);
            } else {
                return $src;
            }
        } catch(Exception $ex) {    
            return $src;
        }
    }    
    
    $result = '/' . $cachedir . '/' . $thumbnailFilename;
    $result = preg_replace('@[/]+@', '/', $result);
    
    return $result;
}

