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
        $cacheID = $this->view->generateCacheId('Index', '', "RSS");

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

        return $this->render(
            'rss/index.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss' ]
        );
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

        $this->view->setConfig('rss');

        $id       = 0;
        $cm       = new \ContentManager;
        $cacheID  = $this->view->generateCacheId($categoryName, '', 'RSS|frontpage');
        $rssTitle = _('Homepage News');

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
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

            $contents = $cm->getContentsForHomepageOfCategory($id);
            $contents = $cm->getInTime($contents);

            // Remove advertisements and widgets
            $contents = array_filter($contents, function ($a) {
                return !in_array(
                    $a->content_type_name,
                    [ 'advertisement', 'widget' ]
                );
            });

            $this->sortByPlaceholder($contents, $categoryName);
            $this->getRelatedContents($contents);

            $this->view->assign([
                'rss_title' => $rssTitle,
                'contents'  => $contents,
                'type'      => $categoryName,
            ]);
        }

        return $this->render(
            'rss/rss.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss' ],
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
        $cacheID  = $this->view->generateCacheId($type, '', 'RSS|' . $category);
        $titles   = [
            'album'   => _('Latest Albums'),
            'article' => _('Latest News'),
            'opinion' => _('Latest Opinions'),
            'video'   => _('Latest Videos'),
        ];

        if ($category === 'last') {
            $category = null;
        }

        $this->view->setConfig('rss');

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
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss' ],
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
        $slug    = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);
        $total   = 10;
        $cacheId = $this->view->generateCacheId('rss|author', '', $slug);

        $this->view->setConfig('rss');

        if (($this->view->getCaching() === 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheId))
        ) {
            // Get user by slug
            $user = $this->get('user_repository')->findOneBy(
                [ 'username' => [[ 'value' => $slug ]] ],
                ''
            );

            if (is_null($user)) {
                throw new ResourceNotFoundException();
            }

            $rssTitle   = sprintf('RSS de «%s»', $user->name);
            // Get entity repository
            $er = $this->get('entity_repository');
            $user->photo = $er->find('Photo', $user->avatar_img_id);

            $order = ['starttime' => 'DESC' ];
            $filters =  [
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
            [ 'cache_id' => $cacheId, 'x-tags' => 'rss' ],
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
    public function facebookInstantArticlesRSSAction(Request $request)
    {
        if (!$this->get('core.security')->hasExtension('FIA_MODULE')) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('rss');

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        $cacheID = $this->view->generateCacheId('instantArticles', '', 'RSS');
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
                    $relations = getService('related_contents')->getRelations($content->id, 'inner');
                    if (count($relations) > 0) {
                        $contentObjects = $this->get('entity_repository')->findMulti($relations);

                        // Filter out not ready for publish contents.
                        foreach ($contentObjects as $contentID) {
                            if (!$content->isReadyForPublish()) {
                                continue;
                            }

                            $relatedContents[] = $content;
                        }
                    }

                    // Wrap img with figure and add caption
                    $content->body = preg_replace(
                        '@(<p>)*(<img[^>]+>)@',
                        '<figure>${2}</figure>${1}',
                        $content->body
                    );

                    // Wrap social embed and iframes
                    $patterns = [
                        '@(<blockquote.*class="(instagram-media|twitter-tweet)"[^>]+>.+<\/blockquote>\n*<script[^>]+><\/script>)@',
                        '@(<p>)*(<iframe[^>]+><\/iframe>)@'
                    ];
                    $replacements = [
                        '<figure class="op-social"><iframe>${1}</iframe></figure>',
                        '<figure class="op-interactive">${2}</figure>${1}'
                    ];
                    $content->body = preg_replace($patterns, $replacements, $content->body);

                    // Change <br> tag to <p>
                    $content->body = preg_replace("@<br[\s]*\/?>[\s]*?\n?[\s]*@", "</p>\n<p>", $content->body);

                    // Clean empty HTML tags
                    $content->body = preg_replace('@<(.*)>\s*<\/\1>@', '', $content->body);
                }
            }

            $this->view->assign('contents', $contents);
        }

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
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ],
            'endtime'           => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '>' ],
            ]
        ];

        if ($contentType !== 'opinion' && !empty($category)) {
            $filters['category_name'] = [ [ 'value' => $category ] ];
        }

        $contents = $em->findBy($filters, $order, $total, 1);

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
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        return getService('advertisement_repository')
            ->findByPositionsAndCategory([1075, 1076, 1077], $category);
    }

    /**
     * Sorts a list of contents by position and placeholder.
     *
     * @param array  $contents The list of contents to sort.
     * @param string $category The category name.
     */
    protected function sortByPlaceholder(&$contents, $category)
    {
        $order = $this->getPlaceholders($category);

        if (empty($order)) {
            return;
        }

        uasort($contents, function ($a, $b) use ($order) {
            $positionA = array_search($a->placeholder, $order);
            $positionB = array_search($b->placeholder, $order);

            return $positionA < $positionB ? -1 :
                ($positionA > $positionB ? 1 :
                ($a->position < $b->position ? -1 : 1)
            );
        });
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
    protected function getRelatedContents(&$contents)
    {
        // Fetch photo for each article
        $er = getService('entity_repository');
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

            // Related contents code ---------------------------------------
            $relations = getService('related_contents')->getRelations($content->id, 'inner');

            if (count($relations) > 0) {
                $relatedContents  = [];
                $relatedContents = $this->get('entity_repository')->findMulti($relations);
                $ccm = new \ContentCategoryManager();

                // Filter out not ready for publish contents.
                foreach ($relatedContents as $contentID) {
                    if ($content->isReadyForPublish()) {
                        $content->category_name = $ccm->getName($content->category);
                        if ($content->content_type == 1 && !empty($content->img1)) {
                            $content->photo = $er->find('Photo', $content->img1);
                        } elseif ($content->content_type == 1 && !empty($content->fk_video)) {
                            $content->video = $er->find('Video', $content->fk_video);
                        }
                        $relatedContents[] = $content;
                    }
                }

                $content->related = $relatedContents;
            }
        }
    }
}
