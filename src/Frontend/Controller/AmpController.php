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

/**
 * Defines the frontend controller for Amp HTML content
 *
 * @package Frontend_Controllers
 **/
class AmpController extends Controller
{
    /**
     * Load site configuration before executing the action
     **/
    public function init()
    {
        // RenderColorMenu
        $siteColor = '#005689';
        $configColor = getService('setting_repository')->get('site_color');
        if (!empty($configColor)) {
            if (!preg_match('@^#@', $configColor)) {
                $siteColor = '#'.$configColor;
            } else {
                $siteColor = $configColor;
            }
        }

        $this->view = new \Template(TEMPLATE_USER);
        $this->view->assign('site_color', $siteColor);
    }

    /**
     * Displays the article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     **/
    public function showAction(Request $request)
    {
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $urlSlug      = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $this->ccm  = \ContentCategoryManager::get_instance();

        $article = $this->get('content_url_matcher')
            ->matchContentUrl('article', $dirtyID, $urlSlug, $categoryName);

        if (empty($article)) {
            throw new ResourceNotFoundException();
        }

        // If external link is set, redirect
        if (isset($article->params['bodyLink']) && !empty($article->params['bodyLink'])) {
            return $this->redirect($article->params['bodyLink']);
        }

        // Load config
        $this->view->setConfig('articles');

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable = $subscriptionFilter->subscriptionHook($article);

        // Advertisements for single article NO CACHE
        $actualCategoryId = $this->ccm->get_id($categoryName);
        $ads = $this->getAds($actualCategoryId);
        $this->view->assign('advertisements', $ads);

        $cacheID = $this->view->generateCacheId($categoryName, null, $article->id);
        if ($this->view->caching == 0
            || !$this->view->isCached("amp/article.tpl", $cacheID)
        ) {
            // Categories code -------------------------------------------
            // TODO: Seems that this is rubbish, evaluate its removal
            $actualCategoryTitle = $this->ccm->getTitle($categoryName);
            $categoryData        = null;
            if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $this->ccm->categories)) {
                $categoryData = $this->ccm->categories[$actualCategoryId];
            }

            $this->view->assign(
                array(
                    'category_name'         => $categoryName,
                    'actual_category_title' => $actualCategoryTitle,
                    'actual_category_id'    => $actualCategoryId,
                    'category_data'         => $categoryData,
                )
            );

            // Associated media code --------------------------------------
            $er = $this->get('entity_repository');
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
            $relationIDs = getService('related_contents')->getRelationsForInner($article->id);
            $relatedContents = [];
            if (count($relationIDs) > 0) {
                $cm = new \ContentManager;
                $relatedContents = $cm->getContents($relationIDs);

                // Drop contents that are not available or not in time
                $relatedContents = $cm->getInTime($relatedContents);
                $relatedContents = $cm->getAvailable($relatedContents);

                // Get front media element and add category name
                foreach ($relatedContents as $key => &$content) {
                    $content->category_name = $this->ccm->getCategoryNameByContentId($content->id);
                    if ($key == 0 && $content->content_type == 1 && !empty($content->img1)) {
                        $content->photo = $er->find('Photo', $content->img1);
                    } elseif ($key == 0 && $content->content_type == 1 && !empty($content->fk_video)) {
                        $content->video = $er->find('Video', $content->fk_video);
                    }
                }
            }
            $this->view->assign('relationed', $relatedContents);

        } // end if $this->view->is_cached

        return $this->render(
            "amp/article.tpl",
            [
                'cache_id'        => $cacheID,
                'contentId'       => $article->id,
                'category_name'   => $categoryName,
                'article'         => $article,
                'content'         => $article,
                'actual_category' => $categoryName,
                'time'            => '12345',
                'x-tags'          => 'article-amp,article,'.$article->id,
                'x-cache-for'     => '+1 day',
                'x-cacheable'     => $cacheable
            ]
        );
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

        return \Advertisement::findForPositionIdsAndCategory([1051], $category);
    }
}
