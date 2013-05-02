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
     **/
    public function downloadFilesToCacheDir($params)
    {
        $downloadedFiles = 0;
        $deletedFiles = 0;

        foreach ($this->contentList as $content) {
            $id = $content->attributes()->{'id'};
            $url = trim((string) $content);
            $files[] = $id.'.xml';

            if ($this->fetchContentAndSave($id, $url)) {
                $downloadedFiles++;
            }
        }

        foreach ($files as $file) {
            $serverFiles[] = array(
                'filename' => $file,
            );
        }

        // Filter files by its creation
        self::cleanWeirdFiles($params['sync_path']);
        $deletedFiles = self::cleanFiles(
            $params['sync_path'],
            $serverFiles,
            $params['excluded_files'],
            $params['sync_from']
        );

        return array(
            "deleted"    => $deletedFiles,
            "downloaded" => $downloadedFiles
        );
    }

    /**
     * Fetch content from an url and save the file into a local path
     *
     * @param $id the id of the file to be saved
     * @param $url the url from where to get the file content
     *
     * @return true if the file has been saved correctly
     **/
    public function fetchContentAndSave($id, $url)
    {
        $content = $this->getContentFromUrlWithDigestAuth($url);

        $localFilePath = $this->params['sync_path'].DS.strtolower($id.'.xml');
        if (!file_exists($localFilePath)) {
            @file_put_contents($localFilePath, $content);

            $element = \Onm\Import\DataSource\DataSourceFactory::get($localFilePath);
            if (is_object($element)) {
                if ($element->hasPhotos()) {
                    $photos = $element->getPhotos();
                    foreach ($photos as $photo) {
                        $rawImage = $this->getContentFromUrlWithDigestAuth($photo->file_path);
                        $localImagePath = $this->params['sync_path'].DS.$photo->name;
                        if (file_exists($localImagePath)) {
                            unlink($localImagePath);
                        }
                        @file_put_contents($localImagePath, $rawImage);
                    }
                }

                $date = $element->getCreatedTime();
                touch($localFilePath, $date->getTimestamp());
            }

            return true;
        }

    }

    /**
     * Remove empty or invalid files from $cacheDir.
     *
     * @param string $cacheDir The directory where remove files from.
     *
     * @return array list of deleted files
     **/
    public function cleanWeirdFiles($cacheDir)
    {
        $fileListing = glob($cacheDir.DS.'*.xml');

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
     * @return int, number of total downloaded files
    */
    public static function cleanFiles($cacheDir, $serverFiles, $localFileList)
    {
        $deletedFiles = 0;

        if (count($localFileList) > 0) {
            $serverFileList = array();
            foreach ($serverFiles as $key) {
                $serverFileList []= strtolower(basename($key['filename']));
            }

            foreach ($localFileList as $file) {
                $file = basename($file);
                $filePath = $cacheDir.'/'.$file;
                if (!in_array($file, $serverFileList)) {
                    if (file_exists($filePath)) {
                        unlink($filePath);

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

    /**
     * Get content from a given url using http digest auth and curl
     *
     * @param $url the http server url
     *
     * @return $content the content from this url
     *
     **/
    public function getContentFromUrlWithDigestAuth($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->params['username']}:{$this->params['password']}");

        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }
}
