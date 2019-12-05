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
 * Synchronize local folders with an external RSS-based source server.
 */
class HttpRss extends Http
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters() : bool
    {
        if (array_key_exists('url', $this->params)
            && !preg_match('@efeservicios@', $this->params['url'])
            && preg_match('@rss|feed|xml@', $this->params['url'])
            && strpos(
                @file_get_contents($this->params['url']),
                'application/atom+xml'
            ) === false
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles(string $path, ?array $files = null) : Server
    {
        if (empty($files)) {
            $files = $this->remoteFiles;
        }

        if (!is_writable($path)) {
            throw new \Exception(
                sprintf(_('Directory %s is not writable.'), $path)
            );
        }

        foreach ($files as $file) {
            $localFile = $path . DS . $file['filename'];

            if (!file_exists($localFile)) {
                $this->generateNewsML($localFile, $file['content']);
                $this->downloaded++;
            }

            $this->localFiles[] = $localFile;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteFiles() : Server
    {
        $content = $this->getContentFromUrl($this->params['url']);

        if (!$content) {
            throw new \Exception(
                sprintf(
                    _(
                        'Can\'t connect to server %s. Please check your'
                        . ' connection details.'
                    ),
                    $this->params['name']
                )
            );
        }

        $xml = simplexml_load_string($content);

        if (!is_object($xml)) {
            return $this->remoteFiles;
        }

        $files = $xml->xpath('//channel/item');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'content'  => $value,
                'filename' => md5(urlencode((string) $value->link[0])) . '.xml',
                'url'      => (string) $value->link[0]
            ];
        }

        return $this;
    }

    /**
     * Saves a new NewsML files from a string.
     *
     * @param string           $path    The path to the NewsML file.
     * @param SimpleXMLElement $content The NewsML file content.
     */
    protected function generateNewsML(string $path, \SimpleXMLElement $xml) : void
    {
        $content = $this->parseXml($path, $xml);

        $newsML = $this->tpl->fetch('news_agency/newsml_templates/base.tpl', [
            'article' => $content,
            'tags'    => []
        ]);

        file_put_contents($path, $newsML);

        $time = $content->created_datetime->getTimestamp();

        touch($path, $time);
    }

    /**
     * Parses the XML and returns an object with the content information.
     *
     * @param string            $path    The path to the XML file.
     * @param \SimpleXMLElement $content The content as XML.
     *
     * @return \StdClass The content.
     */
    protected function parseXml(string $path, \SimpleXMLElement $content) : \StdClass
    {
        $article = new \StdClass();

        $body = (string) htmlentities($content->description) . '<br>'
            . htmlentities($content->children('content', true));

        $article->id               = md5($path);
        $article->title            = (string) $content->title;
        $article->body             = (string) $body;
        $article->created_datetime = new \DateTime($content->pubDate);
        $article->updated_datetime = new \DateTime($content->pubDate);
        $article->category_name    = (string) $content->category;
        $article->tags             = [];

        return $article;
    }
}
