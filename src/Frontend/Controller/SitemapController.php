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
        'latest'  => '1h',
        'tag'   => '1d',
        'video' => '1h',
        'article'   => '1h',
        'opinion'   => '1h',
        'album'   => '1h',
        'letter'   => '1h',
        'poll'   => '1h',
        'kiosko'   => '1h',
        'event'   => '1h',
    ];

    /**
     * The list of needed extensions per action.
     *
     * @var array
     */
    const EXTENSIONS = [
        'album'   => [ 'ALBUM_MANAGER' ],
        'article'   => [ 'ARTICLE_MANAGER' ],
        'event'   => [ 'EVENT_MANAGER' ],
        'image' => [ 'IMAGE_MANAGER' ],
        'kiosko'   => [ 'KIOSKO_MANAGER' ],
        'latest'  => [
                    'ALBUM_MANAGER', 'ARTICLE_MANAGER',
                    'EVENT_MANAGER', 'IMAGE_MANAGER',
                    'KIOSKO_MANAGER', 'LATEST_MANAGER' ,
                    'LETTER_MANAGER','OPINION_MANAGER',
                    'POLL_MANAGER', 'VIDEO_MANAGER',
                ],
        'letter'   => [ 'LETTER_MANAGER' ],
        'opinion'   => [ 'OPINION_MANAGER' ],
        'poll'   => [ 'POLL_MANAGER' ],
        'tag'   => [ 'TAG_MANAGER' ],
        'video' => [ 'VIDEO_MANAGER' ],
    ];

    /**
     * Renders the index sitemap
     *
     * @param string $action The sitemap action.
     * @param string $format The sitemap format.
     *
     * @return Response the response object
     */
    public function indexAction($format, $action = 'index', $page = null, $letter = '')
    {
        $cacheId       = $this->view->getCacheId('sitemap', $action);
        $contentsCount = [];
        $perPage       = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('sitemap')['perpage'] ?? 50;

        $this->view->setConfig('sitemap');

        if (!$this->isActionAvailable($action)) {
            throw new ResourceNotFoundException();
        }

        if ($action != 'index'
            && (empty($this->view->getCaching())
                || !$this->view->isCached('sitemap/sitemap.tpl', $cacheId))
        ) {
            $this->getAction($action, $perPage, $page, $letter);
        } else {
            $contentsCount = $this->getContentsCount($perPage);
            $page          = 1;
        }

        return $this->getResponse($format, $action, $cacheId, $contentsCount, $page);
    }

    /**
     * Generates and assigns the information for the actions sitemap to template.
     */
    protected function getAction($action, $perPage, $page, $letter)
    {
        switch ($action) {
            case 'latest':
                $this->setSitemap([], [], null, null);
                break;
            case 'tag':
                $this->generateTagSitemap($letter, $perPage, $page);
                break;
            case 'image':
                $this->setSitemap([], [[ 'value' => 'photo' ]], $perPage, $page);
                break;
            default:
                $this->setSitemap([], [[ 'value' => $action ]], $perPage, $page);
        }
    }

    /**
     * Generates and assigns the information for the tag sitemap to template.
     */
    protected function generateTagSitemap($letter, $perPage, $page)
    {
        $sql = 'SELECT DISTINCT(slug) FROM tags'
            . ' WHERE slug LIKE "'
            . $letter
            . '%"'
            . ' ORDER BY slug ASC'
            . ' LIMIT '
            . $perPage
            . ' OFFSET '
            . $perPage * ($page - 1);

        $tags = $this->get('orm.connection.instance')->fetchAll($sql);
        $tags = array_map(function ($a) {
            return $a['slug'];
        }, $tags);

        $this->view->assign([ 'tags' => $tags ]);
    }

    /**
     * Returns the list of contents basing on a criteria.
     *
     * @param array   $criteria The criteria to search by.
     * @param integer $limit    The maximum number of contents to return.
     *
     * @return Array The list of contents
     */
    public function getContents($criteria = [], $types = [], $limit = 100, $page = 1)
    {
        if (empty($types)) {
            $types = $this->getTypes();
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
        $contents = $em->findBy($filters, ['created' => 'desc'], $limit, $page);

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
     * Returns the count of contents for index listing
     *
     * @param integer $perPage    Items per page
     *
     * @return Array The count of contents
     */
    protected function getContentsCount($perPage)
    {
        $em       = $this->get('entity_repository');

        $filters = [
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

        $counts = [];

        if ($this->get('core.security')->hasExtension('ALBUM_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'album' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['ALBUM_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('ARTICLE_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'article' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['ARTICLE_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('IMAGE_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'photo' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['IMAGE_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('EVENT_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'event' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['EVENT_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'kiosko' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['KIOSKO_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('LETTER_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'letter' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['LETTER_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'opinion' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['OPINION_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('POLL_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'poll' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['POLL_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('VIDEO_MANAGER')) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'video' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $counts['VIDEO_MANAGER'] = ceil($em->countBy($filters) / $perPage);
        }

        if ($this->get('core.security')->hasExtension('TAG_MANAGER')) {
            $sql     = ' SELECT DISTINCT SUBSTR(slug,1,1) as letter FROM `tags` ORDER BY SUBSTR(slug,1,1)';
            $letters = $this->get('orm.connection.instance')->fetchAll($sql);

            foreach ($letters as $letter) {
                $char = $letter['letter'];
                $sql = 'SELECT COUNT(id) as counter FROM tags'
                . ' WHERE slug LIKE "' . $char . '%"';

                $tagsCount = $this->get('orm.connection.instance')->fetchAll($sql)[0]['counter'];

                $counts['TAG_MANAGER'][$char] = ceil($tagsCount / $perPage);
            }
        }

        return $counts;
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
    protected function getResponse($format, $action, $cacheId, $contentsCount = [], $page = null)
    {
        $headers  = [ 'Content-Type' => 'application/xml; charset=utf-8' ];
        $contents = $this->get('core.template.frontend')
            ->render('sitemap/sitemap.tpl', [
                'action'   => $action,
                'cache_id' => $cacheId,
                'counters' => $contentsCount,
                'page'     => $page
            ]);

        if ($format === 'xml.gz') {
            $contents = gzencode($contents, 9);
            $headers  = [
                'Content-Type'        => 'application/x-gzip',
                'Content-Length'      => strlen($contents),
                'Content-Disposition' => 'attachment; filename="sitemap.'
                    . $action . '.xml.gz"'
            ];
        }

        $headers = array_merge($headers, [
            'x-cache-for' => self::EXPIRE[$action],
            'x-cacheable' => true,
            'x-tags'      => sprintf('sitemap,%s', $action),
            'x-cacheable' => true,
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
     *  Method for recover get types
     *
     * @return Array The list of types
     */
    protected function getTypes()
    {
        $types = [];

        if ($this->get('core.security')->hasExtension('ALBUM_MANAGER')) {
            $types[] = [ 'value' => 'album' ];
        }

        if ($this->get('core.security')->hasExtension('ARTICLE_MANAGER')) {
            $types[] = [ 'value' => 'article' ];
        }

        if ($this->get('core.security')->hasExtension('EVENT_MANAGER')) {
            $types[] = [ 'value' => 'event' ];
        }

        if ($this->get('core.security')->hasExtension('IMAGE_MANAGER')) {
            $types[] = [ 'value' => 'photo' ];
        }

        if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER')) {
            $types[] = [ 'value' => 'kiosko' ];
        }

        if ($this->get('core.security')->hasExtension('LETTER_MANAGER')) {
            $types[] = [ 'value' => 'letter' ];
        }

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER')) {
            $types[] = [ 'value' => 'opinion' ];
        }

        if ($this->get('core.security')->hasExtension('POLL_MANAGER')) {
            $types[] = [ 'value' => 'poll' ];
        }

        if ($this->get('core.security')->hasExtension('VIDEO_MANAGER')) {
            $types[] = [ 'value' => 'video' ];
        }

        return $types;
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

    /**
     *  Method for set sitemap of similar content
     */
    protected function setSitemap($criteria = [], $types = [], $limit = 100, $page = 1)
    {
        if (is_null($page) || is_null($limit)) {
            $limit = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('sitemap')['total'] ?? 100;
            $pag   = 1;
        }

        $tags     = [];
        $contents = $this->getContents($criteria, $types, $limit, $page);

        foreach ($contents as &$content) {
            if (!empty($content->tags)) {
                $tags = array_merge($content->tags, $tags);
            }
        }

        $this->view->assign([
            'contents'   => $contents,
            'googleNews' => $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('google_news_name'),
            'tags'       => $this->getTags($tags)
        ]);
    }
}
