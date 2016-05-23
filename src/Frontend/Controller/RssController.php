<?php
/**
 * Handles the actions for the public RSS
 *
 * @package Frontend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the public RSS
 *
 * @package Frontend_Controllers
 **/
class RssController extends Controller
{
    /**
     * Shows a page that shows a list of available RSS sources
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function indexAction()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $cacheID = $this->view->generateCacheId('Index', '', "RSS");

        // Fetch information for Advertisements
        \Frontend\Controller\ArticlesController::getAds();

        if (($this->view->caching == 0)
            || !$this->view->isCached('rss/index.tpl', $cacheID)
        ) {
            $ccm = \ContentCategoryManager::get_instance();

            $categoriesTree = $ccm->getCategoriesTreeMenu();
            $opinionAuthors = \User::getAllUsersAuthors();

            $this->view->assign(
                [
                    'categoriesTree' => $categoriesTree,
                    'opinionAuthors' => $opinionAuthors,
                ]
            );
        }

        return $this->render(
            'rss/index.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss' ]
        );
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function generalRssAction(Request $request)
    {
        $categoryName = $request->query->filter('category_name', 'last', FILTER_SANITIZE_STRING);
        $author       = $request->query->filter('author', '', FILTER_SANITIZE_STRING);

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('rss');

        $cacheID = $this->view->generateCacheId($categoryName, '', 'RSS'.$author);
        if (($this->view->caching == 0)
           || (!$this->view->isCached('rss/rss.tpl', $cacheID))
        ) {
            // Set total number of contents
            $total = 10;
            $rssTitle = '';
            switch ($categoryName) {
                case 'opinion':
                    // Latest opinions
                    $rssTitle = _('Latest Opinions');
                    $contents = $this->getLatestOpinions($total);
                    break;
                case 'last':
                    // Latest news
                    $rssTitle = _('Latest News');
                    $contents = $this->getLatestContents('article', $total);
                    break;
                case 'videos':
                    // Latest videos
                    $rssTitle = _('Latest Videos');
                    $contents = $this->getLatestContents('video', $total);
                    foreach ($contents as &$content) {
                        $content->thumb = $content->getThumb();
                        $content->category_name  = $content->loadCategoryName($content->pk_content);
                        $content->category_title = $content->loadCategoryTitle($content->pk_content);
                    }
                    break;
                case 'albums':
                    // Latest albums
                    $rssTitle = _('Latest Albums');
                    $contents   = $this->getLatestContents('album', $total);
                    foreach ($contents as &$content) {
                        $content->cover          = $content->cover_image->path_img;
                        $content->category_name  = $content->loadCategoryName($content->id);
                        $content->category_title = $content->loadCategoryTitle($content->id);
                    }
                    break;
                default:
                    // Latest news by category
                    $category = getService('category_repository')->findOneBy(
                        [ 'name' => [[ 'value' => $categoryName ]] ],
                        'name ASC'
                    );
                    if (is_null($category)) {
                        throw new ResourceNotFoundException();
                    }
                    $rssTitle = $category->title;
                    $contents = $this->getLatestArticlesByCategory($categoryName, $total);
                    break;
            }

            // Fetch photo for each article
            $er = getService('entity_repository');
            foreach ($contents as $key => $content) {
                // Fetch photo for each content
                if (isset($content->img1) && !empty($content->img1)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img1);
                } elseif (isset($content->img2) && !empty($content->img2)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img2);
                }
                // Exclude articles with external link from RSS
                if (isset($content->params['bodyLink'])
                    && !empty($content->params['bodyLink'])) {
                    unset($contents[$key]);
                }

                $relationsId = getService('related_contents')->getRelationsForInner($content->id);
                if (count($relationsId) > 0) {
                    $cm = new \ContentManager;
                    $relatedContents  = $cm->getContents($relationsId);
                    // Drop contents that are not available or not in time
                    $relatedContents  = $cm->getInTime($relatedContents);
                    $relatedContents  = $cm->getAvailable($relatedContents);
                    $content->related = $relatedContents;
                }
            }

            $this->view->assign(
                [
                    'rss_title' => $rssTitle,
                    'contents'  => $contents,
                    'type'      => $categoryName,
                ]
            );
        }

        return $this->render(
            'rss/rss.tpl',
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss' ],
            new Response('', 200, ['Content-Type' => 'text/xml; charset=UTF-8'])
        );
    }

    /**
     * Shows the author frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function authorRSSAction(Request $request)
    {
        $slug  = $request->query->filter('author_slug', '', FILTER_SANITIZE_STRING);
        $total = 10;

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('rss');

        $cacheID = $this->view->generateCacheId('rss|author', '', $slug);
        if (($this->view->caching == 0)
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
            [ 'cache_id' => $cacheID, 'x-tags' => 'rss' ],
            new Response('', 200, ['Content-Type' => 'text/xml; charset=UTF-8'])
        );
    }

    /**
     * Displays the RSS feed for a given category, opinion or topic
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function facebookInstantArticlesRSSAction(Request $request)
    {
        if (!\Onm\Module\ModuleManager::isActivated('FIA_MODULE')) {
            throw new ResourceNotFoundException();
        }

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('rss');

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        $cacheID = $this->view->generateCacheId('instantArticles', '', 'RSS');
        if (($this->view->caching == 0)
           || (!$this->view->isCached('rss/fb_instant_articles.tpl', $cacheID))
        ) {
            // Get last articles contents
            $contents = $this->getLatestContents('article', 50);

            // Fetch photo for each article
            $er = getService('entity_repository');
            foreach ($contents as $key => $content) {
                // Fetch photo for each content
                if (isset($content->img1) && !empty($content->img1)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img1);
                } elseif (isset($content->img2) && !empty($content->img2)) {
                    $contents[$key]->photo = $er->find('Photo', $content->img2);
                }
                // Exclude articles with external link from RSS
                if (isset($content->params['bodyLink'])
                    && !empty($content->params['bodyLink'])) {
                    unset($contents[$key]);
                }

                $relationsId = getService('related_contents')->getRelationsForInner($content->id);
                if (count($relationsId) > 0) {
                    $cm = new \ContentManager;
                    $relatedContents  = $cm->getContents($relationsId);
                    // Drop contents that are not available or not in time
                    $relatedContents  = $cm->getInTime($relatedContents);
                    $relatedContents  = $cm->getAvailable($relatedContents);
                    $content->related = $relatedContents;
                }

                // Wrap img with figure and add caption
                $content->body = preg_replace(
                    '@(<img[^>]+>)@',
                    '<figure>${1}<figcaption>'.$content->title.'</figcaption></figure>',
                    $content->body
                );

                // Clean empty HTML tags
                $content->body = preg_replace('@<(.*)><\/\1>@', '', $content->body);
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
     * Get latest opinions
     *
     * @param int $total The total number of contents
     *
     * @return Array Latest opinions
     **/
    public function getLatestOpinions($total = 10)
    {
        $or = getService('opinion_repository');

        $order = [ 'starttime' => 'DESC' ];
        $filters = [
            'content_status' => [[ 'value' => 1 ]],
            'in_litter'      => [[ 'value' => 1, 'operator' => '!=' ]],
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

        $contents = $or->findBy($filters, $order, $total, 1);

        return $contents;
    }

    /**
     * Get latest contents given a type of content
     *
     * @param int $contentType The type of the contents to fetch
     * @param int $total The total number of contents
     *
     * @return Array Latest contents
     **/
    public function getLatestContents($contentType = 'article', $total = 10)
    {
        $er = getService('entity_repository');

        $order = [ 'starttime' => 'DESC' ];
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

        $contents = $er->findBy($filters, $order, $total, 1);

        return $contents;
    }

    /**
     * Get latest articles by category
     *
     * @param int $total The total number of contents
     * @param string $category The category to fetch articles from
     *
     * @return Array Latest articles of category
     **/
    public function getLatestArticlesByCategory($category, $total = 10)
    {
        $er = getService('entity_repository');

        $order = [ 'starttime' => 'DESC' ];
        $filters = [
            'content_type_name' => [[ 'value' => 'article' ]],
            'category_name'     => [[ 'value' => $category ]],
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

        $contents = $er->findBy($filters, $order, $total, 1);

        return $contents;
    }


    /**
     * Fetches advertisements for Instant article
     *
     * @param string category the category identifier
     *
     * @return array the list of advertisements for this page
     **/
    public static function getAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        return \Advertisement::findForPositionIdsAndCategory([1075, 1076, 1077], $category);
    }
}
