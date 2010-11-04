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
 * Files Manager
 *
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content.class.php 1 2010-04-20   $
 */
class FilesManager {
    /**
     * Create directories each content, if it don't exists
     * /images/ /files/, /advertisements/, /opinion/
     */
    public function createAllDirectories() {
        $dir_date = date("Y/m/d/");
        // /images/, /files/, /ads/, /opinion/
        // /media/images/año/mes/dia/
        //
        $dirs = array( MEDIA_IMG_DIR, MEDIA_FILE_DIR, MEDIA_ADS_DIR, MEDIA_OPINION_DIR );

        foreach($dirs as $dir) {
            $path = MEDIA_PATH.$dir.'/'.$dir_date ;
            FilesManager::createDirectory($path);
        }
    }

    /**
     * Create a new directory, if it don't exists
     *
     * @param string $path Directory to create
     */
    static public function createDirectory($path) {

        $created =  mkdir($path, 0777, true);
        if(!$created) {
            // Register a critical error
            echo '<br> error'.$path;
            $GLOBALS['application']->logger->emerg("Error creating directory: " . $path);
        }
    }

     /**
     * Check if a directory is empty
     *
     * @param string $path Directory to check
     */
    public function isDirEmpty($path){

        foreach(glob($path."/*") as $archivos) {
            return false; //tiene archivos o dirs que no son /. o /..
        }

        return true;
    }

     /**
     * Delete a directory is it exists
     *
     * @param string $path Directory to delete
     */
    public function deleteDirectory($path) {
 
        if(file_exists($path)) {
           if(rmdir($path)){
              $GLOBALS['application']->logger->emerg("Error deleting directory: " . $path);
           }
        }
    }

}