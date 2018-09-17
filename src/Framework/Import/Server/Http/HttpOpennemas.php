<?php
/*
 * This file is part of the onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Server\Http;

/**
 * Synchronize local folders with an HTTP opennemas server.
 */
class HttpOpennemas extends Http
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters($params)
    {
        if (array_key_exists('url', $params)
            && preg_match('@http(s)?://(.*)/ws/agency@', $params['url']) === 1
        ) {
            return true;
        }

        return false;
    }

    /**
     * Gets and returns the list of remote files.
     *
     * @return array The list of remote files.
     *
     * @throws \Exception
     */
    public function getRemoteFiles()
    {
        $url = $this->params['url'] . '/export.xml?until='
            . $this->params['sync_from'];

        $content = $this->getContentFromUrl($url);

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

        $xml = @simplexml_load_string($content);

        // Avoid errors when the content is not xml-parseable
        if (!is_object($xml)) {
            return [];
        }

        $files = $xml->xpath('//content');
        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'filename' => (string) $value->attributes()->id . '.xml',
                'url'      => preg_replace([ '/\n/', '/\s+/' ], '', $value[0])
            ];
        }

        return $this->remoteFiles;
    }
}
