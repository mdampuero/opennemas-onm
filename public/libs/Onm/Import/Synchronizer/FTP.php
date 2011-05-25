<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * (c) Copyright Mér Mai 25 11:26:28 2011 Francisco Diéguez. All Rights Reserved.
*/
namespace Onm\Import\Synchronizer;

/*
 * class Sync
 */
class FTP {


	public function __construct($params = null)
	{

	}

    static public function downloadFilesToCacheDir($ftpConnection, $cacheDir)
    {
        
        $files = ftp_nlist($ftpConnection, ftp_pwd($ftpConnection));
        
        self::cleanFiles($cacheDir,$files);
        die();

        if (is_writable($cacheDir)) {
            $elements = array();
            foreach($files as $file) {
                $elements []= $file;
                
                $localFilePath = $cacheDir.DIRECTORY_SEPARATOR.basename($file);
                if (!file_exists($localFilePath)){
                    ftp_get($ftpConnection,  $cacheDir.DIRECTORY_SEPARATOR.basename($file), $file, FTP_BINARY);
                }
            }
        } else {
            throw new Exception(sprintf(_('Directory %s is not writable.'),$cacheDir));
        }
        
    }
    
    /**
     * Clean downloaded files in cacheDir that are not present in server
     *
     * @param files, the list of files present in server
     * @return boolean, true if all went well
    */
    static public function cleanFiles($cacheDir, $serverFiles)
    {
        $localFileList = self::getLocalFileList($cacheDir);
        
        $serverFileList = array();
        foreach ($serverFiles as $key) {
            $serverFileList []= $key;
        }
        
        foreach ($localFileList as $file) {
            var_dump($file, $serverFileList);
            if(!in_array($file,$serverFileList)) {
                //unlink($cacheDir.'/'.$file);
                var_dump("Cleaning file $cacheDir.$file");
            }
        }
        return true;
    }
    
    /*
     * function 
     */
    static public function getLocalFileList($cacheDir)
    {
        $fileListing = glob($cacheDir.DIRECTORY_SEPARATOR.'*.xml');
        $fileListingCleaned = array();
        
        foreach($fileListing as $file) {
            $fileListingCleaned []= basename($file);
        }
        
        return $fileListingCleaned;
    }

}