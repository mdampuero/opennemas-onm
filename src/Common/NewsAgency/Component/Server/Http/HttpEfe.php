<?php
/*
 * This file is part of the onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Server\Http;

/**
 * Synchronize local folders with an external XML-based source server.
 */
class HttpEfe extends Http
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters() : bool
    {
        if (array_key_exists('url', $this->params)
            && preg_match('@efeservicios@', $this->params['url'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getContentFromUrl(string $url) : string
    {
        $auth = '';

        if (array_key_exists('username', $this->params)) {
            $this->params['username'] . ':' . $this->params['password'];
        }

        $url = str_replace('http://', 'http://' . $auth . '@', $url);

        return @file_get_contents($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteFiles() : array
    {
        $content = $this->getContentFromUrl($this->params['url']);

        if (!$content) {
            throw new \Exception(sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $this->params['name']
            ));
        }

        $xml   = simplexml_load_string($content);
        $files = $xml->xpath('//elemento');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'filename' => (string) $value->attributes()->id . '.xml',
                'url'      => preg_replace([ '/\n/', '/\s+/' ], '', $value[0])
            ];
        }

        return $this->remoteFiles;
    }
}
