<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\Synchronizer;

/**
 * Class to synchronize local folders with an external FTP folder.
 *
 * @package    Onm
 * @subpackage Import
 * @author     Fran Dieguez <fran@openhost.es>
 * @version    SVN: $Id: FTP.php 28842 Mér Xuñ 22 16:24:40 2011 frandieguez $
 */
class FTP {


    /*
     * Opens an FTP connection with the parameters of the object
     *
     * @throws Exception, if something went wrong while connecting to FTP server
     */
    public function __construct($params = null)
    {
        $this->params = $params;

        $this->serverUrl = parse_url($params['server']);
        

        $this->ftpConnection = @ftp_connect($this->serverUrl['host']);
        // test if the connection was successful
        if (!$this->ftpConnection) {
            throw new \Exception(sprintf(_('Can\'t connect to server %s. Contact with your administrator for support.'), $params['server']));
        }

        // if there is a ftp login configuration use it
        if (isset($params['user'])) {

            $loginResult = ftp_login($this->ftpConnection,
                                     $params['user'],
                                     $params['password']);
            

            if (!$loginResult) {
                throw new \Exception(sprintf(_('Can\'t login into server '), $params['server']));
            }

            if (isset($this->serverUrl['path'])) {
                if (!@ftp_chdir($this->ftpConnection, $this->serverUrl['path'])) {
                    throw new \Exception(sprintf(
                        _("Directory '%s' doesn't exists or you don't have enought permissions to acces it"),
                        $this->serverUrl['path']
                    ));
                }
            }

            return $this;
        }

    }

    /**
     * Downloads files from an FTP to a $cacheDir.
     *
     * @param string $cacheDir Path to the directory where save files to.
     *
     * @return array counts of deleted and downloaded files
     *
     * @throws <b>Exception</b> $cacheDir not writable.
     */
    public function downloadFilesToCacheDir($cacheDir, $excludedFiles = array())
    {

        $files = ftp_nlist($this->ftpConnection, ftp_pwd($this->ftpConnection));

        self::cleanWeirdFiles($cacheDir);
        $deletedFiles = self::cleanFiles($cacheDir,$files, $excludedFiles);

        $downloadedFiles = 0;

        if (is_writable($cacheDir)) {
            $elements = array();
            if (is_array($files) && count($files) > 0) {
                foreach($files as $file) {

                    
                    if (!isset($this->params['allowed_file_extesions_pattern']) 
                        || !preg_match('@'.$this->params['allowed_file_extesions_pattern'].'@', $file)
                    ) {
                        continue;
                    } else {
                        $elements []= $file;
                        $localFilePath = $cacheDir.DIRECTORY_SEPARATOR.strtolower(basename($file));
                        if (!file_exists($localFilePath)){
                            @ftp_get($this->ftpConnection,  $cacheDir.DIRECTORY_SEPARATOR.strtolower(basename($file)), $file, FTP_BINARY);
                            $downloadedFiles++;
                        }
                    }
                    
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
     * Remove empty or invalid files from $cacheDir.
     *
     * @param string $cacheDir The directory where remove files from.
     *
     * @return array list of deleted files
     */
    public function cleanWeirdFiles($cacheDir)
    {
        $fileListing = glob($cacheDir.DIRECTORY_SEPARATOR.'*.xml');

        $fileListingCleaned = array();

        foreach($fileListing as $file) {
            if (filesize($file) < 2) {
                unlink($file);
                $fileListingCleaned []= basename($file);
            }
        }

        return  $fileListingCleaned;
    }

    /**
     * Clean downloaded files in cacheDir that are not present in server
     *
     * @param string    $cacheDir       the directory where remove files
     * @param string    $serverFiles    the list of files present in server
     * @param string    $localFiles     the list of local files
     *
     * @return boolean, true if all went well
    */
    static public function cleanFiles($cacheDir, $serverFiles, $localFileList)
    {

        $deletedFiles = 0;

        if (count($localFileList) > 0) {

            $serverFileList = array();
            foreach ($serverFiles as $key) {
                $serverFileList []= strtolower(basename($key));
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
