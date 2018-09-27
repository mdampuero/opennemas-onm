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
use Common\Core\Controller\Controller;

/**
 * Handles the actions for sitemaps
 */
class SitemapController extends Controller
{
    /**
     * Renders the index sitemap
     *
     * @param string $action The sitemap action.
     * @param string $format The sitemap format.
     *
     * @return Response the response object
     */
    public function indexAction($action, $format)
    {
        $action  = empty($action) ? 'index' : $action;
        $cacheId = $this->view->getCacheId('sitemap', $action);
        $method  = 'generate' . $action . 'Sitemap';

        $this->view->setConfig('sitemap');

        if (method_exists($this, $method)
            && (empty($this->view->getCaching())
                || !$this->view->isCached('sitemap/sitemap.tpl', $cacheId)
            )
        ) {
            $this->{$method}();
        }

        return $this->buildResponse($format, $cacheId, $action);
    }

    /**
     * Generates and assigns the information for the image sitemap to template.
     */
    protected function generateImageSitemap()
    {
        $contents = $this->getContents([
            'join' => [
                [
                    'table'      => 'articles',
                    'pk_content' => [ [ 'value' => 'pk_article', 'field' => true ] ]
                ]
            ],
            'content_type_name' => [['value' => 'article']],
            'img2'              => [['value' => 'NULL', 'operator' => '<>']],
        ]);

        $em = getService('entity_repository');

        foreach ($contents as &$content) {
            if (!empty($content->img2)) {
                $content->image = $em->find('Photo', $content->img2);
            }
        }

        $this->view->assign(['contents' => $contents]);
    }

    /**
     * Generates and assigns the information for the news sitemap to template.
     */
    protected function generateNewsSitemap()
    {
        $tags     = [];
        $contents = $this->getContents();
        $em       = $this->get('entity_repository');

        foreach ($contents as &$content) {
            if (!empty($content->img1)) {
                $content->image = $em->find('Photo', $content->img2);
            } elseif (!empty($content->img2)) {
                $content->image = $em->find('Photo', $content->img2);
            }

            if (!empty($content->fk_video)) {
                $content->video = $em->find('Video', $content->fk_video);
            } elseif (!empty($content->fk_video2)) {
                $content->video = $em->find('Video', $content->fk_video2);
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

    /**
     * Generates and assigns the information for the video sitemap to template.
     */
    protected function generateVideoSitemap()
    {
        $tags     = [];
        $contents = $this->getContents([
            'content_type_name' => [ ['value' => 'video'] ]
        ]);

        foreach ($contents as $content) {
            $tags = array_merge($content->tag_ids, $tags);
        }

        $this->view->assign([
            'contents' => $contents,
            'tags'     => $this->getTags($tags)
        ]);
    }

    /**
     * Generates and assigns the information for the web sitemap to template.
     */
    protected function generateWebSitemap()
    {
        $contents = $this->getContents();

        $this->view->assign([ 'contents' => $contents ]);
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
    public function buildResponse($format, $cacheId, $action)
    {
        $headers  = [ 'Content-Type' => 'application/xml; charset=utf-8' ];
        $instance = $this->get('core.instance')->internal_name;

        $contents = $this->renderView('sitemap/sitemap.tpl', [
            'action'   => $action,
            'cache_id' => $cacheId
        ]);

        if ($format === 'xml.gz') {
            $headers = [
                'Content-Type'        => 'application/x-gzip',
                'Content-Length'      => strlen($contents),
                'Content-Disposition' => 'attachment; filename="sitemap'
                    . $action . '.xml.gz"'
            ];

            $contents = gzencode($contents, 9);
        }

        $headers = array_merge($headers, [
            'x-cache-for' => '1d',
            'x-cacheable' => true,
            'x-instance'  => $instance,
            'x-tags'      => sprintf('instance-%s,sitemap,%s', $instance, $action)
        ]);

        return new Response($contents, 200, $headers);
    }

    /**
     * Returns the list of contents basing on a criteria.
     *
     * @param array   $criteria The criteria to search by.
     * @param integer $limit    The maximum number of contents to return.
     *
     * @return Array The list of contents
     */
    public function getContents($criteria = [], $limit = 100)
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
        $em       = $this->get('entity_repository');
        $contents = $em->findBy($filters, ['created' => 'desc'], $limit, 1);

        // Filter by scheduled
        $cm       = new \ContentManager();
        $contents = $cm->getInTime($contents);
        $contents = $cm->filterBlocked($contents);

        $contents = array_filter($contents, function ($a) {
            return !array_key_exists('bodyLink', $a->params)
                || empty($a->params['bodyLink']);
        });

        return $contents;
    }

    /**
     *  Method for recover all tags for the content list
     *
     * @param array $tagIds List of ids for tags
     *
     * @return array List of tags for the contents
     */
    protected function getTags($tagIds)
    {
        $tagIds = array_unique($tagIds);
        return $this->get('api.service.tag')
            ->getListByIdsKeyMapped($tagIds)['items'];
    }
}
