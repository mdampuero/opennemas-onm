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
     * Gets a content from a given url.
     *
     * @param $url The http server URL.
     *
     * @return $content The content from this url.
     */
    public function getContentFromUrl($url)
    {
        $ch = curl_init();

        $auth = $this->params['username'] . ':' . $this->params['password'];
        $httpCode = '';
        $maxRedirects = 0;
        $maxRedirectsAllowed = 3;

        do {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            curl_setopt($ch, CURLOPT_USERPWD, $auth);
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

    /**
     * Filters files by its creation time.
     *
     * @param array   $files  The list of files to filter.
     * @param integer $maxAge The timestamp of the max age allowed.
     *
     * @return array The list of files.
     */
    protected function filterOldFiles($files, $maxAge)
    {
        if (empty($maxAge)) {
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
}
