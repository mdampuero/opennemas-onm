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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
        'image'        => '1h',
        'index'        => '1d',
        'latest'       => '1h',
        'tag'          => '1d',
        'video'        => '1h',
        'article'      => '1h',
        'opinion'      => '1h',
        'album'        => '1h',
        'letter'       => '1h',
        'poll'         => '1h',
        'kiosko'       => '1h',
        'event'        => '1h',
        'categories'   => '1h',
    ];

    /**
     * The list of needed extensions per action.
     *
     * @const array
     */
    const EXTENSIONS = [
        'album'   => [ 'ALBUM_MANAGER' ],
        'article' => [ 'ARTICLE_MANAGER' ],
        'event'   => [ 'EVENT_MANAGER' ],
        'image'   => [ 'IMAGE_MANAGER' ],
        'kiosko'  => [ 'KIOSKO_MANAGER' ],
        'latest'  => [
                    'ALBUM_MANAGER', 'ARTICLE_MANAGER',
                    'EVENT_MANAGER', 'IMAGE_MANAGER',
                    'KIOSKO_MANAGER', 'LATEST_MANAGER' ,
                    'LETTER_MANAGER','OPINION_MANAGER',
                    'POLL_MANAGER', 'VIDEO_MANAGER',
                   ],
        'letter'  => [ 'LETTER_MANAGER' ],
        'opinion' => [ 'OPINION_MANAGER' ],
        'poll'    => [ 'POLL_MANAGER' ],
        'tag'     => [ 'TAG_MANAGER' ],
        'video'   => [ 'VIDEO_MANAGER' ],
    ];

    /**
     * Renders the index sitemap
     *
     * @param string  $format The sitemap format.
     * @param string  $action The sitemap action.
     * @param integer $page The actual page.
     * @param string  $letter The actual tag letter
     * @param integer $year The sitemap year.
     * @param integer $format The sitemap month.
     *
     * @return Response the response object
     */
    public function indexAction($format, $action = 'index', $page = null, $letter = '', $year = null, $month = null)
    {
        $cacheId       = $this->view->getCacheId('sitemap', $action);
        $contentsCount = [];

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('sitemap');

        if (empty($settings)) {
            $settings = getService('orm.manager')
                ->getDataSet('Settings', 'manager')
                ->get('sitemap');
        }

        $this->view->setConfig('sitemap');

        if (!$this->isActionAvailable($action)) {
            throw new ResourceNotFoundException();
        }

        if ($action != 'index'
            && (empty($this->view->getCaching())
                || !$this->view->isCached('sitemap/sitemap.tpl', $cacheId))
        ) {
            $this->setAction($action, $settings, $page, $letter, $year, $month);
        } else {
            if (!empty($year) && !empty($month)) {
                $contentsCount = $this->getContentsCount($settings, $year, $month, $settings);
            } else {
                $contentsCount = $this->getYears();
            }

            $page = 1;
        }

        return $this->getResponse($settings, $format, $action, $cacheId, $contentsCount, $page, $year, $month);
    }

    /**
     * Generates and assigns the information for the tag sitemap to template.
     *
     * @param string $letter The actual tag letter
     * @param string $settins The sitemap settings.
     * @param int    $page The actual page.
     *
     * @return Response the response object
     */
    protected function generateTagSitemap($letter, $settings, $page)
    {
        if (empty($page)) {
            $page = 1;
        }

        $sql = 'SELECT DISTINCT(slug) FROM tags'
            . ' WHERE slug LIKE "'
            . $lettercle
            . '%"'
            . ' ORDER BY slug ASC'
            . ' LIMIT '
            . $settings['perpage']
            . ' OFFSET '
            . $settings['perpage'] * ($page - 1);

        $tags = $this->get('orm.connection.instance')->fetchAll($sql);
        $tags = array_map(function ($a) {
            return $a['slug'];
        }, $tags);

        $this->view->assign([ 'tags' => $tags ]);
    }

    /**
     * Returns the list of contents basing on a criteria.
     *
     * @param integer $year The sitemap year to search by.
     * @param integer $month The sitemap month to search by.
     * @param array   $criteria The criteria to search by.
     * @param array   $types The types to search by.
     * @param integer $page The page to search by.
     *
     * @return Array The list of contents
     */
    public function getContents($limit, $year, $month, $settings, $criteria = [], $types = [], $page = 1)
    {
        if (empty($types)) {
            $types = $this->getTypes($settings);
        }

        if (!empty($criteria)) {
            $filters = $criteria;
        } else {
            // Set search filters
            $filters = [
                'content_type_name' => array_merge([
                    'union' => 'OR',
                ], $types),
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'changed'         => [
                    [ 'value' => $year . '-' . $month . '%', 'operator' => 'LIKE' ],
                ]
            ];
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
     * @param integer $settings The sitemap settings
     * @param integer $year The sitemap year
     * @param integer $month The sitemap month
     *
     * @return Array The count of contents
     */
    protected function getContentsCount($settings, $year, $month)
    {
        $em = $this->get('entity_repository');

        $filters = [
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'changed'         => [
                [ 'value' => $year . '-' . $month . '%', 'operator' => 'LIKE' ],
            ]
        ];

        $counts = [];

        if ($this->get('core.security')->hasExtension('ALBUM_MANAGER') && $settings['album']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'album' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['ALBUM_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('ARTICLE_MANAGER') && $settings['articles']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'article' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['ARTICLE_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('IMAGE_MANAGER') && $settings['images']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'photo' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['IMAGE_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('EVENT_MANAGER') && $settings['events']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'event' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['EVENT_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER') && $settings['kiosko']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'kiosko' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['KIOSKO_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('LETTER_MANAGER') && $settings['letters']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'letter' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['LETTER_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER') && $settings['opinions']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'opinion' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['OPINION_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('POLL_MANAGER') && $settings['polls']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'poll' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['POLL_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('VIDEO_MANAGER') && $settings['videos']) {
            $contentFilter = [  'content_type_name'    => [[ 'value' => 'video' ]] ];
            $filters       = array_merge($filters, $contentFilter);
            $pages         = ceil($em->countBy($filters) / $settings['perpage']);

            $counts['VIDEO_MANAGER']['count'] = $pages;
        }

        if ($this->get('core.security')->hasExtension('TAG_MANAGER') && $settings['tags']) {
            $sql     = ' SELECT DISTINCT SUBSTR(CAST(CONVERT(slug USING utf8) as binary),1,1) as letter
                        FROM `tags` ORDER BY SUBSTR(CAST(CONVERT(slug USING utf8) as binary),1,1) ';
            $letters = $this->get('orm.connection.instance')->fetchAll($sql);

            foreach ($letters as $letter) {
                if (ctype_graph($letter['letter'])) {
                    $char = $letter['letter'];

                    $sql = 'SELECT COUNT(id) as counter FROM tags'
                    . ' WHERE slug LIKE "' . $char . '%"';

                    $tagsCount = $this->get('orm.connection.instance')->fetchAll($sql)[0]['counter'];

                    $counts['TAG_MANAGER'][$char] = ceil($tagsCount / $settings['perpage']);
                }
            }
        }

        return $counts;
    }

    /**
     * Generates a response basing on the format and the action.
     *
     * @param string $settings The sitemap settings.
     * @param string $format  Whether compress the sitemap or not.
     * @param string $action The type of sitemap.
     * @param string $cacheId The template cache id.
     * @param array  $contentsCount The array with de contents count.
     * @param integer $page The sitemap page.
     * @param integer $year The sitemap year.
     * @param integer $month The sitemap month.
     *
     * @return Response The response object.
     */
    protected function getResponse(
        $settings,
        $format,
        $action,
        $cacheId,
        $contentsCount = [],
        $page = null,
        $year = null,
        $month = null
    ) {
        $headers  = [ 'Content-Type' => 'application/xml; charset=utf-8' ];
        $contents = $this->get('core.template.frontend')
            ->render('sitemap/sitemap.tpl', [
                'action'   => $action,
                'cache_id' => $cacheId,
                'counters' => $contentsCount,
                'page'     => $page,
                'sitemap'  => $settings,
                'year'     => $year,
                'month'    => $month
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
     * @param array $settings The sitemap settins
     *
     * @return Array The list of types
     */
    protected function getTypes($settings)
    {
        $types = [];

        if ($this->get('core.security')->hasExtension('ALBUM_MANAGER') && $settings['album']) {
            $types[] = [ 'value' => 'album' ];
        }

        if ($this->get('core.security')->hasExtension('ARTICLE_MANAGER') && $settings['articles']) {
            $types[] = [ 'value' => 'article' ];
        }

        if ($this->get('core.security')->hasExtension('EVENT_MANAGER') && $settings['events']) {
            $types[] = [ 'value' => 'event' ];
        }

        if ($this->get('core.security')->hasExtension('IMAGE_MANAGER') && $settings['images']) {
            $types[] = [ 'value' => 'photo' ];
        }

        if ($this->get('core.security')->hasExtension('KIOSKO_MANAGER') && $settings['kiosko']) {
            $types[] = [ 'value' => 'kiosko' ];
        }

        if ($this->get('core.security')->hasExtension('LETTER_MANAGER') && $settings['letters']) {
            $types[] = [ 'value' => 'letter' ];
        }

        if ($this->get('core.security')->hasExtension('OPINION_MANAGER') && $settings['opinions']) {
            $types[] = [ 'value' => 'opinion' ];
        }

        if ($this->get('core.security')->hasExtension('POLL_MANAGER') && $settings['polls']) {
            $types[] = [ 'value' => 'poll' ];
        }

        if ($this->get('core.security')->hasExtension('VIDEO_MANAGER') && $settings['videos']) {
            $types[] = [ 'value' => 'video' ];
        }

        return $types;
    }

        /**
     * Returns the list of year/month
     *
     *
     * @return Array The count of contents
     */
    protected function getYears()
    {
        $years = [];

        $sql = 'SELECT DISTINCT DATE_FORMAT(`changed`, "%Y-%m") as `dates`
            FROM `contents` ORDER BY `dates` ASC';

        $result = $this->get('orm.connection.instance')->fetchAll($sql);

        foreach ($result as $value) {
            $aux = explode('-', $value['dates']);

            if (count($aux) == 2) {
                $currentMonth = date('m');
                $currentYear  = date('Y');

                if ($aux[0] == $currentYear && $aux[1] == $currentMonth) {
                    $lastMod = date('Y-m-d H:i:s');
                } else {
                    $lastMod = date('Y-m-t 23:59:59', strtotime($aux[0] . '-' . $aux[1]));
                }

                $years[$aux[0]][$aux[1]] = $lastMod;
            }
        }

        return $years;
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
     * Generates and assigns the information for the actions sitemap to template.
     *
     * @param string  $action The sitemap action.
     * @param array   $settings The sitemap settings.
     * @param integer $page The actual page.
     * @param string  $letter The actual tag letter
     * @param integer $year The sitemap year.
     * @param integer $format The sitemap month.
     *
     */

    protected function setAction($action, $settings, $page, $letter, $year, $month)
    {
        switch ($action) {
            case 'latest':
                $criteria = [
                    'content_type_name' => array_merge([
                        'union' => 'OR',
                    ], $this->getTypes($settings)),
                    'content_status'    => [[ 'value' => 1 ]],
                    'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]]
                ];

                $this->setSitemap($year, $month, $settings, $criteria);
                break;
            case 'tag':
                $this->generateTagSitemap($letter, $settings, $page);
                break;
            case 'image':
                $this->setSitemap($year, $month, $settings, $page, [], [[ 'value' => 'photo' ]]);
                break;
            case 'categories':
                break;
            default:
                $this->setSitemap($year, $month, $settings, $page, [], [[ 'value' => $action ]]);
        }
    }

    /**
     *  Method for set sitemap of similar content
     *
     * @param integer $year The sitemap year.
     * @param integer $month The sitemap month.
     * @param array $settings The sitemap settins.
     * @param array $criteria The criteria to search by.
     * @param array $types The types to seearch by.
     * @param integer $page The sitemap page.
     *
     * @return boolean True if the action is avaiable. False otherwise.
     */
    protected function setSitemap($year, $month, $settings, $page, $criteria = [], $types = [])
    {
        $tags     = [];

        if (empty($page)) {
            $page  = 1;
            $limit = $settings['total'];
        } else {
            $limit = $settings['perpage'];
        }

        $contents = $this->getContents($limit, $year, $month, $settings, $criteria, $types, $page);

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
