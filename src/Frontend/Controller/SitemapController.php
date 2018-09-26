<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for sitemaps
 */
class SitemapController extends Controller
{
    /**
     * Renders the index sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function indexAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->getCacheId('sitemap', 'index');

        return $this->buildResponse($format, $cacheID, null);
    }

    /**
     * Renders the sitemap web
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function webAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->getCacheId('sitemap', 'web');

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents([]);
            $tags     = [];

            // Remove external articles
            foreach ($contents as $key => &$content) {
                if (!empty($content->params['bodyLink'])) {
                    unset($contents[$key]);
                } else {
                    $tags = array_merge($content->tag_ids, $tags);
                }
            }
            $this->view->assign([
                'contents' => $contents,
                'tags'     => $this->getTags($tags)
            ]);
        }

        return $this->buildResponse($format, $cacheID, 'web');
    }

    /**
     * Renders the news sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function newsAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->getCacheId('sitemap', 'news');

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents([]);

            // Fetch images and videos from contents
            $er   = getService('entity_repository');
            $tags = [];
            foreach ($contents as $key => &$content) {
                if (!empty($content->params['bodyLink'])) {
                    unset($contents[$key]);
                    continue;
                }

                // Get content image
                if (!empty($content->img1)) {
                    $content->image = $er->find('Photo', $content->img2);
                } elseif (!empty($content->img2)) {
                    $content->image = $er->find('Photo', $content->img2);
                }

                // Get content video
                if (!empty($content->fk_video)) {
                    $content->video = $er->find('Video', $content->fk_video);
                } elseif (!empty($content->fk_video2)) {
                    $content->video = $er->find('Video', $content->fk_video2);
                }
                $tags = array_merge($content->tag_ids, $tags);
            }

            $this->view->assign([
                'contents'   => $contents,
                'googleNews' => $this->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('google_news_name'),
                'tags'       => $this->getTags($tags)
            ]);
        }

        return $this->buildResponse($format, $cacheID, 'news');
    }

    /**
     * Renders the image sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function imageAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->getCacheId('sitemap', 'image');

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Set sql filters for articles with inner image
            $filters = [
                'join' => [
                    [
                        'table'      => 'articles',
                        'pk_content' => [ [ 'value' => 'pk_article', 'field' => true ] ]
                    ]
                ],
                'content_type_name' => [['value' => 'article']],
                'img2'              => [['value' => 'NULL', 'operator' => '<>']],
            ];

            // Fetch contents
            $contents = $this->fetchContents($filters);

            // Fetch images and videos from contents
            $er = getService('entity_repository');
            foreach ($contents as $key => &$content) {
                if (!empty($content->params['bodyLink'])) {
                    // Remove external articles
                    unset($contents[$key]);
                    continue;
                }

                // Get content image
                if (!empty($content->img2)) {
                    $content->image = $er->find('Photo', $content->img2);
                }
            }

            $this->view->assign(['contents' => $contents]);
        }

        return $this->buildResponse($format, $cacheID, 'image');
    }

    /**
     * Renders the video sitemap
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function videoAction(Request $request)
    {
        $format = $request->query->filter('_format', 'xml', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('sitemap');
        $cacheID = $this->view->getCacheId('sitemap', 'video');

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('sitemap/sitemap.tpl', $cacheID)
        ) {
            // Fetch contents
            $contents = $this->fetchContents([
                'content_type_name' => [['value' => 'video']]
            ]);
            $tags     = [];
            foreach ($contents as $content) {
                $tags = array_merge($content->tag_ids, $tags);
            }

            $this->view->assign([
                'contents' => $contents,
                'tags'     => $this->getTags($tags)
            ]);
        }

        return $this->buildResponse($format, $cacheID, 'video');
    }

    /**
     * Formats the response
     *
     * @param string format whether compress the sitemap or not
     * @param string $cacheID the identifier for this cache
     * @param string $action the type of sitemap: news, web, image or video
     *
     * @return Response the response object
     */
    public function buildResponse($format, $cacheID, $action)
    {
        $contents = $this->renderView('sitemap/sitemap.tpl', [
            'action'   => $action,
            'cache_id' => $cacheID
        ]);

        if ($format == 'xml.gz') {
            // disable ZLIB output compression
            // ini_set('zlib.output_compression', 'Off');
            // compress data
            $contents = gzencode($contents, 9);

            $headers = [
                'Content-Type'        => 'application/x-gzip',
                'Content-Length'      => strlen($contents),
                'Content-Disposition' => 'attachment; filename="sitemap' . $action . '.xml.gz"'
            ];
        } else {
            // Return the output as xml
            $headers = ['Content-Type' => 'application/xml; charset=utf-8'];
        }

        $instanceName = getService('core.instance')->internal_name;

        $headers = array_merge($headers, [
            'x-cache-for'  => '1d',
            'x-cacheable'  => true,
            'x-instance'   => $instanceName,
            'x-tags'       => 'instance-' . $instanceName . ',sitemap,' . $action,
        ]);

        return new Response($contents, 200, $headers);
    }

    /**
     * Fetch articles and opinions contents
     *
     * @param int Max number of contents
     *
     * @return Array all contents
     */
    public function fetchContents($criteria = [], $limit = 100)
    {
        // Set search filters
        $filters = [
            'content_type_name' => [
                'union' => 'OR',
                ['value' => 'article'],
                ['value' => 'opinion']
            ],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        if (!empty($criteria)) {
            $filters = array_merge($filters, $criteria);
        }

        // Fetch contents
        $er       = getService('entity_repository');
        $contents = $er->findBy($filters, ['created' => 'desc'], $limit, 1);

        // Filter by scheduled
        $cm       = new \ContentManager();
        $contents = $cm->getInTime($contents);
        $contents = $cm->filterBlocked($contents);

        return $contents;
    }

    /**
     *  Method for recover all tags for the content list
     *
     * @param array $tagIds List of ids for tags
     *
     * @return array List of tags for the contents
     */
    public function getTags($tagIds)
    {
        $tagIds = array_unique($tagIds);
        return $this->get('api.service.tag')
            ->getListByIdsKeyMapped($tagIds)['items'];
    }
}
