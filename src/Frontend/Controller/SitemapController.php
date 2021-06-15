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

use Api\Exception\GetListException;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        'categories' => '1h',
        'authors'    => '1d',
        'contents'   => '1h',
        'index'      => '1d',
        'news'       => '1h',
        'subindex'   => '1d',
        'tag'        => '1d',
        'tagIndex'   => '1d',
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
        'photo'   => [ 'IMAGE_MANAGER' ],
        'kiosko'  => [ 'KIOSKO_MANAGER' ],
        'letter'  => [ 'LETTER_MANAGER' ],
        'opinion' => [ 'OPINION_MANAGER' ],
        'poll'    => [ 'POLL_MANAGER' ],
        'tag'     => [ 'TAG_MANAGER' ],
        'video'   => [ 'VIDEO_MANAGER' ],
    ];

    /**
     * The list of templates per action.
     *
     * @const array
     */
    const TEMPLATES = [
        'index'      => 'sitemap/index.tpl',
        'categories' => 'sitemap/categories.tpl',
        'authors'    => 'sitemap/authors.tpl',
        'contents'   => 'sitemap/content.tpl',
        'news'       => 'sitemap/news.tpl',
        'subindex'   => 'sitemap/subindex.tpl',
        'tag'        => 'sitemap/tag.tpl',
        'tagIndex'   => 'sitemap/subindex.tpl'
    ];

    /**
     * Renders the index sitemap
     *
     * @param string  $format The sitemap format.
     *
     * @return Response the response object
     */
    public function indexAction($format)
    {
        $cacheId  = $this->view->getCacheId('sitemap', 'index');
        $settings = $this->getSettings();
        $letters  = [];

        if (!$this->isCached('index', $cacheId)) {
            $contents = [];

            if ($this->get('core.security')->hasExtension('TAG_MANAGER') && $settings['tag']) {
                $letters = $this->get('orm.connection.instance')
                    ->fetchAll(
                        'SELECT DISTINCT SUBSTR(CAST(CONVERT(slug USING utf8) as binary),1,1) as "letter"' .
                        'FROM `tags` WHERE `slug` IS NOT NULL'
                    );
            }

            $letters = array_filter($letters, function ($a) {
                return ctype_graph($a['letter']);
            });

            $result = $this->get('orm.connection.instance')->fetchAll(
                'SELECT CONCAT(CONVERT(year(changed), NCHAR),\'-\', LPAD(month(changed),2,"0")) as \'dates\''
                . 'FROM `contents` WHERE year(changed) is not null group by dates order by dates'
            );

            foreach ($result as $value) {
                if (empty($value['dates'])) {
                    continue;
                }

                $aux = explode('-', $value['dates']);

                $contents[$aux[0]][$aux[1]] = $value['dates'] === date("Y-m")
                    ? date('Y-m-d H:i:s')
                    : date('Y-m-t 23:59:59', strtotime($aux[0] . '-' . $aux[1]));
            }

            return $this->getResponse($format, $cacheId, 'index', [ 'letters' => $letters, 'contents' => $contents ]);
        }

        return $this->getResponse($format, $cacheId, 'index');
    }

    /**
     * Returns the categories sitemap.
     *
     * @param string $format The format to get the response.
     *
     * @return Response The categories sitemap response.
     */
    public function categoriesAction($format)
    {
        $cacheId    = $this->view->getCacheId('sitemap', 'categories');
        $categories = [];

        if (!$this->isCached('categories', $cacheId)) {
            try {
                $categories = $this->get('api.service.category')->getList(
                    'enabled = 1'
                )['items'];
            } catch (GetListException $e) {
            }
        }

        return $this->getResponse($format, $cacheId, 'categories', $categories);
    }

    /**
     * Returns the author sitemap.
     *
     * @param string $format The format to get the response.
     *
     * @return Response The authors sitemap response.
     */
    public function authorsAction($format)
    {
        $cacheId = $this->view->getCacheId('sitemap', 'authors');
        $authors = [];

        if (!$this->isCached('authors', $cacheId)) {
            try {
                $authors = $this->get('api.service.author')->getList(
                    'activated = 1'
                )['items'];
            } catch (GetListException $e) {
            }
        }

        return $this->getResponse($format, $cacheId, 'authors', $authors);
    }

    /**
     * Returns the news sitemap.
     *
     * @param string $format The format to get the response.
     *
     * @return Response The newss sitemap.
     */
    public function newsAction($format)
    {
        $cacheId = $this->view->getCacheId('sitemap', 'news');

        if (!$this->isCached('news', $cacheId)) {
            $settings = $this->getSettings();
            $filters  = [
                'content_type_name' => [
                    [ 'value' => [ 'article', 'opinion' ], 'operator' => 'IN' ]
                ],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'endtime'           => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ],
                'starttime'         => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ]
            ];

            $contents = $this->get('entity_repository')
                ->findBy($filters, ['created' => 'desc'], $settings['total']);

            return $this->getResponse($format, $cacheId, 'news', $contents);
        }

        return $this->getResponse($format, $cacheId, 'news');
    }

    /**
     * Returns the subindex sitemap.
     *
     * @param string $format The format to get the response.
     *
     * @return Response The subindex sitemap response.
     */
    public function subindexAction($year, $month, $format)
    {
        $cacheId = $this->view->getCacheId('sitemap', 'subindex', $year, $month);

        if (!$this->isCached('subindex', $cacheId)) {
            $settings = $this->getSettings();
            $contents = [];

            $filters = [
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'changed'         => [
                    [ 'value' => $year . '-' . $month . '%', 'operator' => 'LIKE' ],
                ]
            ];

            foreach ($this->getTypes($settings, [ 'tag' ]) as $type) {
                $contentFilter = [ 'content_type_name' => [[ 'value' => $type ]] ];
                $filters       = array_merge($filters, $contentFilter);
                $pages         = ceil($this->get('entity_repository')->countBy($filters) / $settings['perpage']);

                $contents[$type] = $pages;
            }

            return $this->getResponse($format, $cacheId, 'subindex', $contents, null, $year, $month);
        }

        return $this->getResponse($format, $cacheId, 'subindex');
    }

    /**
     * Returns the content sitemap.
     *
     * @param integer $year   The year of the content.
     * @param integer $month  The month of the content.
     * @param string  $action The action to perform.
     * @param integer $page   The page of the contents.
     * @param string  $format The format to get the response.
     *
     * @return Response The sitemap for the contents.
     */
    public function contentsAction($year, $month, $action, $page, $format)
    {
        $cacheId = $this->view->getCacheId('sitemap', $action, $year, $month, $page);

        if (!$this->isCached('contents', $cacheId)) {
            $googleNews = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('google_news_name');

            $em       = $this->get('entity_repository');
            $settings = $this->getSettings();

            $filters = [
                'content_type_name' => [[ 'value' => $action ]],
                'content_status'    => [[ 'value' => 1 ]],
                'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
                'endtime'           => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ],
                'starttime'         => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'changed'         => [
                    [ 'value' => $year . '-' . $month . '%', 'operator' => 'LIKE' ],
                ]
            ];

            $contents = $em->findBy($filters, ['created' => 'desc'], $settings['perpage'], $page);

            return $this->getResponse($format, $cacheId, 'contents', $contents, null, $year, $month, $googleNews);
        }

        return $this->getResponse($format, $cacheId, 'contents');
    }

    /**
     * Generates the subindex for the tags basing on the letter.
     *
     * @param string $letter The first letter of the tags to search.
     *
     * @return Response the response object
     */
    public function tagIndexAction($letter, $format)
    {
        $letter = html_entity_decode($letter, ENT_XML1, 'UTF-8');

        $cacheId = $this->view->getCacheId('sitemap', 'tagIndex', $letter);

        if (!$this->isCached('tagIndex', $cacheId)) {
            $settings = $this->getSettings();

            $sql = 'SELECT DISTINCT(slug) FROM tags WHERE slug LIKE "'
                . preg_replace(
                    ['/"/', '/_/'],
                    ['\"', '\\_'],
                    $letter
                ) . '%" ORDER BY slug ASC';

            $number = ceil(count($this->get('orm.connection.instance')->fetchAll($sql)) / $settings['perpage']);

            return $this->getResponse(
                $format,
                $cacheId,
                'tagIndex',
                [ 'tag' => $number ],
                null,
                null,
                null,
                null,
                $letter
            );
        }

        return $this->getResponse($format, $cacheId, 'tagIndex');
    }

    /**
     * Generates the tags sitemap.
     *
     * @param integer $page The current page.
     *
     * @return Response the sitemap with the tags of the current page.
     */
    public function tagAction($letter, $page, $format)
    {
        $letter = html_entity_decode($letter, ENT_XML1, 'UTF-8');

        $cacheId = $this->view->getCacheId('sitemap', 'tag', $letter, $page);
        $tags    = [];

        if (!$this->isCached('tag', $cacheId)) {
            $settings = $this->getSettings();

            try {
                $tags = $this->get('api.service.tag')->getListBySql(
                    sprintf(
                        'SELECT * FROM tags WHERE slug LIKE "%s%%" ' .
                        'LIMIT %s OFFSET %s',
                        preg_replace(
                            ['/"/', '/_/'],
                            ['\"', '\\_'],
                            $letter
                        ),
                        $settings['perpage'],
                        $settings['perpage'] * ($page - 1)
                    )
                )['items'];
            } catch (GetListException $e) {
            }

            return $this->getResponse($format, $cacheId, 'tag', $tags);
        }

        return $this->getResponse($format, $cacheId, 'tag');
    }

    protected function getSettings()
    {
        return $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('sitemap')
            ?? $this->get('orm.manager')->getDataSet('Settings', 'manager')->get('sitemap');
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
        $format,
        $cacheId,
        $action,
        $contentsCount = [],
        $page = null,
        $year = null,
        $month = null,
        $googleNews = null,
        $letter = null
    ) {
        $headers  = [ 'Content-Type' => 'application/xml; charset=utf-8' ];
        $contents = $this->get('core.template.frontend')
            ->render(self::TEMPLATES[$action], [
                'action'     => $action,
                'cache_id'   => $cacheId,
                'counters'   => $contentsCount,
                'letter'     => $letter,
                'page'       => $page,
                'year'       => $year,
                'month'      => $month,
                'googleNews' => $googleNews
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
     * Method for recover get types
     *
     * @param array $settings The sitemap settings
     * @param array $ommit    An array of types to ommit.
     *
     * @return Array The list of types
     */
    protected function getTypes($settings, $ommit = [])
    {
        return array_keys(array_filter(self::EXTENSIONS, function ($value, $key) use ($settings, $ommit) {
            return !in_array($key, $ommit)
                && array_key_exists($key, $settings)
                && !empty($settings[$key])
                && $this->get('core.security')->hasExtension($value);
        }, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Returns true if there is a valid cache.
     *
     * @param string $action The action to check.
     *
     * @return string True if the content is cached false otherwise.
     */
    protected function isCached($action, $cacheId)
    {
        $this->view->setConfig('sitemap');

        return !empty($this->view->getCaching() && $this->view->isCached(self::TEMPLATES[$action], $cacheId));
    }
}
