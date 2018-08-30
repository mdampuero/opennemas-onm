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

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the public RSS
 */
class RssController extends Controller
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
        // Setup templating cache layer
        $this->view->setConfig('rss');
        $cacheID = $this->view->getCacheId('rss', 'index');

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('rss/index.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            $categoriesTree = $ccm->getCategoriesTreeMenu();
            $opinionAuthors = \User::getAllUsersAuthors();

            $this->view->assign([
                'categoriesTree' => $categoriesTree,
                'opinionAuthors' => $opinionAuthors,
            ]);
        }

        return $this->render('rss/index.tpl', [
            'cache_id' => $cacheID,
            'x-tags'   => 'rss,index',
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
        $categoryName = $request->query->filter('category', 'home', FILTER_SANITIZE_STRING);

        // Setup templating cache layer
        $this->view->setConfig('rss');
        $cacheID = $this->view->getCacheId('rss', 'frontpage', $categoryName);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            $id       = 0;
            $rssTitle = _('Homepage News');

            if (!empty($categoryName) && $categoryName !== 'home') {
                $category = getService('category_repository')->findOneBy(
                    [ 'name' => [[ 'value' => $categoryName ]] ],
                    'name ASC'
                );

                if (is_null($category)) {
                    throw new ResourceNotFoundException();
                }

                $id       = $category->id;
                $rssTitle = $category->title;
            }

            list($contentPositions, $contents, $invalidationDt, $lastSaved) =
                $this->get('api.service.frontpage_version')
                    ->getPublicFrontpageData($id);

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

        return $this->render(
            'rss/rss.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss,frontpage-' . $categoryName ],
            new Response('', 200, ['Content-Type' => 'text/xml; charset=UTF-8'])
        );
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
        $type     = $request->query->filter('type', 'article', FILTER_SANITIZE_STRING);
        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $titles   = [
            'album'   => _('Latest Albums'),
            'article' => _('Latest News'),
            'opinion' => _('Latest Opinions'),
            'video'   => _('Latest Videos'),
        ];

        if ($category === 'last') {
            $category = null;
        }

        // Setup templating cache layer
        $this->view->setConfig('rss');
        $cacheID = $this->view->getCacheId('rss', $type, $category);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            $rssTitle = $titles[$type];
            $total    = $this->get('setting_repository')->get('elements_in_rss', 10);
            $contents = $this->getLatestContents($type, $category, $total);

            $this->getRelatedContents($contents);

            if (!empty($category)) {
                $c = getService('category_repository')
                    ->findOneBy([ 'name' => [[ 'value' => $category ]] ]);

                if (!empty($c)) {
                    $rssTitle = $rssTitle . ' - ' . $c->title;
                }
            }

            $this->view->assign([
                'rss_title' => $rssTitle,
                'contents'  => $contents,
                'type'      => $type,
                'category'  => $category
            ]);
        }

        return $this->render(
            'rss/rss.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss,' . $type . ',' . $category ],
            new Response('', 200, ['Content-Type' => 'text/xml; charset=UTF-8'])
        );
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
        $cacheID = $this->view->getCacheId('rss', 'author', $slug);

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            // Get user by slug
            $user = $this->get('user_repository')->findOneBy(
                [ 'username' => [[ 'value' => $slug ]] ],
                ''
            );

            if (is_null($user)) {
                throw new ResourceNotFoundException();
            }

            $rssTitle = sprintf('RSS de «%s»', $user->name);
            // Get entity repository
            $er          = $this->get('entity_repository');
            $user->photo = $er->find('Photo', $user->avatar_img_id);

            $order   = ['starttime' => 'DESC' ];
            $filters = [
                'fk_author'       => [['value' => $user->id]],
                'fk_content_type' => [['value' => [1, 4, 7], 'operator' => 'IN']],
                'content_status'  => [['value' => 1]],
                'in_litter'       => [['value' => 0]],
            ];

            $contents = $er->findBy($filters, $order, $total, 1);

            foreach ($contents as $key => $content) {
                $contents[$key]->author = $user;
                if (isset($content->img1) && ($content->img1 > 0)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img1);
                }

                // Get album cover photo
                if ($content->fk_content_type == 7) {
                    $contents[$key]->photo = $er->find('Photo', $content->cover_id);
                }
            }

            $this->view->assign(['contents' => $contents, 'rss_title' => $rssTitle]);
        }

        return $this->render(
            'rss/rss.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss,author-' . $slug ],
            new Response('', 200, ['Content-Type' => 'text/xml; charset=UTF-8'])
        );
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function facebookInstantArticlesRSSAction()
    {
        if (!$this->get('core.security')->hasExtension('FIA_MODULE')) {
            throw new ResourceNotFoundException();
        }

        // Setup templating cache layer
        $this->view->setConfig('rss');
        $cacheID = $this->view->getCacheId('rss', 'fia');

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/fb_instant_articles.tpl', $cacheID))
        ) {
            // Get last articles contents
            $contents = $this->getLatestContents('article', null, 50);

            // Fetch photo for each article
            $er = getService('entity_repository');
            foreach ($contents as $key => $content) {
                // Fetch photo for each content
                if (isset($content->img1) && !empty($content->img1)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img1);
                } elseif (isset($content->img2) && !empty($content->img2)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img2);
                }

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

        $this->view->assign('advertisements', $this->getAds());

        return $this->render(
            'rss/fb_instant_articles.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss,instant-articles' ],
            new Response('', 200, ['Content-Type' => 'text/xml; charset=UTF-8'])
        );
    }

    /**
     * Get latest contents given a type of content.
     *
     * @param int $contentType The type of the contents to fetch.
     * @param int $total The total number of contents.
     *
     * @return Array Latest contents.
     */
    public function getLatestContents($contentType = 'article', $category = null, $total = 10)
    {
        $em = getService('entity_repository');

        if ($contentType === 'opinion') {
            $em = getService('opinion_repository');
        }

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'content_type_name' => [[ 'value' => $contentType ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value' => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        // Fetch contents only on categories set inrss
        $categories = \ContentCategoryManager::get_instance()->findAll();
        $categories = array_map(function ($a) {
            return $a->name;
        }, array_filter($categories, function ($a) {
            return $a->internal_category == 1
                && is_array($a->params)
                && !empty($a->params['inrss']);
        }));

        // Fix condition for IN operator when no categories
        $categories = empty($categories) ? [ '' ] : $categories;

        if ($contentType !== 'opinion') {
            $filters['category_name'] = [
                [ 'value' => $categories, 'operator' => 'IN' ]
            ];

            if (!empty($category)) {
                $filters['category_name'] = [ [ 'value' => $category ] ];
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
    public static function getAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        $positions = getService('core.helper.advertisement')
            ->getPositionsForGroup('fia_inner', [1075, 1076, 1077]);

        return getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);
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
        $setting = null;

        if (!empty($name)) {
            try {
                $category = $this->get('orm.manager')->getRepository('Category')
                    ->findOneBy(sprintf('title = "%s"', $name));

                $setting = 'frontpage_layout_' . $category->pk_content_category;
            } catch (\Exception $e) {
                if ($name === 'home') {
                    $setting = 'frontpage_layout_0';
                }
            }
        }

        // TODO: Use new repository when cache is unified
        $layout = $this->get('setting_repository')->get($setting);
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
        // Fetch photo for each article
        $er = $this->get('entity_repository');

        foreach ($contents as $key => $content) {
            // Fetch photo for each content
            if (isset($content->img1) && !empty($content->img1)) {
                $contents[$key]->photo = $er->find('Photo', $content->img1);
            } elseif (isset($content->img2) && !empty($content->img2)) {
                $contents[$key]->photo = $er->find('Photo', $content->img2);
            }

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

            $relations = getService('related_contents')
                ->getRelations($content->id, 'inner', $limit);
            if (count($relations) > 0) {
                $relatedContents = [];
                $relateds        = $this->get('entity_repository')->findMulti($relations);
                $ccm             = \ContentCategoryManager::get_instance();

                // Filter out not ready for publish contents.
                foreach ($relateds as $related) {
                    if ($related->isReadyForPublish()) {
                        $related->category_name = $ccm->getName($related->category);
                        if ($related->content_type == 1 && !empty($related->img1)) {
                            $related->photo = $er->find('Photo', $related->img1);
                        } elseif ($related->content_type == 1 && !empty($related->fk_video)) {
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
