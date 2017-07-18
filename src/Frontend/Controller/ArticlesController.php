<?php
/**
 * Defines the frontend controller for the article content type
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\StringUtils;
use Onm\Settings as s;

/**
 * Defines the frontend controller for the articles content type
 *
 * @package Frontend_Controllers
 */
class ArticlesController extends Controller
{
    /**
     * Displays the article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function showAction(Request $request)
    {
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
        $urlSlug      = $request->query->filter('slug', '', FILTER_SANITIZE_STRING);

        $article = $this->get('content_url_matcher')
            ->matchContentUrl('article', $dirtyID, $urlSlug, $categoryName);

        if (empty($article)) {
            throw new ResourceNotFoundException();
        }

        // If external link is set, redirect
        if (isset($article->params['bodyLink']) && !empty($article->params['bodyLink'])) {
            // TODO: Remove when target="_blank"' not included in URI for external
            $url = str_replace('" target="_blank', '', $article->params['bodyLink']);

            return $this->redirect($url);
        }

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->get('core.user'));
        $cacheable = $subscriptionFilter->subscriptionHook($article);

        // Advertisements for single article NO CACHE
        $this->ccm  = \ContentCategoryManager::get_instance();
        $actualCategoryId = $this->ccm->get_id($categoryName);
        list($positions, $advertisements) = $this->getAds($actualCategoryId);

        // Fetch general layout
        $layout = $this->get('setting_repository')->get('frontpage_layout_'.$actualCategoryId);
        if (empty($layout)) {
            $layout = 'default';
        }
        $layoutFile = 'layouts/'.$layout.'.tpl';
        $this->view->assign('layoutFile', $layoutFile);

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $article->id);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached("extends:{$layoutFile}|article/article.tpl", $cacheID)
        ) {
            // Categories code -------------------------------------------
            // TODO: Seems that this is rubbish, evaluate its removal
            $actualCategoryTitle = $this->ccm->getTitle($categoryName);
            $categoryData        = null;
            if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $this->ccm->categories)) {
                $categoryData = $this->ccm->categories[$actualCategoryId];
            }

            $this->view->assign([
                'category_name'         => $categoryName,
                'actual_category_title' => $actualCategoryTitle,
                'actual_category_id'    => $actualCategoryId,
                'category_data'         => $categoryData,
            ]);

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
            $relatedContents = [];
            $relations       = $this->get('related_contents')->getRelations($article->id, 'inner');
            if (count($relations) > 0) {
                $contentObjects = $this->get('entity_repository')->findMulti($relations);

                // Filter out not ready for publish contents.
                foreach ($contentObjects as $content) {
                    if ($content->isReadyForPublish()) {
                        $content->category_name = $this->ccm->getName($content->category);
                        if ($content->content_type == 1 && !empty($content->img1)) {
                            $content->photo = $er->find('Photo', $content->img1);
                        } elseif ($content->content_type == 1 && !empty($content->fk_video)) {
                            $content->video = $er->find('Video', $content->fk_video);
                        }
                        $relatedContents[] = $content;
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
        }

        return $this->render("extends:{$layoutFile}|article/article.tpl", [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'cache_id'        => $cacheID,
            'contentId'       => $article->id,
            'category_name'   => $categoryName,
            'article'         => $article,
            'content'         => $article,
            'actual_category' => $categoryName,
            'time'            => '12345',
            'x-tags'          => 'article,'.$article->id,
            'x-cache-for'     => '+1 day',
            'x-cacheable'     => $cacheable,
        ]);
    }

    /**
     * Displays the external article given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function extShowAction(Request $request)
    {
        // Fetch HTTP variables
        $dirtyID      = $request->query->filter('article_id', '', FILTER_SANITIZE_STRING);
        $categoryName = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);

        // Get sync params
        $wsUrl = $this->get('core.helper.instance_sync')->getSyncUrl($categoryName);
        if (empty($wsUrl)) {
            throw new ResourceNotFoundException();
        }

        $cm = new \ContentManager;

        // Get full article
        $article = $cm->getUrlContent($wsUrl.'/ws/articles/complete/'.$dirtyID, true);
        if (is_string($article)) {
            $article = @unserialize($article);
        }

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('sync', 'content', $dirtyID);

        if ($article->content_status != 1
            || $article->in_litter == 1
            || !$article->isStarted()
        ) {
            throw new ResourceNotFoundException();
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('article/article.tpl', [
            'actual_category_title' => $article->category_title,
            'ads_positions'         => $positions,
            'advertisements'        => $advertisements,
            'article'               => $article,
            'cache_id'              => $cacheID,
            'category_name'         => $categoryName,
            'content'               => $article,
            'contentId'             => $article->id,// Used on module_comments.tpl
            'ext'                   => 1,
            'photoInt'              => $article->photoInt,
            'relationed'            => $article->relatedContents,
            'suggested'             => $article->suggested,
            'videoInt'              => $article->videoInt,
            'x-cache-for'           => '+1 day',
            'x-tags'                => 'ext-article,'.$article->id
        ]);
    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return array the list of advertisements for this page
     *
     * TODO: Make this function non-static
     */
    public static function getAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home'))? 0: $category;

        // TODO: Use $this->get when the function changes to non-static
        $positionManager = getService('core.helper.advertisement');
        $positions       = $positionManager->getPositionsForGroup('article_inner', [ 7 ]);
        $advertisements  = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
    }
}
