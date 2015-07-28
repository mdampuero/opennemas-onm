<?php
/**
 * Defines the frontend controller for the article content type
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
use Onm\Settings as s;

/**
 * Defines the frontend controller for the articles content type
 *
 * @package Frontend_Controllers
 **/
class ArticlesController extends Controller
{
    /**
     * Displays the article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        $this->ccm  = \ContentCategoryManager::get_instance();

        // Resolve article ID, search in repository or redirect to 404
        $articleID = \ContentManager::resolveID($dirtyID);
        $er        = $this->get('entity_repository');
        $article   = $er->find('Article', $articleID);
        if (is_null($article)) {
            throw new ResourceNotFoundException();
        }

        // If external link is set, redirect
        if (isset($article->params['bodyLink']) && !empty($article->params['bodyLink'])) {
            return $this->redirect($article->params['bodyLink']);
        }

        // Load config
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('articles');

        $cacheable = $this->paywallHook($article);

        // Advertisements for single article NO CACHE
        $actualCategoryId    = $this->ccm->get_id($categoryName);
        $ads = $this->getAds($actualCategoryId);
        $this->view->assign('advertisements', $ads);

        // Fetch general layout
        $layout = $this->get('setting_repository')->get('frontpage_layout_'.$actualCategoryId, 'default');
        $layoutFile = 'layouts/'.$layout.'.tpl';
        $this->view->assign('layoutFile', $layoutFile);

        $cacheID = $this->view->generateCacheId($categoryName, null, $articleID);
        if ($this->view->caching == 0
            || !$this->view->isCached("extends:{$layoutFile}|article/article.tpl", $cacheID)
        ) {
            if (($article->content_status == 1) && ($article->in_litter == 0)
                && ($article->isStarted())
            ) {
                // Categories code -------------------------------------------
                // TODO: Seems that this is rubbish, evaluate its removal
                $actualCategory      = $categoryName;
                $actualCategoryId    = $this->ccm->get_id($actualCategory);
                $actualCategoryTitle = $this->ccm->getTitle($actualCategory);
                $categoryData        = null;
                if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $this->ccm->categories)) {
                    $categoryData = $this->ccm->categories[$actualCategoryId];
                }

                $this->view->assign(
                    array(
                        'category_name'         => $actualCategory ,
                        'actual_category_title' => $actualCategoryTitle,
                        'actual_category_id'    => $actualCategoryId,
                        'category_data'         => $categoryData,
                    )
                );

                // Associated media code --------------------------------------
                if (isset($article->img2) && ($article->img2 > 0)) {
                    $photoInt = $er->find('Photo', $article->img2);
                    $this->view->assign('photoInt', $photoInt);
                }

                if (isset($article->fk_video2) && ($article->fk_video2 > 0)) {
                    $videoInt = $er->find('Video', $article->fk_video2);
                    $this->view->assign('videoInt', $videoInt);
                }

                $article->media_url = '';
                if (is_object($article->author)) {
                    $article->author->getPhoto();
                }

                // Related contents code ---------------------------------------
                $relationIDs = getService('related_contents')->getRelationsForInner($articleID);
                $relatedContents = [];
                if (count($relationIDs) > 0) {
                    $cm = new \ContentManager;
                    $relatedContents = $cm->getContents($relationIDs);

                    // Drop contents that are not available or not in time
                    $relatedContents = $cm->getInTime($relatedContents);
                    $relatedContents = $cm->getAvailable($relatedContents);

                    // Add category name
                    foreach ($relatedContents as $key => &$content) {
                        $content->category_name = $this->ccm->getCategoryNameByContentId($content->id);
                        if ($key == 0 && $content->content_type == 1 && !empty($content->img1)) {
                            $content->photo = $er->find('Photo', $content->img1);
                        }
                    }
                }
                $this->view->assign('relationed', $relatedContents);

                // Machine suggested contents code -----------------------------
                $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
                    'article',
                    "category_name= '".$article->category_name."' AND pk_content <>".$article->id,
                    4
                );

                $this->view->assign('suggested', $machineSuggestedContents);
            } else {
                throw new ResourceNotFoundException();
            }

        } // end if $this->view->is_cached

        $renderParams = [
            'cache_id'        => $cacheID,
            'contentId'       => $articleID,
            'category_name'   => $categoryName,
            'article'         => $article,
            'content'         => $article,
            'actual_category' => $categoryName,
            'time'            => '12345',
        ];

        if ($cacheable) {
            $renderParams = array_merge(
                $renderParams,
                [
                    'x-tags'      => 'article,'.$article->id,
                    'x-cache-for' => '1d'
                ]
            );
        }

        return $this->render(
            "extends:{$layoutFile}|article/article.tpl",
            $renderParams
        );
    }

    /**
     * Displays the external article given its id
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

        // Setup view
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('articles');
        $cacheID = $this->view->generateCacheId('sync'.$categoryName, null, $dirtyID);

        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        if ($syncParams) {
            foreach ($syncParams as $siteUrl => $values) {
                if (in_array($categoryName, $values['categories'])) {
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

        $cacheable = true;

        if ($paywallActivated && $onlyAvailableSubscribers) {
            $newContent = $this->renderView(
                'paywall/partials/content_only_for_subscribers.tpl',
                array('id' => $content->id)
            );

            $isLogged = isset($_SESSION) && is_array($_SESSION) && array_key_exists('userid', $_SESSION);
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

            $cacheable = false;
        }

        return $cacheable;
    }
}
