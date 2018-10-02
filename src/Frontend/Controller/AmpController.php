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

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Defines the frontend controller for Amp HTML content
 */
class AmpController extends Controller
{
    /**
     * Load site configuration before executing the action
     */
    public function init()
    {
        if (!$this->get('core.security')->hasExtension('AMP_MODULE')) {
            throw new ResourceNotFoundException();
        }

        // RenderColorMenu
        $siteColor   = '#005689';
        $configColor = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('site_color');

        if (!empty($configColor)) {
            if (!preg_match('@^#@', $configColor)) {
                $siteColor = '#' . $configColor;
            } else {
                $siteColor = $configColor;
            }
        }

        $this->view->assign('site_color', $siteColor);
    }

    /**
     * Displays the article given its id.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
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

        // Redirect if external link is set
        if (isset($article->params['bodyLink'])
            && !empty($article->params['bodyLink'])
        ) {
            // TODO: Remove when target="_blank"' not included in URI for external
            $url = str_replace('" target="_blank', '', $article->params['bodyLink']);

            return $this->forward(
                'FrontendBundle:Redirectors:externalLink',
                [ 'to'  => $url ]
            );
        }

        // Avoid NewRelic js script
        if (extension_loaded('newrelic')) {
            newrelic_disable_autorum();
        }

        $sh = $this->get('core.helper.subscription');

        $token = $sh->getToken($article);
        $this->view->assign('token', $token);

        if ($sh->isBlocked($token, 'access')) {
            throw new AccessDeniedException();
        }

        try {
            $category = $this->get('orm.manager')->getRepository('Category')
                ->findOneBy(sprintf('name = "%s"', $categoryName));

            $category->title = $this->get('data.manager.filter')
                ->set($category->title)
                ->filter('localize')
                ->get();
        } catch (EntityNotFoundException $e) {
            throw new ResourceNotFoundException();
        }

        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $article->id, 'amp');

        if ($this->view->getCaching() === 0
            || !$this->view->isCached("amp/article.tpl", $cacheID)
        ) {
            $em = $this->get('entity_repository');

            if (isset($article->img2) && ($article->img2 > 0)) {
                $photoInt = $em->find('Photo', $article->img2);
                $this->view->assign('photoInt', $photoInt);
            }

            if (isset($article->fk_video2) && ($article->fk_video2 > 0)) {
                $videoInt = $em->find('Video', $article->fk_video2);
                $this->view->assign('videoInt', $videoInt);
            }

            $this->view->assign('relationed', $this->getRelated($article));

            $patterns = [
                '@(align|border|style|nowrap|onclick)=(\'|").*?(\'|")@',
                '@<\/?font.*?>@',
                '@<img\s+[^>]*src\s*=\s*"([^"]+)"[^>]*>@',
                '@<video([^>]+>)(?s)(.*?)<\/video>@',
                '@<iframe.*src="[http:|https:]*(.*?)".*><\/iframe>@',
                '@<div.*?class="fb-(post|video)".*?data-href="([^"]+)".*?>(?s).*?<\/div>@',
                '@<blockquote.*?class="instagram-media"(?s).*?href=".*?'
                    . '(\.com|\.am)\/p\/(.*?)\/"[^>]+>(?s).*?<\/blockquote>@',
                '@<blockquote.*?class="twitter-(video|tweet)"(?s).*?\/status\/(\d+)(?s).+?<\/blockquote>@',
                '@<(script|embed|object|frameset|frame|iframe|style|form)[^>]*>(?s).*?<\/\1>@',
                '@<(link|meta|input)[^>]+>@',
                '@<a\s+[^>]*href\s*=\s*"([^"]+)"[^>]*>@',
                '@<(table|tbody|blockquote|th|tr|td|ul|li|ol|dl|p|strong|br|span'
                    . '|div|b|pre|hr|col|h1|h2|h3|h4|h5|h6)[^>]*?(\/?)>@',
                '@target="(.*?)"@',
            ];

            $replacements = [
                '',
                '',
                '<amp-img layout="responsive" width="518" height="291" src="${1}"></amp-img>',
                '<amp-video layout="responsive" width="518" height="291" controls>
                    ${2}
                    <div fallback>
                        <p>This browser does not support the video element.</p>
                    </div>
                </amp-video>',
                '<amp-iframe width=518 height=291
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
                '',
                '<a href="${1}">',
                '<${1}${2}>',
                'target="_blank"',
            ];

            $article->body    = preg_replace($patterns, $replacements, $article->body);
            $article->summary = preg_replace($patterns, $replacements, $article->summary);
        } // end if $this->view->is_cached

        // Get instance logo size
        $logo = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('site_logo');
        if (!empty($logo)) {
            $logoUrl  = SITE_URL . MEDIA_DIR_URL . 'sections/' . rawurlencode($logo);
            $logoSize = @getimagesize($logoUrl);
            if (is_array($logoSize)) {
                $this->view->assign([
                    'logoSize' => $logoSize,
                    'logoUrl'  => $logoUrl
                ]);
            }
        }

        $advertisements = $this->getAds($category->pk_content_category);

        return $this->render('amp/article.tpl', [
            'actual_category'       => $category->name,
            'actual_category_id'    => $category->pk_content_category,
            'actual_category_title' => $category->title,
            'advertisements'        => $advertisements,
            'article'               => $article,
            'cache_id'              => $cacheID,
            'category_data'         => $category,
            'category_name'         => $category->name,
            'content'               => $article,
            'contentId'             => $article->id,
            'render_params'         => ['ads-format' => 'amp'],
            'time'                  => '12345',
            'o_content'             => $article,
            'x-cache-for'           => '+1 day',
            'x-cacheable'           => empty($token),
            'x-tags'                => 'article-amp,article,' . $article->id,
            'tags'                  => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($article->tag_ids)['items']
        ]);
    }

    /**
     * Fetches advertisements for article inner
     *
     * @param string category the category identifier
     *
     * @return array the list of advertisements for this page
     */
    public static function getAds($category = 'home')
    {
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        $positions = getService('core.helper.advertisement')
            ->getPositionsForGroup('amp_inner', [1051, 1052, 1053]);

        return getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);
    }

    /**
     * Returns the list of related contents for an article.
     *
     * @param Article $article The article object.
     *
     * @return array The list of rellated contents.
     */
    private function getRelated($article)
    {
        $relations = $this->get('related_contents')
            ->getRelations($article->id, 'inner');

        if (empty($relations)) {
            return [];
        }

        $em = $this->get('entity_repository');

        $related  = [];
        $contents = $em->findMulti($relations);

        // Filter out not ready for publish contents.
        foreach ($contents as $content) {
            if (!$content->isReadyForPublish()) {
                continue;
            }

            if ($content->content_type == 1 && !empty($content->img1)) {
                $content->photo = $em->find('Photo', $content->img1);
            } elseif ($content->content_type == 1 && !empty($content->fk_video)) {
                $content->video = $em->find('Video', $content->fk_video);
            }

            $related[] = $content;
        }

        return $related;
    }
}
