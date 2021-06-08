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
        'categories'   => '1h',
        'contents'     => '1h',
        'index'        => '1d',
        'news'         => '1h',
        'subindex'     => '1d',
        'tag'          => '1d',
        'tagIndex'     => '1d',
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
        'contents'   => 'sitemap/content.tpl',
        'news'       => 'sitemap/content.tpl',
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
        $contents = [];
        $settings = $this->getSettings();

        if ($this->get('core.security')->hasExtension('TAG_MANAGER')) {
            $letters = $this->get('orm.connection.instance')
                ->fetchAll(
                    'SELECT DISTINCT SUBSTRING(`slug`, 1, 1) as "letter"' .
                    'FROM `tags` WHERE `slug` IS NOT NULL'
                );
        }

        $result = $this->get('orm.connection.instance')->fetchAll(
            'SELECT DISTINCT DATE_FORMAT(`changed`, "%Y-%m") as `dates`
            FROM `contents` ORDER BY `dates` ASC'
        );

        foreach ($result as $value) {
            if (empty($value['dates'])) {
                continue;
            }

            $aux = explode('-', $value['dates']);

            $contents[$aux[0]][$aux[1]] = $value === date("Y-m")
                ? date('Y-m-d H:i:s')
                : date('Y-m-t 23:59:59', strtotime($aux[0] . '-' . $aux[1]));
        }

        return $this->getResponse($settings, $format, 'index', [ 'letters' => $letters, 'contents' => $contents ]);
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
        $settings = $this->getSettings();

        try {
            $categories = $this->get('api.service.category')->getList(
                sprintf('enabled = 1 limit %d', $settings['total'])
            )['items'];
        } catch (GetListException $e) {
            $categories = [];
        }

        return $this->getResponse($settings, $format, 'categories', $categories);
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

        return $this->getResponse($settings, $format, 'news', $contents);
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

        return $this->getResponse($settings, $format, 'subindex', $contents, null, $year, $month);
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
            ]
        ];

        $contents = $em->findBy($filters, ['created' => 'desc'], $settings['perpage'], $page);

        return $this->getResponse($settings, $format, 'contents', $contents, null, $year, $month, $googleNews);
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
        $settings = $this->getSettings();

        $sql = 'SELECT DISTINCT(slug) FROM tags WHERE slug LIKE "'
            . $letter . '%" ORDER BY slug ASC';

        $number = ceil(count($this->get('orm.connection.instance')->fetchAll($sql)) / $settings['perpage']);

        return $this->getResponse($settings, $format, 'tagIndex', [ 'tag' => $number ]);
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
        $settings = $this->getSettings();

        try {
            $tags = $this->get('api.service.tag')->getListBySql(
                sprintf(
                    'SELECT * FROM tags WHERE slug LIKE "%s%%" ' .
                    'LIMIT %s OFFSET %s',
                    $letter,
                    $settings['perpage'],
                    $settings['perpage'] * ($page - 1)
                )
            )['items'];
        } catch (GetListException $e) {
            return $this->getResponse($settings, $format, 'tag', []);
        }

        return $this->getResponse($settings, $format, 'tag', $tags);
    }

    protected function getSettings()
    {
        return $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('sitemap')
            ?? getService('orm.manager')->getDataSet('Settings', 'manager')->get('sitemap');
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
        $contentsCount = [],
        $page = null,
        $year = null,
        $month = null,
        $googleNews = null
    ) {
        $headers  = [ 'Content-Type' => 'application/xml; charset=utf-8' ];
        $contents = $this->get('core.template.frontend')
            ->render(self::TEMPLATES[$action], [
                'action'     => $action,
                'counters'   => $contentsCount,
                'page'       => $page,
                'sitemap'    => $settings,
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
     *  Method for recover get types
     *
     * @param array $settings The sitemap settins
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
}
