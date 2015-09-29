<?php
/**
 * This file is part of the onm package.
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Server;

use Framework\Import\ParserFactory;

/**
 * Handles all the common methods in the servers
 */
abstract class Server
{
    /**
     * The number of downloaded files.
     *
     * @var integer
     */
    public $downloaded = 0;

    /**
     * The number of deleted files.
     *
     * @var integer
     */
    public $deleted = 0;

    /**
     * Files donwloaded from server.
     *
     * @var array
     */
    public $localFiles = [];

    /**
     * Files in server.
     *
     * @var array
     */
    public $remoteFiles = [];

    /**
     * Initializes a new Server.
     *
     * @param array $params The server parameters.
     *
     * @throws \Exception If the server parameters are not valid.
     */
    public function __construct($params)
    {
        if (!$this->checkParameters($params)) {
            throw new \Exception('Invalid parameters for server');
        }

        $this->params  = $params;
        $this->factory = new ParserFactory();

        if (array_key_exists('path', $this->params)) {
            $this->localFiles = glob($this->params['path'] . DS . '*.xml');

            $this->cleanFiles();
        }
    }

    /**
     * Clean local files that are not present in server.
     */
    public function cleanFiles()
    {
        $deleted = [];
        foreach ($this->localFiles as $file) {
            var_dump(filesize($file));
            $modTime = filemtime($file);
            $limit   = time() - $this->params['sync_from'];

            if (filesize($file) < 2 || $modTime < $limit) {
                unlink($file);
                $deleted[] = $file;
            }
        }

        $this->deleted += count($deleted);

        $this->localFiles = array_diff($this->localFiles, $deleted);
    }

    /**
     * Checks if the current server parameter.
     *
     * @param array $params Server parameters.
     *
     * @return boolean True if the parameters are valid. Otherwise, returns
     *                 false.
     */
    abstract public function checkParameters($params);

    /**
     * Downloads the main files from server.
     *
     * @param array $files The list of missing files.
     *
     * @throws \Exception If the target directory is not writable.
     */
    abstract public function downloadFiles($files = null);




















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
            $imagesName = array_merge($imagesName, $this->getImagesNameFromLocalContent($id));
        }

        // Add all xml files name on serverFiles array
        foreach ($files as $file) {
            $serverFiles[] = array(
                'filename' => $file,
            );
        }
        // Add all images name on serverFiles array
        foreach ($imagesName as $name) {
            if (!empty($name)) {
                $serverFiles[] = array(
                    'filename' => $name,
                );
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

            $parser = $this->factory->get($localFilePath);

            $elements = $parser->parse($localFilePath);

            foreach ($elements as $element) {
                if (is_object($element)
                    && ($element->type === 'photo'
                        || $element->type === 'video')
                ) {
                    $localPath = $this->params['sync_path'] . DS . $element->title;

                    if (file_exists($localPath)) {
                        unlink($localPath);
                    }

                    $raw = $this->getContentFromUrlWithDigestAuth($element->file_path);

                    @file_put_contents($localPath, $raw);

                    $date = $element->getCreatedTime();

                    touch($localFilePath, $date->getTimestamp());
                }
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
            $parser = $this->factory->get($localFilePath);

            $elements = $parser->parse($localFilePath);

            foreach ($elements as $element) {
                if (is_object($element) && $element->type === 'photo') {
                    $imagesName[] = $element->title;
                }
            }

            return $imagesName;
        }
    }

    /**
     * Filters files by its creation
     *
     * @param  array $files  the list of files for filtering
     * @param  int   $maxAge timestamp of the max age allowed for files
     * @return array the list of files without those with age > $magAge
     **/
    protected function filterOldFiles($files, $maxAge)
    {
        if (!empty($maxAge)) {
            return $files;
        }

        $files = array_filter(
            $files,
            function ($item) use ($maxAge) {
                if ($item['filename'] == '..' || $item['filename'] == '.') {
                    return false;
                }

                return (time() - $maxAge) < $item['date']->getTimestamp();
            }
        );

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

        } while ($httpCode == 302 || $httpCode == 301 || $maxRedirects > $maxRedirectsAllowed);

        curl_close($ch);

        return $content;
    }
}
