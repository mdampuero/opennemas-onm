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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Displays contents as sitemaps.
 */
class SitemapController extends Controller
{
    /**
     * The list of expire times for actions.
     *
     * @const array
     */
    const EXPIRE = [
        'image' => '1h',
        'index' => '1d',
        'news'  => '1h',
        'tag'   => '1d',
        'video' => '1h',
        'web'   => '1h',
    ];

    /**
     * The list of needed extensions per action.
     *
     * @var array
     */
    const EXTENSIONS = [
        'image' => [ 'IMAGE_MANAGER' ],
        'news'  => [ 'ARTICLE_MANAGER' ],
        'tag'   => [ 'es.openhost.module.tagsSitemap' ],
        'video' => [ 'VIDEO_MANAGER' ],
        'web'   => [ 'ARTICLE_MANAGER', 'OPINION_MANAGER' ],
    ];

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

        if (!$this->isActionAvailable($action)) {
            throw new ResourceNotFoundException();
        }

        if (method_exists($this, $method)
            && (empty($this->view->getCaching())
                || !$this->view->isCached('sitemap/sitemap.tpl', $cacheId))
        ) {
            $this->{$method}();
        }

        return $this->getResponse($format, $action, $cacheId);
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
     * Generates and assigns the information for the tag sitemap to template.
     */
    protected function generateTagSitemap()
    {
        $sql = 'SELECT DISTINCT(slug) FROM tags'
            . ' WHERE slug REGEXP "^[a-zA-Z0-9]{1}.{1,29}$"'
            . ' ORDER BY slug ASC';

        $tags = $this->get('orm.connection.instance')->fetchAll($sql);
        $tags = array_map(function ($a) {
            return $a['slug'];
        }, $tags);

        $this->view->assign([ 'tags' => $tags ]);
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
     * Returns the list of contents basing on a criteria.
     *
     * @param array   $criteria The criteria to search by.
     * @param integer $limit    The maximum number of contents to return.
     *
     * @return Array The list of contents
     */
    public function getContents($criteria = [], $limit = 100)
    {
        $types = [];

        if ($this->get('core.security')->hasExtension('ARTICLE_MANAGER')) {
            $types[] = [ 'value' => 'article' ];
        }

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER')) {
            $types[] = [ 'value' => 'opinion' ];
        }

        // Set search filters
        $filters = [
            'content_type_name' => array_merge([
                'union' => 'OR',
            ], $types),
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
     * Generates a response basing on the format and the action.
     *
     * @param string $format  Whether compress the sitemap or not
     * @param string $action  The type of sitemap
     * @param string $cacheId The template cache id.
     *
     * @return Response The response object.
     */
    protected function getResponse($format, $action, $cacheId)
    {
        $headers  = [ 'Content-Type' => 'application/xml; charset=utf-8' ];
        $instance = $this->get('core.instance')->internal_name;

        $contents = $this->renderView('sitemap/sitemap.tpl', [
            'action'   => $action,
            'cache_id' => $cacheId
        ]);

        if ($format === 'xml.gz') {
            $contents = gzencode($contents, 9);
            $headers  = [
                'Content-Type'        => 'application/x-gzip',
                'Content-Length'      => strlen($contents),
                'Content-Disposition' => 'attachment; filename="sitemap'
                    . $action . '.xml.gz"'
            ];
        }

        $headers = array_merge($headers, [
            'x-cache-for' => self::EXPIRE[$action],
            'x-cacheable' => true,
            'x-instance'  => $instance,
            'x-tags'      => sprintf('instance-%s,sitemap,%s', $instance, $action)
        ]);

        return new Response($contents, 200, $headers);
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

    /**
     * Checks if the action is available basing on the current activated
     * extensions.
     *
     * @param string $action The action name.
     *
     * @return boolean True if the action is avaiable. False otherwise.
     */
    protected function isActionAvailable($action)
    {
        if ($action === 'index'
            || !array_key_exists($action, self::EXTENSIONS)
        ) {
            return true;
        }

        $available = false;

        foreach (self::EXTENSIONS[$action] as $extension) {
            if ($this->get('core.security')->hasExtension($extension)) {
                $available = true;
            }
        }

        return $available;
    }
}
