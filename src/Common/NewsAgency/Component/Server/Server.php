<?php
/**
 * This file is part of the onm package.
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Server;

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
    protected $localFiles = [];

    /**
     * Files in server.
     *
     * @var array
     */
    protected $remoteFiles = [];

    /**
     * The template service.
     *
     * @var TemplateAdmin
     */
    protected $tpl;

    /**
     * Initializes a new Server.
     *
     * @param array         $params The server parameters.
     * @param TemplateAdmin $tpl    The template service.
     */
    public function __construct($params, $tpl)
    {
        $this->tpl    = $tpl;
        $this->params = $params;
    }

    /**
     * Returns the list of downloaded files.
     *
     * @return array The list of downloaded files.
     */
    public function getFiles() : array
    {
        return $this->localFiles;
    }

    /**
     * Gets a content from a given url.
     *
     * @param string $url The http server URL.
     *
     * @return strin The content from this URL.
     */
    protected function getContentFromUrl(string $url) : ?string
    {
        $ch   = curl_init();
        $auth = null;

        if (array_key_exists('username', $this->params)) {
            $auth = $this->params['username'] . ':' . $this->params['password'];
        }

        $httpCode     = 0;
        $redirects    = 0;
        $maxRedirects = 3;

        do {
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_HTTPAUTH       => CURLAUTH_DIGEST,
                CURLOPT_USERPWD        => $auth,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT        => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);

            $content  = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($httpCode == 301 || $httpCode == 302) {
                $url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

                continue;
            }

            if ($httpCode === 200) {
                return $content;
            }

            $redirects++;
        } while ($httpCode === 302 || $httpCode === 301 || $redirects < $maxRedirects);

        return null;
    }

    /**
     * Returns the URL for the current server based on the server configuration.
     *
     * @return string The server URL.
     */
    protected function getUrl()
    {
        return array_key_exists('url', $this->params)
            ? $this->params['url']
            : '';
    }

    /**
     * Checks if the application can connect to server.
     *
     * @return bool True if application can connect to server. False otherwise.
     */
    abstract public function checkConnection() : bool;

    /**
     * Checks if the current server parameter.
     *
     * @return bool True if the parameters are valid. False otherwise.
     */
    abstract public function checkParameters() : bool;

    /**
     * Gets and returns the list of remote files.
     *
     * @return Server The current server.
     */
    abstract public function getRemoteFiles() : Server;

    /**
     * Downloads the main files from server.
     *
     * @param string $path  The path to directory to download files to.
     * @param array  $files The list of missing files.
     *
     * @return Server The current server.
     *
     * @throws \Exception If the target directory is not writable.
     */
    abstract public function downloadFiles(string $path, ?array $files = null) : Server;
}
