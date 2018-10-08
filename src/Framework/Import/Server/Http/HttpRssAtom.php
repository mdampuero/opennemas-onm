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
 * Synchronize local folders with an external RSS-Atom-based source server.
 */
class HttpRssAtom extends HttpRss
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters($params)
    {
        if (array_key_exists('url', $params)
            && preg_match('@rss|feed|xml@', $params['url'])
            && strpos(
                @file_get_contents($params['url']),
                'application/atom+xml'
            ) !== false
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
        $content = $this->getContentFromUrl($this->params['url']);

        if (!$content) {
            throw new \Exception(sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $this->params['name']
            ));
        }

        $xml = simplexml_load_string($content);

        $xml->registerXPathNamespace('f', 'http://www.w3.org/2005/Atom');
        $files = $xml->xpath('/f:feed/f:entry');

        foreach ($files as $value) {
            $this->remoteFiles[] = [
                'content'  => $value,
                'filename' => md5(urlencode((string) $value->link['href'])) . '.xml',
                'url'      => (string) $value->link['href']
            ];
        }

        return $this->remoteFiles;
    }

    /**
     * Saves a new NewsML files from a string.
     *
     * @param string $path    The path to the NewsML file.
     * @param string $content The NewsML file content.
     */
    protected function buildContentAndSave($path, $content)
    {
        $article = new \Article();

        $article->id               = md5($path);
        $article->title            = (string) $content->title;
        $article->body             = (string) $content->content;
        $article->summary          = (string) $content->summary;
        $article->created_datetime = new \DateTime($content->published);
        $article->updated_datetime = new \DateTime($content->updated);

        $newsMLString = $this->tpl->fetch('news_agency/newsml_templates/base.tpl', [
            'article' => $article,
            'tags'    => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($article->tag_ids)['items']
        ]);

        file_put_contents($path, $newsMLString);

        $time = $article->created_datetime->getTimestamp();
        touch($path, $time);
    }
}
