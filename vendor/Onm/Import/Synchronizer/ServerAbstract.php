<?php
/**
 * Defines the ServerAbstract class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Importer
 **/
namespace Onm\Import\Synchronizer;

/**
 * Handles all the common methods in the servers
 *
 * @package Onm_Importer
 **/
abstract class ServerAbstract
{
    /**
     * Downloads files from a HTTP server to a $cacheDir.
     *
     * @param string $cacheDir Path to the directory where save files to.
     *
     * @return array counts of deleted and downloaded files
     *
     * @throws <b>Exception</b> $cacheDir not writable.
     */
    public function downloadFilesToCacheDir($params)
    {
        $downloadedFiles = 0;
        $deletedFiles = 0;

        foreach ($this->contentList as $content) {
            $id = $content->attributes()->{'id'};
            $url = trim((string) $content);

            if ($this->fetchContentAndSave($id, $url)) {
                $downloadedFiles++;
            }

            // Calculate $deletedFiles

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
