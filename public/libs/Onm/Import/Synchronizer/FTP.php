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


    /*
     * Opens an FTP connection with the parameters of the object
     * 
     * @throws Exception, if something went wrong while connecting to FTP server
     */
	public function __construct($params = null)
	{
        $this->ftpConnection = ftp_connect($params['server']);
        
        // test if the connection was successful
        if (!$this->ftpConnection) {
            throw new \Exception(sprintf(_('Can\'t connect to server %s'), $params['server']));
        } else {
        
            // if there is a ftp login configuration use it
            if (isset($params['user'])) {
        
                $loginResult = ftp_login($this->ftpConnection,
                                         $params['user'],
                                         $params['password']);
       
                if (!$loginResult) {
                    throw new \Exception(sprintf(_('Can\'t login into server '), $params['server']));
                }
                return $this;
            }
            
        }
	}
    /*
     * TODO: Documentar
     */
    public function downloadFilesToCacheDir($cacheDir, $excludedFiles = array())
    {
        
        $files = ftp_nlist($this->ftpConnection, ftp_pwd($this->ftpConnection));
        
        $deletedFiles = self::cleanFiles($cacheDir,$files, $excludedFiles);
        
        $downloadedFiles = 0;
        
        if (is_writable($cacheDir)) {
            $elements = array();
            foreach($files as $file) {
                $elements []= $file;
                
                $localFilePath = $cacheDir.DIRECTORY_SEPARATOR.basename($file);
                if (!file_exists($localFilePath)){
                    ftp_get($this->ftpConnection,  $cacheDir.DIRECTORY_SEPARATOR.basename($file), $file, FTP_BINARY);
                    $downloadedFiles++;
                }
            }
        } else {
            throw new Exception(sprintf(_('Directory %s is not writable.'),$cacheDir));
        }
        
        return array(
                     "deleted" => $deletedFiles,
                     "downloaded" => $downloadedFiles
                     );
        
    }
    
    /**
     * Clean downloaded files in cacheDir that are not present in server
     *
     * @param files, the list of files present in server
     * @return boolean, true if all went well
    */
    static public function cleanFiles($cacheDir, $serverFiles, $localFileList)
    {

        $deletedFiles = 0;
        
        if (count($localFileList) > 0) {
            
            $serverFileList = array();
            foreach ($serverFiles as $key) {
                $serverFileList []= basename($key);
            }
            
            foreach ($localFileList as $file) {
                if(!in_array($file,$serverFileList)) {
                    unlink($cacheDir.'/'.$file);
                    $deletedFiles++;
                }
            }
        }
                
        return $deletedFiles;
    }
    

}