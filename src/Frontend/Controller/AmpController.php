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

/**
 * Defines the frontend controller for Amp HTML content
 */
class AmpController extends Controller
{
    /**
     * Load site configuration before executing the action
     **/
    public function init()
    {
        if (!$this->get('core.security')->hasExtension('AMP_MODULE')) {
            throw new ResourceNotFoundException();
        }

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
        $actualCategoryId = $this->ccm->get_id($categoryName);

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

        // Avoid NewRelic js script
        if (extension_loaded('newrelic')) {
            newrelic_disable_autorum();
        }

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable = $subscriptionFilter->subscriptionHook($article);

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $article->id, 'amp');

        if ($this->view->getCaching() === 0
            || !$this->view->isCached("amp/article.tpl", $cacheID)
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
            $relatedContents  = [];
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

            $patterns = [
                '@(align|border|style|nowrap|onclick)="[^\"]*\"@',
                '@<font.*?>((?s).*)<\/font>@',
                '@<img([^>]+>)@',
                '@<iframe.*src="[http:|https:]*(.*?)".*><\/iframe>@',
                '@<div.*?class="fb-(post|video)".*?data-href="([^"]+)".*?>(?s).*?<\/div>@',
                '@<blockquote.*?class="instagram-media"(?s).*?href=".*?(\.com|\.am)\/p\/(.*?)\/"[^>]+>(?s).*?<\/blockquote>@',
                '@<blockquote.*?class="twitter-(video|tweet)"(?s).*?\/status\/(\d+)(?s).+?<\/blockquote>@',
                '@<(script|embed|object|frameset|frame|iframe|style|form)[^>]*>(?s).*?<\/\1>@',
                '@<(link|meta|input)[^>]+>@',
            ];
            $replacements  = [
                '',
                '${1}',
                '<amp-img layout="responsive" width="518" height="291" ${1} </amp-img>',
                '<amp-iframe width=300 height=300
                    sandbox="allow-scripts allow-same-origin allow-popups allow-popups-to-escape-sandbox allow-forms"
                    layout="responsive"
                    frameborder="0"
                    src="https:${1}">
                </amp-iframe>',
                '<amp-facebook width=486 height=657
                    layout="responsive"
                    data-embed-as="${1}"
                    data-href="${2}">
                </amp-facebook>',
                '<amp-instagram
                    data-shortcode="${2}"
                    width="400"
                    height="400"
                    layout="responsive">
                </amp-instagram>',
                '<amp-twitter width=486 height=657
                    layout="responsive"
                    data-tweetid="${2}">
                </amp-twitter>',
                '',
                ''
            ];
            $article->body = preg_replace($patterns, $replacements, $article->body);
            $article->summary = preg_replace($patterns, $replacements, $article->summary);
        } // end if $this->view->is_cached

        // Get instance logo size
        $logo = getService('setting_repository')->get('site_logo');
        if (!empty($logo)) {
            $logoUrl = SITE_URL.MEDIA_DIR_URL.'sections/'.rawurlencode($logo);
            $logoSize = @getimagesize($logoUrl);
            if (is_array($logoSize)) {
                $this->view->assign([
                    'logoSize' => $logoSize,
                    'logoUrl'  => $logoUrl
                ]);
            }
        }

        $advertisements = $this->getAds($actualCategoryId);

        return $this->render("amp/article.tpl", [
            'advertisements'  => $advertisements,
            'contentId'       => $article->id,
            'category_name'   => $categoryName,
            'article'         => $article,
            'content'         => $article,
            'actual_category' => $categoryName,
            'time'            => '12345',
            'cache_id'        => $cacheID,
            'x-tags'          => 'article-amp,article,'.$article->id,
            'x-cache-for'     => '+1 day',
            'x-cacheable'     => $cacheable
        ]);
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

        return getService('advertisement_repository')
            ->findByPositionsAndCategory([1051, 1052, 1053], $category);
    }
}
