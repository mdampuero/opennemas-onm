<?php

namespace Frontend\Controller;

use Api\Exception\GetItemException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RssController extends FrontendController
{
    /**
     * Returns a list of allowed content types.
     *
     * @return array The list of content types.
     */
    protected function checkExtensions()
    {
        $cs = $this->container->get('core.security');

        $contentTypes = [];
        if ($cs->hasExtension('ARTICLE_MANAGER')) {
            $contentTypes ['Articles'] = ['slug' => 'article', 'name' => 'Articles'];
        }
        if ($cs->hasExtension('OPINION_MANAGER')) {
            $contentTypes ['Opinions'] = ['slug' => 'opinion', 'name' => 'Opinions'];
        }
        if ($cs->hasExtension('VIDEO_MANAGER')) {
            $contentTypes ['Videos'] = ['slug' => 'video','name' => 'Videos'];
        }
        if ($cs->hasExtension('ALBUM_MANAGER')) {
            $contentTypes ['Albums'] = ['slug' => 'album', 'name' => 'Albums'];
        }
        if ($cs->hasExtension('POLL_MANAGER')) {
            $contentTypes ['Polls'] = ['slug' => 'poll','name' => 'Polls'];
        }
        if ($cs->hasExtension('es.openhost.module.events')) {
            $contentTypes ['Events'] = ['slug' => 'event', 'name' => 'Events'];
        }
        if ($cs->hasExtension('es.openhost.module.companies')) {
            $contentTypes ['Companies'] = ['slug' => 'company', 'name' => 'Companies'];
        }
        if ($cs->hasExtension('es.openhost.module.obituaries')) {
            $contentTypes ['Obituaries'] = ['slug' => 'obituary', 'name' => 'Obituaries'];
        }
        return $contentTypes;
    }

    /**
     * Shows a page that shows a list of available RSS sources.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function indexAction()
    {
        $this->view->setConfig('rss');
        $cacheID             = $this->view->getCacheId('rss', 'index');
        $allowedModules      = $this->checkExtensions();
        $allowedcontentTypes = array_map(function ($element) {
            return $element['slug'];
        }, $allowedModules);

        $data = [];
        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('rss/index.tpl', $cacheID)
        ) {
            $sql = sprintf(
                'select distinct category_id, content_type_name from contents '
                . 'inner join content_category on contents.pk_content = content_category.content_id '
                . 'inner join category on content_category.category_id = category.id '
                . 'where category.rss = 1 and category.enabled = 1 '
                . 'and contents.content_type_name in ("%s") '
                . 'and content_status = 1 and in_litter = 0 '
                . 'and (starttime is null or starttime < "%s") '
                . 'and (endtime is null or endtime > "%s");',
                implode('","', $allowedcontentTypes),
                gmdate('Y-m-d H:i:s'),
                gmdate('Y-m-d H:i:s')
            );

            $response = $this->container->get('orm.manager')->getConnection('instance')
                ->executeQuery($sql)
                ->fetchAll();

            foreach ($response as $element) {
                $module = array_filter($allowedModules, function ($item) use ($element) {
                    return $item['slug'] == $element['content_type_name'];
                });

                $key = array_pop($module)['name'];
                if (!array_key_exists($key, $data)) {
                    $data[$key] = [];
                }
                if (!array_key_exists('slug', $data[$key])) {
                    $data[$key]['slug'] = $element['content_type_name'];
                }
                if (!array_key_exists('values', $data[$key])) {
                    $data[$key]['values'] = [];
                }
                $data[$key]['values'] = array_merge(
                    $data[$key]['values'],
                    [$element['category_id']]
                );
            }

            $authors = $this->get('api.service.author')
                ->getList('order by name asc');

            // Sort array by relevance based on allowedModules array
            $data = array_filter(array_replace($allowedModules, $data), function ($item) {
                return array_key_exists('values', $item);
            });

            $this->view->assign([
                'categoriesTree' => $data,
                'allowedModules' => $allowedModules,
                'opinionAuthors' => $authors['items'],
            ]);
        }

        return $this->render('rss/index.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-tags'      => 'rss-index',
        ]);
    }

    /**
     * Displays the RSS feed with contents in frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function frontpageRssAction(Request $request)
    {
        $categoryName = $request->query->filter('category', null, FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('rss');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        $this->setViewExpireDate($expire);

        $categoryID = 0;
        $rssTitle   = _('Homepage News');

        if (!empty($categoryName)) {
            try {
                $oql = sprintf(
                    'enabled = 1 and rss = 1'
                    . ' and name regexp "(%%\"|^)%s(\"%%|$)"',
                    $categoryName
                );

                $category   = $this->get('api.service.category')
                    ->getItemBy($oql);
                $categoryID = $category->id;
                $rssTitle   = $category->name;
            } catch (\Exception $e) {
                throw new ResourceNotFoundException();
            }
        }

        $cacheID = $this->view->getCacheId('rss', 'frontpage', $categoryID);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            list($contentPositions, $contents, , ) =
                $this->get('api.service.frontpage')
                ->getCurrentVersionForCategory($categoryID);

            // Remove advertisements and widgets
            $contents = array_filter(
                $contents,
                function ($a) {
                    return !in_array(
                        $a->content_type_name,
                        [ 'advertisement', 'widget' ]
                    );
                }
            );

            $this->sortByPlaceholder($contents, $contentPositions, $categoryName);
            $this->getRelatedContents($contents);

            $this->view->assign([
                'rss_title' => $rssTitle,
                'contents'  => $contents,
                'type'      => $categoryName,
            ]);
        }

        $params = [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => 'rss-frontpage-' . $categoryID
        ];

        $response = $this->render('rss/rss.tpl', $params);

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function generalRssAction(Request $request)
    {
        $id     = null;
        $slug   = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $type   = $request->query->filter('type', 'article', FILTER_SANITIZE_STRING);
        $xtags  = 'rss-' . $type;
        $titles = [
            'album'    => _('Latest Albums'),
            'article'  => _('Latest News'),
            'opinion'  => _('Latest Opinions'),
            'video'    => _('Latest Videos'),
            'poll'     => _('Latest Polls'),
            'event'    => _('Latest Events'),
            'obituary' => _('Latest Obituaries'),
            'company'  => _('Latest Companies'),
        ];

        // Setup templating cache layer
        $this->view->setConfig('rss');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        $this->setViewExpireDate($expire);

        $rssTitle = $titles[$type];

        if (!empty($slug)) {
            try {
                $oql = sprintf(
                    'enabled = 1 and rss = 1 '
                    . 'and name regexp "(.*\"|^)%s(\".*|$)"',
                    $slug
                );

                $category = $this->get('api.service.category')
                    ->getItemBy($oql);

                $xtags .= ',category-' . $category->id;

                $id       = $category->id;
                $slug     = $category->name;
                $rssTitle = $rssTitle . ' - ' . $category->title;
            } catch (\Exception $e) {
                throw new ResourceNotFoundException();
            }
        }

        $cacheID = empty($id) ?
            $this->view->getCacheId('rss', $type, '') :
            $this->view->getCacheId('rss', $type, $id);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            $total = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('elements_in_rss', 10);

            $contents = $this->getLatestContents($type, $id, $total);
            $this->getRelatedContents($contents);

            $this->view->assign([
                'contents'  => $contents,
                'rss_title' => $rssTitle,
                'type'      => $type
            ]);
        }

        $response = $this->render('rss/rss.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => $xtags
        ]);

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    /**
     * Displays the RSS feed for most viewed articles.
     *
     * @return Response The response object.
     */
    public function mostViewedRssAction()
    {
        $id    = null;
        $type  = 'article';
        $xtags = 'rss-' . $type;

        // Setup templating cache layer
        $this->view->setConfig('rss');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        $this->setViewExpireDate($expire);

        $rssTitle = _('Most viewed');

        $cacheID = empty($id) ?
            $this->view->getCacheId('rss', $type, '') :
            $this->view->getCacheId('rss', $type, $id);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            $total = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('elements_in_rss', 10);

            $contents = $this->getMostViewedContents($type, $id, $total);

            $this->getRelatedContents($contents);

            $this->view->assign([
                'contents'  => $contents,
                'rss_title' => $rssTitle,
                'type'      => $type
            ]);
        }

        $response = $this->render('rss/rss.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => $xtags
        ]);

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    /**
     * Shows the author frontpage.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function authorRSSAction(Request $request)
    {
        $slug  = $request->get('author_slug', null);
        $total = 10;

        // Get user by slug
        try {
            $user = $this->container->get('api.service.author')
                ->getItemBy("username = '$slug' or slug = '$slug'");
        } catch (GetItemException $e) {
            throw new ResourceNotFoundException();
        }

        $expected = $this->get('router')
            ->generate('frontend_rss_author', [ 'author_slug' => $user->slug ]);
        $expected = $this->get('core.decorator.url')->prefixUrl($expected);

        if ($request->getPathInfo() !== $expected) {
            return new RedirectResponse($expected);
        }

        // Setup templating cache layer
        $this->view->setConfig('rss');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        $this->setViewExpireDate($expire);

        $cacheID = $this->view->getCacheId('rss', 'author', $slug);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            $rssTitle = sprintf('RSS de «%s»', $user->name);
            // Get entity repository
            $er = $this->get('entity_repository');

            $order   = ['starttime' => 'DESC' ];
            $filters = [
                'fk_author'       => [['value' => $user->id]],
                'fk_content_type' => [['value' => [1, 4, 7], 'operator' => 'IN']],
                'content_status'  => [['value' => 1]],
                'in_litter'       => [['value' => 0]],
                'starttime'       => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'           => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            $contents = $er->findBy($filters, $order, $total, 1);

            $this->view->assign(['contents' => $contents, 'rss_title' => $rssTitle]);
        }

        $response = $this->render('rss/rss.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => 'rss-author-' . $user->id
        ]);

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function facebookInstantArticlesAction()
    {
        if (!$this->get('core.security')->hasExtension('FIA_MODULE')) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('rss');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        $this->setViewExpireDate($expire);

        $cacheID = $this->view->getCacheId('rss', 'fia');

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/fb_instant_articles.tpl', $cacheID))
        ) {
            // Get last articles contents
            $contents = $this->getLatestContents('article', null, 50);

            $er = $this->get('entity_repository');
            foreach ($contents as $key => $content) {
                // Exclude articles with external link or without body from RSS
                if ((isset($content->params['bodyLink'])
                    && !empty($content->params['bodyLink']))
                    || empty($content->body)
                ) {
                    unset($contents[$key]);
                } else {
                    // Wrap img with figure and add caption
                    $content->body = preg_replace(
                        '@(<p>)*(<img[^>]+>)@',
                        '<figure>${2}</figure>${1}',
                        $content->body
                    );

                    // Wrap social embed and iframes also add absolute url for images
                    $patterns      = [
                        '@(<blockquote.*class="(instagram-media|twitter-tweet)"[^>]+>.+'
                        . '<\/blockquote>\n*<script[^>]+><\/script>)@',
                        '@(<p>)*(<iframe[^>]+><\/iframe>)@',
                        '@src="/media/@'
                    ];
                    $replacements  = [
                        '<figure class="op-interactive"><iframe>${1}</iframe></figure>',
                        '<figure class="op-interactive">${2}</figure>${1}',
                        'src="' . $this->container->get('core.instance')->getBaseUrl() . '/media/'
                    ];
                    $content->body = preg_replace($patterns, $replacements, $content->body);

                    // Change <br> tag to <p>
                    $content->body = preg_replace("@<br[\s]*\/?>[\s]*?\n?[\s]*@", "</p>\n<p>", $content->body);

                    // Clean empty HTML tags
                    $content->body = preg_replace('@<(.*)>(\s*|&nbsp;)<\/\1>@', '', $content->body);
                }
            }

            // Limit related contents to 3
            $this->getRelatedContents($contents, 3);

            $this->view->assign('contents', $contents);
        }

        $this->getAds();

        $response = $this->render('rss/fb_instant_articles.tpl', [
            'ads_format'  => 'fia',
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => 'rss-instant-articles'
        ]);

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    /**
     * Displays the Rss feed for standalone news of Google News Showcase.
     *
     * @param Request   $request The request object.
     *
     * @return Response The rss feed for standalone news of Google News Showcase.
     */
    public function googleNewsAction()
    {
        if (!$this->get('core.security')->hasExtension('es.openhost.module.google_news_showcase')) {
            throw new ResourceNotFoundException();
        }

        $expire = $this->get('core.helper.content')->getCacheExpireDate();

        $response = $this->render('rss/google_news_showcase.tpl', [
            'contents'    => $this->getShowcaseContents('showcase', 1),
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => 'rss-google-news-showcase'
        ]);

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    /**
     * Get latest contents given a type of content.
     *
     * @param string  $contentType The content type name of the contents.
     * @param integer $category    The category id.
     * @param integer $total       The total number of contents.
     *
     * @return Array Latest contents.
     */
    public function getLatestContents($contentType = 'article', $category = null, $total = 10)
    {
        $sqlBase = 'SELECT * FROM contents '
            . '%s'
            . ' WHERE %s'
            . ' ORDER BY %s'
            . ' LIMIT ' . $total;

        $orderBy = 'starttime desc';
        $join    = '';
        $where   = 'content_type_name = "' . $contentType . '"'
            . ' AND content_status = 1'
            . ' AND in_litter != 1'
            . ' AND (starttime IS NULL OR starttime <= "' . gmdate('Y-m-d H:i:s') . '")'
            . ' AND (endtime IS NULL OR endtime > "' . gmdate('Y-m-d H:i:s') . '")';

        // Get categories excluded from RSS
        $categories = $this->get('api.service.category')
            ->getList('enabled != 1 or rss != 1');

        $ids = array_map(function ($a) {
            return $a->id;
        }, $categories['items']);

        if (!in_array($contentType, ['opinion', 'obituary', 'company'])) {
            if (empty($ids)) {
                return [];
            }

            $join  .= 'inner join content_category on pk_content = content_id ';
            $where .= sprintf(' AND category_id NOT IN (%s)', implode(',', $ids));

            if (!empty($category)) {
                $where .= ' AND category_id = ' . $category;
            }
        }

        if ($contentType == 'event') {
            $where .= sprintf(
                'and (cm1.meta_value >= "%s" or (cm1.meta_value < "%s" and cm2.meta_value >= "%s"))',
                gmdate('Y-m-d H:i:s'),
                gmdate('Y-m-d H:i:s'),
                gmdate('Y-m-d H:i:s')
            );
            $join  .= 'inner join contentmeta as cm1 on contents.pk_content = cm1.fk_content '
                . 'and cm1.meta_name = "event_start_date" '
                . 'left join contentmeta as cm2 on contents.pk_content = cm2.fk_content '
                . 'and cm2.meta_name = "event_end_date" ';
        }

        $sql      = sprintf($sqlBase, $join, $where, $orderBy);
        $contents = $this->get('api.service.content')->getListBySql($sql)['items'];
        $cm       = new \ContentManager();
        $contents = $cm->filterBlocked($contents);

        return $contents;
    }

    /**
     * Get latest contents given a type of content.
     *
     * @param string  $contentType The content type name of the contents.
     * @param integer $category    The category id.
     * @param integer $total       The total number of contents.
     *
     * @return Array Latest contents.
     */
    public function getMostViewedContents($contentType = 'article', $category = null, $total = 10)
    {
        $em = $this->get('entity_repository');

        $startDate = gmdate('Y-m-d H:i:s', strtotime('- 3 days'));

        $order   = [ 'views' => 'DESC' ];
        $filters = [
            'join' => [
                [
                    'table'               => 'content_views',
                    'type'                => 'inner',
                    'contents.pk_content' => [
                        [
                            'value' => 'content_views.pk_fk_content',
                            'field' => true
                        ]
                    ]
                ]
            ],
            'content_type_name' => [[ 'value' => $contentType ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'created'           => [[ 'value' => $startDate, 'operator' => '>=' ]],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => gmdate('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        // Get categories with enabled = 1 and rss = 1
        $categories = $this->get('api.service.category')
            ->getList('enabled = 1 and rss = 1');

        $ids = array_map(function ($a) {
            return $a->id;
        }, $categories['items']);

        // Fix condition for IN operator when no categories
        $ids = empty($ids) ? [ '' ] : $ids;

        $filters['category_id'] = [
            [ 'value' => $ids, 'operator' => 'IN' ]
        ];

        if (!empty($category)) {
            $filters['category_id'] = [ [ 'value' => $category ] ];
        }

        $contents = $em->findBy($filters, $order, $total, 1);

        $cm       = new \ContentManager();
        $contents = $cm->filterBlocked($contents);

        return $contents;
    }

    /**
     * Loads the list of positions and advertisements on renderer service.
     */
    protected function getAds()
    {
        $positions = $this->get('core.helper.advertisement')
            ->getPositionsForGroup('fia_inner', [1075, 1076, 1077]);

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions);

        $this->get('frontend.renderer.advertisement')
            ->setPositions($positions)
            ->setAdvertisements($advertisements);
    }

    /**
     * Sorts a list of contents by position and placeholder.
     *
     * @param array  $contents The list of contents to sort.
     * @param string $category The category name.
     */
    protected function sortByPlaceholder(&$contents, $contentPositions, $category)
    {
        $order = $this->getPlaceholders($category);

        if (empty($order)) {
            return;
        }

        // Order contentPositions
        uksort($contentPositions, function ($a, $b) use ($order) {
            $positionA = array_search($a, $order);
            $positionB = array_search($b, $order);

            return $positionA < $positionB ? -1 : 1;
        });

        // Set array with contents order
        $sorted = [];
        foreach ($contentPositions as $items) {
            foreach ($items as $item) {
                if (array_key_exists($item->pk_fk_content, $contents)) {
                    $sorted[$item->pk_fk_content] =
                        $contents[$item->pk_fk_content];
                }
            }
        }
        // Reassign ordered contents
        $contents = $sorted;
    }

    /**
     * Returns the list of placeholders to sort by for the category.
     *
     * @param string $name The category name.
     *
     * @return array The list of placeholders to sort by.
     */
    protected function getPlaceholders($name)
    {
        try {
            $category = $this->get('api.service.category')
                ->getItemBySlug($name);

            $setting = 'frontpage_layout_' . $category->id;
        } catch (\Exception $e) {
            $setting = 'frontpage_layout_0';
        }

        $layout = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get($setting);
        $theme  = $this->get('core.theme');

        if (empty($layout)) {
            $layout = 'default';
        }

        if (!empty($theme->parameters)
            && array_key_exists('layouts', $theme->parameters)
            && array_key_exists($layout, $theme->parameters['layouts'])
            && array_key_exists('order', $theme->parameters['layouts'][$layout])
        ) {
            return $theme->parameters['layouts'][$layout]['order'];
        }

        return [];
    }

    /**
     * Search related contents for each content in list.
     *
     * @param array $contents The list of contents.
     */
    protected function getRelatedContents(&$contents, $limit = null)
    {
        foreach ($contents as $key => $content) {
            // Exclude articles with external link from RSS
            if (isset($content->params['bodyLink'])
               && !empty($content->params['bodyLink'])) {
                unset($contents[$key]);
            }
        }
    }

    /**
     * Returns the list of contents marked with showcase flags.
     *
     * @param string $flag The name of the flag to check.
     * @param string $days The limit of days that the content can be in the showcase rss.
     *
     * @return array The array of contents checked with showcase limited by date.
     */
    protected function getShowcaseContents($flag, $days)
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval(sprintf('P%dD', $days)));

        $oql = sprintf(
            'content_type_name = "article" and content_status = 1 and in_litter = 0 ' .
            'and %s = 1 and (starttime is null or starttime > "%s") ' .
            'and (endtime is null or endtime < "%s") order by starttime desc',
            $flag,
            $date->format('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        );

        try {
            return $this->get('api.service.content')->getList($oql)['items'];
        } catch (\Exception $e) {
            return [];
        }
    }
}
