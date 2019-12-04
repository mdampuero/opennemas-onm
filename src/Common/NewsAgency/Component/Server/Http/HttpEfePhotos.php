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

use Common\NewsAgency\Component\Server\Server;

/**
 * Synchronize local folders with an external XML-based source server.
 */
class HttpEfePhotos extends HttpEfe
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters() : bool
    {
        if (array_key_exists('url', $this->params)
            && preg_match('@efeservicios.*?fotos@i', $this->getUrl())
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteFiles() : Server
    {
        $content = $this->getContentFromUrl($this->getUrl());

        if (!$content) {
            throw new \Exception(sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $this->params['name']
            ));
        }

        $xml   = simplexml_load_string($content);
        $files = $xml->xpath('//foto');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'filename' => (string) $value->attributes()->id . '.xml',
                'url'      => preg_replace([ '/\n/', '/\s+/' ], '', $value[0])
            ];
        }

        return $this;
    }
}
