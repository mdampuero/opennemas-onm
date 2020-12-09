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
 * Synchronize local folders with an external RSS-Atom-based source server.
 */
class HttpRssAtom extends HttpRss
{
    /**
     * {@inheritdoc}
     */
    public function checkConnection() : bool
    {
        $content = $this->getContentFromUrl($this->getUrl());

        return !empty($content)
            && strpos($content, 'application/atom+xml') !== false;
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

        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOERROR);

        if (empty($xml)) {
            return $this;
        }

        $xml->registerXPathNamespace('f', 'http://www.w3.org/2005/Atom');
        $files = $xml->xpath('/f:feed/f:entry');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'content'  => $value,
                'filename' => md5(urlencode((string) $value->link['href'])) . '.xml',
                'url'      => (string) $value->link['href']
            ];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseXml(string $path, \SimpleXMLElement $content) : \StdClass
    {
        $article = new \StdClass();

        $article->id               = md5($path);
        $article->title            = (string) $content->title;
        $article->body             = (string) $content->content;
        $article->created_datetime = new \DateTime($content->published);
        $article->updated_datetime = new \DateTime($content->updated);
        $article->summary          = (string) $content->summary;
        $article->tags             = [];
        $article->externalUri      = (string) $content->link['href'];

        return $article;
    }
}
