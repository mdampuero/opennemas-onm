<?php
/**
 * Handles the actions for the articles
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
use Onm\Module\ModuleManager;
use Onm\StringUtils;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the articles
 *
 * @package Frontend_Controllers
 **/
class ArticlesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);

        $this->cm   = new \ContentManager();
        $this->ccm  = \ContentCategoryManager::get_instance();
    }

    /**
     * Displays the article given its id or slug
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        // Resolve article ID
        $er = $this->get('entity_repository');
        $articleID = $er->resolveID($dirtyID);
        if (empty($articleID)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $article = $er->find('Article', $articleID);

        if (isset($article->params['bodyLink']) && !empty($article->params['bodyLink'])) {
            return $this->redirect($article->params['bodyLink']);
        }

        // Load config
        $this->view->setConfig('articles');

        $this->paywallHook($article);

        $cm = new \ContentManager();

        // Advertisements for single article NO CACHE
        $actualCategoryId    = $this->ccm->get_id($categoryName);
        $ads = $this->getAds($actualCategoryId);
        $this->view->assign('advertisements', $ads);

        $cacheID = $this->view->generateCacheId($categoryName, null, $articleID);

        $layout = $this->get('setting_repository')->get('frontpage_layout_'.$actualCategoryId, 'default');
        $layoutFile = 'layouts/'.$layout.'.tpl';

        $this->view->assign('layoutFile', $layoutFile);

        if (($this->view->caching == 0)
            || !$this->view->isCached("extends:{$layoutFile}|article/article.tpl", $cacheID)
        ) {
            if (($article->content_status == 1) && ($article->in_litter == 0)
                && ($article->isStarted())
            ) {
                // Categories code -------------------------------------------
                // TODO: Seems that this is rubbish, evaluate its removal
                $actualCategory      = $categoryName;
                $actualCategoryId    = $this->ccm->get_id($actualCategory);
                $actualCategoryTitle = $this->ccm->get_title($actualCategory);
                $categoryData        = null;
                if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $this->ccm->categories)) {
                    $categoryData = $this->ccm->categories[$actualCategoryId];
                }

                $this->view->assign(
                    array(
                        'category_name'         => $actualCategory ,
                        'actual_category_title' => $actualCategoryTitle,
                        'actual_category'       => $actualCategory,
                        'actual_category_id'    => $actualCategoryId,
                        'category_data'         => $categoryData,
                    )
                );

                // Associated media code --------------------------------------
                if (isset($article->img2) && ($article->img2 > 0)) {
                    $photoInt = new \Photo($article->img2);
                    $this->view->assign('photoInt', $photoInt);
                }

                if (isset($article->fk_video2) && ($article->fk_video2 > 0)) {
                    $videoInt = new \Video($article->fk_video2);
                    $this->view->assign('videoInt', $videoInt);
                }

                $article->media_url = '';
                if (is_object($article->author)) {
                    $article->author->getPhoto();
                }

                // Related contents code ---------------------------------------
                $relContent      = new \RelatedContent();
                $relatedContents = array();

                $relationIDs     = $relContent->getRelationsForInner($articleID);
                if (count($relationIDs) > 0) {
                    $relatedContents = $cm->getContents($relationIDs);

                    // Drop contents that are not available or not in time
                    $relatedContents = $cm->getInTime($relatedContents);
                    $relatedContents = $cm->getAvailable($relatedContents);

                    // Add category name
                    foreach ($relatedContents as $key => &$content) {
                        $content->category_name = $this->ccm->get_category_name_by_content_id($content->id);
                        if ($key == 0 && $content->content_type == 1 && !empty($content->img1)) {
                             $content->photo = new \Photo($content->img1);
                        }
                    }
                }
                $this->view->assign('relationed', $relatedContents);

                // Machine suggested contents code -----------------------------
                $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
                    $article->metadata,
                    'article',
                    "pk_fk_content_category= ".$article->category.
                    " AND contents.content_status=1 AND pk_content = pk_fk_content",
                    4
                );

                foreach ($machineSuggestedContents as &$element) {
                    $element['uri'] = \Uri::generate(
                        'article',
                        array(
                            'id'       => $element['pk_content'],
                            'date'     => date('YmdHis', strtotime($element['created'])),
                            'category' => $element['catName'],
                            'slug'     => StringUtils::get_title($element['title']),
                        )
                    );
                }

                $this->view->assign('suggested', $machineSuggestedContents);
            } else {
                throw new ResourceNotFoundException();
            }

        } // end if $this->view->is_cached

        return $this->render(
            "extends:{$layoutFile}|article/article.tpl",
            array(
                'cache_id'      => $cacheID,
                'contentId'     => $articleID,
                'category_name' => $categoryName,
                'article'       => $article,
                'content'       => $article,
            )
        );
    }

    /**
     * Displays the external article given its id or slug
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extShowAction(Request $request)
    {
        // Fetch HTTP variables
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Setup view
        $this->view->setConfig('articles');

        $cacheID = $this->view->generateCacheId('sync'.$categoryName, null, $dirtyID);

        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$categoryName.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        // Advertisements for single article NO CACHE
        $cm = new \ContentManager;
        // Get category id correspondence
        $wsActualCategoryId = $cm->getUrlContent($wsUrl.'/ws/categories/id/'.$categoryName);
        // Fetch advertisement information from external
        $ads = unserialize($cm->getUrlContent($wsUrl.'/ws/ads/article/'.$wsActualCategoryId, true));
        $this->view->assign('advertisements', $ads);

        // Get full article
        $article = $cm->getUrlContent($wsUrl.'/ws/articles/complete/'.$dirtyID, true);
        $article = unserialize($article);

        if (($article->content_status==1) && ($article->in_litter==0)
            && ($article->isStarted())
        ) {
            // Template vars
            $this->view->assign(
                array(
                    'article'               => $article,
                    'content'               => $article,
                    'photoInt'              => $article->photoInt,
                    'videoInt'              => $article->videoInt,
                    'relationed'            => $article->relatedContents,
                    'contentId'             => $article->id,// Used on module_comments.tpl
                    'actual_category_title' => $article->category_title,
                    'suggested'             => $article->suggested,
                    'ext'                   => 1,
                )
            );

        } else {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        return $this->render(
            'article/article.tpl',
            array(
                'cache_id' => $cacheID,
                'category_name' => $categoryName,
            )
        );
    }

    /**
     * Redirects the article given its external link url
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function externalLinkAction(Request $request)
    {
        $url = $request->query->filter('to', '', FILTER_VALIDATE_URL);

        if (empty($url)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        return $this->redirect($url);

    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return array the list of advertisements for this page
     **/
    public static function getAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // Get article_inner positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        $positions = $positionManager->getAdsPositionsForGroup('article_inner', array(7, 9));

        return  \Advertisement::findForPositionIdsAndCategory($positions, $category);
    }

    /**
     * Alteres the article given the paywall module status
     *
     * @return Article the article
     **/
    public function paywallHook(&$content)
    {
        $paywallActivated = ModuleManager::isActivated('PAYWALL');
        $onlyAvailableSubscribers = $content->isOnlyAvailableForSubscribers();

        if ($paywallActivated && $onlyAvailableSubscribers) {
            $newContent = $this->renderView(
                'paywall/partials/content_only_for_subscribers.tpl',
                array('id' => $content->id)
            );

            $isLogged = array_key_exists('userid', $_SESSION);
            if ($isLogged) {
                if (array_key_exists('meta', $_SESSION)
                    && array_key_exists('paywall_time_limit', $_SESSION['meta'])) {
                    $userSubscriptionDateString = $_SESSION['meta']['paywall_time_limit'];
                } else {
                    $userSubscriptionDateString = '';
                }
                $userSubscriptionDate = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $userSubscriptionDateString,
                    new \DateTimeZone('UTC')
                );

                $now = new \DateTime('now', new \DateTimeZone('UTC'));

                $hasSubscription = $userSubscriptionDate > $now;

                if (!$hasSubscription) {
                    $newContent = $this->renderView(
                        'paywall/partials/content_only_for_subscribers.tpl',
                        array(
                            'logged' => $isLogged,
                            'id'     => $content->id
                        )
                    );
                    $content->body = $newContent;
                }
            } else {
                $content->body = $newContent;
            }
        }
    }
}
