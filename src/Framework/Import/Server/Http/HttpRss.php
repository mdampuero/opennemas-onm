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
 * Synchronize local folders with an external RSS-based source server.
 */
class HttpRss extends Http
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
            ) === false
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
            $localFile = $this->params['path'] . DS . $file['filename'];

            if (!file_exists($localFile)) {
                $this->buildContentAndSave($localFile, $file['content']);

                $this->localFiles[] = $localFile;
                $this->downloaded++;
            }
        }
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

        $fullBody = (string) htmlentities($content->description) . '<br>'
            . htmlentities($content->children('content', true));

        $article->id               = md5($path);
        $article->title            = (string) $content->title;
        $article->body             = $fullBody;
        $article->created_datetime = new \DateTime($content->pubDate);
        $article->updated_datetime = new \DateTime($content->pubDate);
        $article->category_name    = (string) $content->category;

        $newsMLString = $this->tpl->fetch(
            'news_agency/newsml_templates/base.tpl',
            [
                'article' => $article,
                'tags'    => getService('api.service.tag')
                    ->getListByIdsKeyMapped($article->tag_ids)['items']
            ]
        );

        file_put_contents($path, $newsMLString);

        $time = $article->created_datetime->getTimestamp();
        touch($path, $time);
    }
}
