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
 * @package    Onm_Import
 */
class FTP
{
    /**
     * Opens an FTP connection with the parameters of the object
     *
     * @param array $params the list of params to the ftp connection
     *
     * @throws Exception, if something went wrong while connecting to FTP server
     */
    public function __construct($params = null)
    {
        $this->params = $params;

        $this->serverUrl = parse_url($params['url']);

        $this->ftpConnection = @ftp_connect($this->serverUrl['host']);
        // test if the connection was successful
        if (!$this->ftpConnection) {
            throw new \Exception(
                sprintf(
                    _(
                        'Can\'t connect to server %s. Please check your'
                        .' connection details.'
                    ),
                    $params['name']
                )
            );
        }

        ftp_pasv($this->ftpConnection, true);

        // if there is a ftp login configuration use it
        if (isset($params['username'])) {

            $loginResult = ftp_login(
                $this->ftpConnection,
                $params['username'],
                $params['password']
            );

            if (!$loginResult) {
                throw new \Exception(
                    sprintf(_('Can\'t login into server %s'), $params['server'])
                );
            }

            if (isset($this->serverUrl['path'])) {
                if (!@ftp_chdir($this->ftpConnection, $this->serverUrl['path'])) {
                    throw new \Exception(
                        sprintf(
                            _(
                                "Directory '%s' in the server '%s' doesn't exists or "
                                ."you don't have enought permissions to access it"
                            ),
                            $this->serverUrl['path'],
                            $this->serverUrl['host']
                        )
                    );
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
    public function downloadFilesToCacheDir(
        $cacheDir,
        $excludedFiles = array(),
        $maxAge = null
    ) {
        $files = ftp_rawlist(
            $this->ftpConnection,
            ftp_pwd($this->ftpConnection),
            true
        );

        $files = $this->_filterOldFiles(
            $this->formatRawFtpFileList($files),
            $maxAge
        );

        // Filter files by its creation
        self::cleanWeirdFiles($cacheDir);
        $deletedFiles = self::cleanFiles(
            $cacheDir,
            $files,
            $excludedFiles,
            $maxAge
        );

        $downloadedFiles = 0;

        if (is_writable($cacheDir)) {
            $elements = array();
            if (is_array($files) && count($files) > 0) {
                foreach ($files as $file) {
                    $fileExtensions =
                        $this->params['allowed_file_extesions_pattern'];
                    if (!isset($this->params['allowed_file_extesions_pattern'])
                        || !preg_match('@'.$fileExtensions.'@', $file['filename'])
                    ) {
                        continue;
                    } else {
                        $elements[]    = $file;
                        $localFilePath =
                            $cacheDir.DIRECTORY_SEPARATOR.strtolower(basename($file['filename']));
                        if (!file_exists($localFilePath)) {
                            @ftp_get(
                                $this->ftpConnection,
                                $localFilePath,
                                $file['filename'],
                                FTP_BINARY
                            );


                            $element = \Onm\Import\DataSource\DataSourceFactory::get($localFilePath);
                            if (is_object($element)) {
                                $date = $element->getCreatedTime();
                            } else {
                                $date = $file['date'];
                            }

                            touch($localFilePath, $date->getTimestamp());

                            $downloadedFiles++;
                        }
                    }
                }
            }
        } else {
            throw new Exception(
                sprintf(_('Directory %s is not writable.'), $cacheDir)
            );
        }

        return array(
            "deleted"    => $deletedFiles,
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

        foreach ($fileListing as $file) {
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
     * @param string $cacheDir    the directory where remove files
     * @param string $serverFiles the list of files present in server
     * @param string $localFiles  the list of local files
     *
     * @return boolean, true if all went well
    */
    public static function cleanFiles(
        $cacheDir,
        $serverFiles,
        $localFileList,
        $maxAge
    ) {
        $deletedFiles = 0;

        if (count($localFileList) > 0) {
            $serverFileList = array();
            foreach ($serverFiles as $key) {
                $serverFileList []= strtolower(basename($key['filename']));
            }

            foreach ($localFileList as $file) {
                if (!in_array($file, $serverFileList)) {
                    $file = basename($file);
                    $filePath = $cacheDir.'/'.$file;

                    if (file_exists($filePath)) {
                        unlink($cacheDir.'/'.$file);

                        $deletedFiles++;
                    }
                }
            }
        }

        return $deletedFiles;
    }

    /**
     * Converts a raw file list from a FTP connection to a formatted array list
     *
     * @return array list of files with its properties
     **/
    protected function formatRawFtpFileList($rawFiles = '')
    {
        // here the magic begins!
        $structure = array();
        $arraypointer = &$structure;
        foreach ($rawFiles as $rawfile) {
            if ($rawfile[0] == '/') {
                $paths =
                    array_slice(explode('/', str_replace(':', '', $rawfile)), 1);
                $arraypointer = &$structure;
                foreach ($paths as $path) {
                    foreach ($arraypointer as $i => $file) {
                        if ($file['text'] == $path) {
                            $arraypointer = &$arraypointer[ $i ]['children'];
                            break;
                        }
                    }
                }
            } elseif (!empty($rawfile)) {
                $info = preg_split("/[\s]+/", $rawfile, 9);
                $arraypointer[] = array(
                    'filename'   => $info[8],
                    'isDir'  => $info[0]{0} == 'd',
                    'size'   => $this->_byteconvert($info[4]),
                    'chmod'  => $this->_chmodnum($info[0]),
                    'date'   => \DateTime::createFromFormat(
                        'd M H:i',
                        $info[6] . ' ' . $info[5] . ' ' . $info[7]
                    ),
                    'raw'    => $info,
                    'raw2'   => $rawfile,
                    // the 'children' attribut is automatically added
                    // if the folder contains at least one file
                );
            }
        }

        return $structure;
    }

    /**
     * Converts a byte based file size to a human readable string
     * @param  integer $bytes the amount of bytes of the file
     * @return string  the human readable file size
     */
    protected function _byteconvert($bytes)
    {
        $symbol = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        if ($bytes > 0) {
            $exp    = floor(log($bytes)/log(1024));
        } else {
            $exp = 0;
        }

        return sprintf('%.2f '.$symbol[$exp], ($bytes/pow(1024, floor($exp))));
    }

    /**
     * Converts an chmod string to a numeric based file permissions
     * @param  string  $chmod the chmod string-based file perms
     * @return integer the numeric based file permissions
     */
    protected function _chmodnum($chmod)
    {
        $trans = array('-' => '0', 'r' => '4', 'w' => '2', 'x' => '1');
        $chmod = substr(strtr($chmod, $trans), 1);
        $array = str_split($chmod, 3);

        return array_sum(str_split($array[0]))
            . array_sum(str_split($array[1]))
            . array_sum(str_split($array[2]));
    }

    /**
     * Filters files by its creation
     *
     * @param  array $files  the list of files for filtering
     * @param  int   $maxAge timestamp of the max age allowed for files
     * @return array the list of files without those with age > $magAge
     **/
    protected function _filterOldFiles($files, $maxAge)
    {
        if (!empty($maxAge)) {
            $files = array_filter(
                $files,
                function ($item) use ($maxAge) {
                    if ($item['filename'] == '..' || $item['filename'] == '.') {
                        return false;
                    }
                    return (time() - $maxAge) < $item['date']->getTimestamp();
                }
            );
        }

        return $files;
    }
}
