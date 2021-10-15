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
        'categories' => '1d',
        'authors'    => '1d',
        'contents'   => '1h',
        'index'      => '1d',
        'news'       => '1h',
        'tag'        => '1h',
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
        'tag'        => 'sitemap/tag.tpl'
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
        $helper   = $this->get('core.helper.sitemap');
        $settings = $helper->getSettings();
        $letters  = [];

        if (!$this->isCached('index', $cacheId)) {
            $contents = [];

            if ($settings['tag']) {
                $letters = $helper->getTags();
            }

            $dates = $helper
                ->getDates($helper->getTypes($settings, [ 'tag' ], true));

            $types = $helper->getTypes($settings, [ 'tag' ]);

            if (empty($dates)) {
                return $this->getResponse($format, $cacheId, 'index', [ 'letters' => $letters ]);
            }

            foreach ($dates as $date) {
                if (empty($date)) {
                    continue;
                }

                list($year, $month) = explode('-', $date);

                $contents[] = [
                    'year'  => $year,
                    'month' => $month,
                    'pages' => ceil(
                        $helper->getContents($date, $types) / $settings['perpage']
                    )
                ];
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
            $settings = $this->get('core.helper.sitemap')->getSettings();
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
                ],
                'changed' => [
                    [
                        'value' => sprintf(
                            '"%s" AND DATE_ADD("%s", INTERVAL 1 MONTH)',
                            date('Y-m-01 00:00:00', strtotime(date('Y-m-d H:i:s'))),
                            date('Y-m-01 00:00:00', strtotime(date('Y-m-d H:i:s')))
                        ),
                        'field' => true,
                        'operator' => 'BETWEEN'
                    ]
                ]
            ];

            $contents = $this->get('entity_repository')
                ->findBy($filters, ['changed' => 'desc'], $settings['total']);

            return $this->getResponse(
                $format,
                $cacheId,
                'news',
                $contents,
                null,
                null,
                null,
                null,
                'no-store'
            );
        }

        return $this->getResponse(
            $format,
            $cacheId,
            'news',
            [],
            null,
            null,
            null,
            null,
            'no-store'
        );
    }

    /**
     * Returns the content sitemap.
     *
     * @param integer $year   The year of the content.
     * @param integer $month  The month of the content.
     * @param integer $page   The page of the contents.
     * @param string  $format The format to get the response.
     *
     * @return Response The sitemap for the contents.
     */
    public function contentsAction($year, $month, $page, $format)
    {
        // TODO: Remove this as soon as possible
        $helper   = $this->get('core.helper.sitemap');
        $settings = $helper->getSettings();

        if (empty(array_filter([$year, $month, $page]))) {
            $dates              = $helper->getDates();
            $last               = array_pop($dates);
            list($year, $month) = explode('-', $last);
            $page               = ceil(
                $helper->getContents($last, $helper->getTypes($settings)) / $settings['perpage']
            );
        }

        $cacheId = $this->view->getCacheId('sitemap', 'contents', $year, $month, $page);

        $path = sprintf(
            '%s/%s/sitemap.%d.%s.%d.xml.gz',
            $this->getParameter('core.paths.cache'),
            $this->get('core.instance')->getSitemapShortPath(),
            $year,
            str_pad($month, 2, "0", STR_PAD_LEFT),
            $page
        );

        if (file_exists($path)) {
            return $this->getResponse($format, $cacheId, 'contents', [], $path, $page, $year, $month);
        }

        $date     = $year . '-' . $month;
        $types    = $helper->getTypes($settings, [ 'tag' ]);
        $contents = $helper
            ->getContents($date, $types, $settings['perpage'], $page);

        if ($date === date('Y-m')) {
            $path = null;
        }

        return $this->getResponse(
            $format,
            $cacheId,
            'contents',
            $contents,
            $path,
            $page,
            $year,
            $month
        );
    }

    /**
     * Redirects to the new contents action.
     */
    public function oldContentsAction()
    {
        $helper             = $this->get('core.helper.sitemap');
        $settings           = $helper->getSettings();
        $dates              = $helper->getDates();
        $last               = array_pop($dates);
        list($year, $month) = explode('-', $last);
        $page               = ceil(
            $helper->getContents($last, $helper->getTypes($settings)) / $settings['perpage']
        );

        return $this->redirect(
            $this->generateUrl(
                'frontend_contents_sitemap',
                ['format' => 'xml.gz', 'page' => $page, 'month' => $month, 'year' => $year]
            ),
            301
        );
    }

    /**
     * Redirects to the news action.
     */
    public function oldNewsAction($format)
    {
        return $this->redirect(
            $this->generateUrl(
                'frontend_news_sitemap',
                [ 'format' => $format ]
            ),
            301
        );
    }

    /**
     * Generates the tags sitemap.
     *
     * @param integer $page The current page.
     *
     * @return Response the sitemap with the tags of the current page.
     */
    public function tagAction($letter, $format)
    {
        $letter = html_entity_decode($letter, ENT_XML1, 'UTF-8');

        $cacheId = $this->view->getCacheId('sitemap', 'tag', $letter);
        $tags    = [];

        if (!$this->isCached('tag', $cacheId)) {
            try {
                $tags = $this->get('api.service.tag')->getListBySql(
                    sprintf(
                        'SELECT * FROM tags WHERE slug LIKE "%s%%" ',
                        preg_replace(
                            ['/"/', '/_/'],
                            ['\"', '\\_'],
                            $letter
                        )
                    )
                )['items'];
            } catch (GetListException $e) {
            }

            return $this->getResponse($format, $cacheId, 'tag', $tags);
        }

        return $this->getResponse($format, $cacheId, 'tag');
    }

    /**
     * Redirects to the new tag action.
     */
    public function oldTagAction($format)
    {
        $tags   = $this->get('core.helper.sitemap')->getTags();
        $letter = array_shift($tags)['letter'];

        return $this->redirect(
            $this->generateUrl(
                'frontend_tag_sitemap',
                [ 'letter' => $letter, 'format' => $format ]
            ),
            301
        );
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
        $path = null,
        $page = null,
        $year = null,
        $month = null,
        $cacheControl = ''
    ) {
        $headers = [
            'Content-Type' => 'application/xml; charset=utf-8',
            'x-cache-for' => self::EXPIRE[$action],
            'x-cacheable' => true,
            'x-tags'      => sprintf('sitemap,%s', $action)
        ];

        if (!empty($cacheControl)) {
            $headers['Cache-Control'] = $cacheControl;
        }

        $contents = $this->get('core.template.frontend')
            ->render(self::TEMPLATES[$action], [
                'action'     => $action,
                'cache_id'   => $cacheId,
                'counters'   => $contentsCount,
                'page'       => $page,
                'year'       => $year,
                'month'      => $month,
                'googleNews' => $this->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get('google_news_name')
            ]);

        $file = null;
        if (!empty($path)) {
            $length = null;

            if (!file_exists($path)) {
                $length = $this->get('core.helper.sitemap')->saveSitemap($path, $contents);
            }

            $file = !empty($length) ? gzencode($contents, 9) : file_get_contents($path);
        }

        if ($format === 'xml.gz') {
            $filename = implode(".", array_filter([ $action, $year, $month, $page ]));
            $file     = $file ?? gzencode($contents, 9);

            $headers = array_merge($headers, [
                'Content-Type'        => 'application/x-gzip',
                'Content-length'      => strlen($file),
                'Content-Disposition' => sprintf('attachment; filename="sitemap.%s.xml.gz"', $filename)
            ]);

            return new Response($file, 200, $headers);
        }

        return new Response($contents, 200, $headers);
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
