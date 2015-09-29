<?php
/*
 * This file is part of the onm package.
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Server\Http;

use Framework\Import\Server\Server;

/**
 * Synchronize local folders with an external XML-based source server.
 */
class HttpEfe extends Server
{
    /**
     * Opens an HTTP connection basing on the server parameters.
     *
     * @param array $params The server parameters.
     *
     * @throws \Exception If the server parameters are not valid.
     */
    public function __construct($params = null)
    {
        parent::__construct($params);

        $this->getRemoteFiles();
    }

    /**
     * {@inheritdoc}
     */
    public function checkParameters($params)
    {
        if (array_key_exists('url', $params)
            && preg_match('@efeservicios@', $params['url'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles($files = null)
    {
        if (empty($files)) {
            $files = $this->remoteFiles;
        }

        if (!is_writable($this->params['path'])) {
            throw new \Exception(
                sprintf(
                    _('Directory %s is not writable.'),
                    $this->params['path']
                )
            );
        }

        foreach ($files as $file) {
            $localFilePath = $this->params['path'] . DS . $file['filename'];

            if (!file_exists($localFilePath)) {
                $content = $this->getContentFromUrlWithDigestAuth($file['url']);

                file_put_contents($localFilePath, $content);

                $this->downloaded++;
            }
        }
    }

    /**
     * Gets and returns the list of remote files.
     *
     * @return array The list of remote files.
     */
    public function getRemoteFiles()
    {
        $content = $this->getContentFromUrlWithDigestAuth($this->params['url']);

        if (!$content) {
            throw new \Exception(
                sprintf(
                    _(
                        'Can\'t connect to server %s. Please check your'
                        .' connection details.'
                    ),
                    $this->params['name']
                )
            );
        }

        $xml   = simplexml_load_string($content);
        $files = $xml->xpath('//elemento');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'filename' => (string) $value->attributes()->id . '.xml',
                'url'      => (string) $value[0]
            ];
        }

        return $this->remoteFiles;
    }

    /**
     * Gets a content from a given URL using http digest auth.
     *
     * @param $url The URL.
     *
     * @return string The content from the URL.
     */
    public function getContentFromUrl($url)
    {
        $auth = $this->params['username'] . ':' . $this->params['password'];

        $url = str_replace('http://', 'http://' . $auth . '@', $url);

        $content = @file_get_contents($url);

        return $content;
    }
}
