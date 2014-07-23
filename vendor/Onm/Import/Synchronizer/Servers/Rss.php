<?php
/*
 * Implements the EFE class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\Synchronizer\Servers;

use \Onm\Import\Synchronizer\ServerAbstract;
use \Onm\Import\Synchronizer\ServerInterface;

/**
 * Class to synchronize local folders with an HTTP Efe server.
 *
 * @package    Onm_Import
 */
class Rss extends ServerAbstract implements ServerInterface
{
    /**
     * Opens an HTTP connection with the parameters of the object
     *
     * @param array $params the list of params to the http connection
     *
     * @throws Exception, if something went wrong while connecting to HTTP server
     */
    public function __construct($params = null)
    {
        $this->canHandle($params);

        $this->params = $params;

        $this->serverUrl = $params['url'];

        $contentListString = $this->getContentFromUrlWithDigestAuth($this->serverUrl);
        // test if the connection was successful
        if (!$contentListString) {
            throw new \Exception(
                sprintf(
                    _(
                        'Can\'t connect to server %s. Please check your'
                        .' connection details.'
                    ),
                    $params['name']
                )
            );
        }

        $this->contentList = simplexml_load_string($contentListString);

        return $this;
    }

    /**
     * Get content from a given url using http digest auth
     *
     * @param $url the http server url
     *
     * @return $content the content from this url
     *
     **/
    public function getContentFromUrlWithDigestAuth($url)
    {
        $content = @file_get_contents($url);

        return $content;
    }

    /**
     * Downloads files from a HTTP server to a $cacheDir.
     *
     * @param string $cacheDir Path to the directory where save files to.
     *
     * @return array counts of deleted and downloaded files
     **/
    public function downloadFilesToCacheDir($params)
    {
        $downloadedFiles = 0;
        $deletedFiles = 0;

        $files = array();
        $imagesName = array();
        $serverFiles = array();

        foreach ($this->contentList->channel->item as $content) {
            $id = $content->guid;
            $files[] = $id.'.xml';

            if ($this->buildContentAndSave($id, $content, $params)) {
                $downloadedFiles++;
            }
        }

        // Filter files by its creation
        self::cleanWeirdFiles($params['sync_path']);
        $deletedFiles = 0;
        self::cleanFiles(
            $params['sync_path'],
            $serverFiles,
            $params['excluded_files'],
            $params['sync_from']
        );

        // Add all xml files name on serverFiles array
        foreach ($files as $file) {
            $serverFiles[] = array(
                'filename' => $file,
            );
        }

        return array(
            "deleted"    => $deletedFiles,
            "downloaded" => $downloadedFiles
        );
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function buildContentAndSave($id, $content, $params)
    {
        $article = new \Article();

        $article->id               = (string) $content->guid;
        $article->title            = (string) $content->title;
        $article->body             = (string) $content->description;
        $article->created_datetime = new \DateTime($content->pubDate);
        $article->updated_datetime = new \DateTime($content->pubDate);
        $article->category_name    = (string) $content->category;

        $tpl = new \TemplateAdmin();
        $newsMLString = $tpl->fetch(
            'news_agency/newsml_templates/base.tpl',
            array('article' => $article)
        );

        $path = $this->params['sync_path'].DS.strtolower($id.'.xml');

        file_put_contents($path, $newsMLString);

        $time = $article->created_datetime->getTimestamp();
        touch($path, $time);

        return true;
    }


    /**
     * Check if this server class can handle the http service
     *
     * @param $params the http server parameters
     *
     * @return true if the url matches the pattern for this server
     *
     * @throws Exception, if this server class can't handle this service url
     **/
    public function canHandle($params)
    {
        // Check url
        $res = preg_match('@rss@', $params['url'], $matches);
        if ($res) {
            return true;
        }

        throw new \Exception(
            sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $params['name']
            )
        );

    }
}
