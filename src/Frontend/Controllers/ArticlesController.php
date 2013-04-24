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
namespace Frontend\Controllers;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
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
        $slug         = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        // Resolve article ID
        $articleID = \Content::resolveID($dirtyID);
        if (empty($articleID)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        // Load config
        $this->view->setConfig('articles');

        $cm = new \ContentManager();

        // Advertisements for single article NO CACHE
        $actualCategoryId    = $this->ccm->get_id($categoryName);
        $this->getInnerAds($actualCategoryId);

        $cacheID = $this->view->generateCacheId($categoryName, null, $articleID);

        $layout = s::get('frontpage_layout_'.$actualCategoryId, 'default');
        $layoutFile = 'layouts/'.$layout.'.tpl';

        $this->view->assign('layoutFile', $layoutFile);

        if (($this->view->caching == 0)
            || !$this->view->isCached("extends:{$layoutFile}|article/article.tpl", $cacheID)
        ) {
            $article = new \Article($articleID);

            if (($article->available == 1) && ($article->in_litter == 0)
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

                $this->view->assign(
                    array(
                        'article' => $article,
                        'content' => $article,
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
                    foreach ($relatedContents as &$content) {
                        $content->category_name =
                            $this->ccm->get_category_name_by_content_id($content->id);
                    }
                }
                $this->view->assign('relationed', $relatedContents);

                // Machine suggested contents code -----------------------------
                $machineSuggestedContents = array();
                if (!empty($article->metadata)) {

                    $objSearch    = \cSearch::getInstance();
                    $machineSuggestedContents =
                        $objSearch->searchSuggestedContents(
                            $article->metadata,
                            'Article',
                            "pk_fk_content_category= ".$article->category.
                            " AND contents.available=1 AND pk_content = pk_fk_content",
                            4
                        );
                    $machineSuggestedContents =
                        $cm->getInTime($machineSuggestedContents);
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
        $advertisement = \Advertisement::getInstance();
        $ads  = unserialize($cm->getUrlContent($wsUrl.'/ws/ads/article/'.$wsActualCategoryId, true));
        $intersticial = $ads[0];
        $banners      = $ads[1];

        // Render advertisements
        if (!empty($banners)) {
            $advertisement->renderMultiple($banners, $advertisement, $wsUrl);
        }
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement, $wsUrl);
        }

        // Cached article logic
        if ($this->view->caching == 0
            || !$this->view->isCached('article/article.tpl', $cacheID)
        ) {
            // Get full article
            $article = $cm->getUrlContent($wsUrl.'/ws/articles/complete/'.$dirtyID, true);
            $article = unserialize($article);

            if (($article->available==1) && ($article->in_litter==0)
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

        } // end if $this->view->is_cached

        return $this->render(
            'article/article.tpl',
            array(
                'cache_id' => $cacheID,
                'category_name' => $categoryName,
            )
        );
    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return void
     **/
    public static function getInnerAds($category = 'home')
    {
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        $positions = array(101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 191, 192, 193);

        $advertisement = \Advertisement::getInstance();
        $banners = $advertisement->getAdvertisements($positions, $category);

        if (count($banners<=0)) {
            $cm = new \ContentManager();
            $banners = $cm->getInTime($banners);
            //$advertisement->renderMultiple($banners, &$tpl);
            $advertisement->renderMultiple($banners, $advertisement);
        }
        // Get intersticial banner,1,2,9,10
        $intersticial = $advertisement->getIntersticial(150, $category);
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }
    }
}
