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

use Api\Exception\GetItemException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for the public RSS
 */
class RssController extends FrontendController
{
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
        $cacheID = $this->view->getCacheId('rss', 'index');

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('rss/index.tpl', $cacheID)
        ) {
            // Get categories with enabled = 1 and rss = 1
            $categories = $this->get('api.service.category')
                ->getList('enabled = 1 and rss = 1');

            $authors = $this->get('api.service.author')
                ->getList('order by name asc');

            $this->view->assign([
                'categoriesTree' => $categories['items'],
                'opinionAuthors' => $authors['items'],
            ]);
        }

        return $this->render('rss/index.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-tags'      => 'rss,index',
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

        $response = $this->render('rss/rss.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => 'rss,frontpage-' . $categoryID
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
    public function generalRssAction(Request $request)
    {
        $id     = null;
        $slug   = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $type   = $request->query->filter('type', 'article', FILTER_SANITIZE_STRING);
        $titles = [
            'album'   => _('Latest Albums'),
            'article' => _('Latest News'),
            'opinion' => _('Latest Opinions'),
            'video'   => _('Latest Videos'),
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
            'x-tags'      => 'rss,' . $type . ',' . $slug
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
        $slug  = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);
        $total = 10;

        // Setup templating cache layer
        $this->view->setConfig('rss');

        $expire = $this->get('core.helper.content')->getCacheExpireDate();
        $this->setViewExpireDate($expire);

        $cacheID = $this->view->getCacheId('rss', 'author', $slug);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            // Get user by slug
            try {
                $user = $this->container->get('api.service.author')
                    ->getItemBy("username='{$slug}'");
            } catch (GetItemException $e) {
                throw new ResourceNotFoundException();
            }

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
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
                ],
                'endtime'           => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
                ]
            ];

            $contents = $er->findBy($filters, $order, $total, 1);

            $this->view->assign(['contents' => $contents, 'rss_title' => $rssTitle]);
        }

        $response = $this->render('rss/rss.tpl', [
            'cache_id'    => $cacheID,
            'x-cacheable' => true,
            'x-cache-for' => $expire,
            'x-tags'      => 'rss,author-' . $slug
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

            // Fetch extras for each article
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
                        'src="' . SITE_URL . 'media/'
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

        list($adsPositions, $advertisements) = $this->getAds();

        $response = $this->render('rss/fb_instant_articles.tpl', [
            'advertisements' => $advertisements,
            'ads_positions'  => $adsPositions,
            'ads_format'     => 'fia',
            'cache_id'       => $cacheID,
            'x-cacheable'    => true,
            'x-cache-for'    => $expire,
            'x-tags'         => 'rss,instant-articles'
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
        $em = $this->get('entity_repository');

        if ($contentType === 'opinion') {
            $em = $this->get('opinion_repository');
        }

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'content_type_name' => [[ 'value' => $contentType ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
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

        if ($contentType !== 'opinion') {
            $filters['category_id'] = [
                [ 'value' => $ids, 'operator' => 'IN' ]
            ];

            if (!empty($category)) {
                $filters['category_id'] = [ [ 'value' => $category ] ];
            }
        }

        $contents = $em->findBy($filters, $order, $total, 1);

        $cm       = new \ContentManager();
        $contents = $cm->filterBlocked($contents);

        return $contents;
    }

    /**
     * Fetches advertisements for Instant article.
     *
     * @param string category The category identifier.
     *
     * @return array The list of advertisements for this page.
     */
    protected function getAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        $positions = $this->get('core.helper.advertisement')
            ->getPositionsForGroup('fia_inner', [1075, 1076, 1077]);

        $advertisements = $this->get('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
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
        // Fetch extras for each article
        $er = $this->get('entity_repository');

        foreach ($contents as $key => $content) {
            if (isset($content->fk_video) && !empty($content->fk_video)) {
                $contents[$key]->video = $er->find('Video', $content->fk_video);
            } elseif (isset($content->fk_video2) && !empty($content->fk_video2)) {
                $contents[$key]->video = $er->find('Video', $content->fk_video2);
            }

            // Exclude articles with external link from RSS
            if (isset($content->params['bodyLink'])
               && !empty($content->params['bodyLink'])) {
                unset($contents[$key]);
            }

            $relations = $this->get('related_contents')
                ->getRelations($content->id, 'inner', $limit);
            if (!empty($relations)) {
                $relatedContents = [];
                $relateds        = $this->get('entity_repository')->findMulti($relations);

                // Filter out not ready for publish contents.
                foreach ($relateds as $related) {
                    if ($related->isReadyForPublish()) {
                        if ($related->content_type == 1 && !empty($related->fk_video)) {
                            $related->video = $er->find('Video', $related->fk_video);
                        }

                        $relatedContents[] = $related;
                    }
                }

                $content->related = $relatedContents;
            }
        }
    }
}
