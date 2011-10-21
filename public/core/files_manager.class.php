<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Files Manager
 *
 * @package    Onm
 * @subpackage Utils
 **/
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

        $created =  mkdir($path, 0775, true);
        chmod($path, 0755);
        if(!$created) {
            // Register a critical error
            echo '<br> error'.$path;
            $GLOBALS['application']->logger->emerg("Error creating directory: " . $path);
        }
    }
    
    /**
     * Copy source path into destination path, and creates it if not exists.
     *
     * @param string $source fullpath for the copy 
     * @param string $destination path to the new destination
     */
    static public function recursiveCopy($source, $destination)
    {
        // Delete destination if exists
        if (file_exists($destination)) { unlink($destination); }
        
        // if is dir try to recursive copy it, if is a file copy it directly
        if (is_dir($source)) {
            if (!file_exists($destination)) { mkdir($destination,0775, true); }
            $files = scandir($source);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    self::recursiveCopy(
                        $source.DIRECTORY_SEPARATOR.$file,
                        $destination.DIRECTORY_SEPARATOR.$file
                    );
                }
            }
        } elseif (file_exists($source)) {
            copy($source, $destination);
        };
        return true;
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

    /**
     * Create a file in some path
     *
     * @param string $path Directory concat with filename
     */
     static public function mkFile($filename){
        if(!is_file($filename)) {
            $handle = fopen($filename,"x");
            if ($handle){
                fclose($handle);
                return true;
            }else{
                return false;
            }
        } else {
            return true; //file already exists
        }
    }

    /**
     * Write some content in a file
     *
     * @param string $file name
     * @param string $input content to save in a file
     */
    static public function writeInFile($file, $input){
        chmod($file, 0755);
        $handle = fopen($file, "w");
        if (!fwrite($handle, $input)) {
            return false; // failed.
        } else {
            return true; //success.
            fclose($handle);
        }

    }

}
