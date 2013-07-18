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

        $files = array();
        $imagesName = array();
        $serverFiles = array();
        foreach ($this->contentList as $content) {
            $id = $content->attributes()->{'id'};
            $url = trim((string) $content);
            $files[] = $id.'.xml';

            if ($this->fetchContentAndSave($id, $url)) {
                $downloadedFiles++;
            }
            // Fetch all images name
            $imagesName[] = $this->getImagesNameFromLocalContent($id);
        }

        // Add all xml files name on serverFiles array
        foreach ($files as $file) {
            $serverFiles[] = array(
                'filename' => $file,
            );
        }

        // Add all images name on serverFiles array
        foreach ($imagesName as $names) {
            if (!empty($names)) {
                foreach ($names as $name) {
                    $serverFiles[] = array(
                        'filename' => $name,
                    );
                }
            }
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
                // Check for photos
                if ($element->hasPhotos()) {
                    $photos = $element->getPhotos();
                    $i = 0;
                    foreach ($photos as $photo) {
                        $rawImage = $this->getContentFromUrlWithDigestAuth($photo->file_path);
                        $localImagePath = $this->params['sync_path'].DS.$photo->name[$i];
                        if (file_exists($localImagePath)) {
                            unlink($localImagePath);
                        }
                        @file_put_contents($localImagePath, $rawImage);
                        $i++;
                    }
                }

                // Check for videos
                if ($element->hasVideos()) {
                    $videos = $element->getVideos();
                    foreach ($videos as $video) {
                        $rawVideo = $this->getContentFromUrlWithDigestAuth($video->file_path);
                        $localVideoPath = $this->params['sync_path'].DS.$video->name[$i];
                        if (file_exists($localVideoPath)) {
                            unlink($localVideoPath);
                        }
                        @file_put_contents($localVideoPath, $rawVideo);
                        $i++;
                    }
                }

                $date = $element->getCreatedTime();

                touch($localFilePath, $date->getTimestamp());
            }

            return true;
        }
    }

    /**
     * Fetch images from a given xml content
     *
     * @param $id the id of the local file
     *
     * @return array $imagesName an array with all images from xml contents
     **/
    public function getImagesNameFromLocalContent($id)
    {
        $localFilePath = $this->params['sync_path'].DS.strtolower($id.'.xml');
        if (file_exists($localFilePath)) {
            $imagesName = array();
            $element = \Onm\Import\DataSource\DataSourceFactory::get($localFilePath);
            if (is_object($element)) {
                if ($element->hasPhotos()) {
                    $photos = $element->getPhotos();
                    $i = 0;
                    foreach ($photos as $photo) {
                        $imagesName[] = $photo->name[$i];
                        $i++;
                    }
                }
            }

            return $imagesName;
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
    public static function cleanFiles($cacheDir, $serverFiles, $localFileList, $syncFrom)
    {
        $deletedFiles = 0;

        if (count($localFileList) > 0) {
            $serverFileList = array();

            foreach ($localFileList as $file) {
                $file = basename($file);
                $filePath = $cacheDir.DIRECTORY_SEPARATOR.$file;

                $fileModTime = filemtime($filePath);
                $timeLimit = time() - $syncFrom;

                if ($fileModTime < $timeLimit) {
                    unlink($filePath);

                    $deletedFiles++;
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

        $httpCode = '';
        $maxRedirects = 0;
        $maxRedirectsAllowed = 3;
        do {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($ch, CURLOPT_USERPWD, "{$this->params['username']}:{$this->params['password']}");
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $content = curl_exec($ch);

            $response = explode("\r\n\r\n", $content);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $content = $response[count($response) -1];

            if ($httpCode == 301 || $httpCode == 302) {
                $matches = array();
                preg_match('/(Location:|URI:)(.*?)\n/', $response[0], $matches);
                $url = trim(array_pop($matches));
            }

            $maxRedirects++;
        } while ($httpCode != 200 || $maxRedirects > $maxRedirectsAllowed);

        curl_close($ch);

        return $content;
    }
}
