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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Defines the frontend controller for the articles.
 */
class ArticlesController extends Controller
{
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
        if (array_key_exists('bodyLink', $article->params)
            && !empty($article->params['bodyLink'])
        ) {
            // TODO: Remove when target="_blank"' not included in URI for external
            $url = str_replace('" target="_blank', '', $article->params['bodyLink']);

            return $this->forward(
                'FrontendBundle:Redirectors:externalLink',
                [ 'to'  => $url ]
            );
        }

        $sh = $this->get('core.helper.subscription');

        $token = $sh->getToken($article);

        if ($sh->isBlocked($token, 'access')) {
            throw new AccessDeniedException();
        }

        $category = $this->get('orm.manager')->getRepository('Category')
            ->findOneBy(sprintf('name = "%s"', $categoryName));

        list($positions, $advertisements) =
            $this->getAds($category->pk_content_category);

        $layout = $this->get('setting_repository')->get(
            'frontpage_layout_' . $category->pk_content_category,
            'default'
        );

        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('content', $article->id);

        if ($this->view->getCaching() === 0
            || !$this->view->isCached("extends:layouts/{$layout}.tpl|article/article.tpl", $cacheID)
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

            $this->view->assign([
                'relationed' => $this->getRelated($article),
                'suggested'  => $this->getSuggested($article, $category)
            ]);
        }

        return $this->render("extends:layouts/{$layout}.tpl|article/article.tpl", [
            'actual_category'       => $category->name,
            'actual_category_id'    => $category->pk_content_category,
            'actual_category_title' => $category->title,
            'ads_positions'         => $positions,
            'advertisements'        => $advertisements,
            'article'               => $article,
            'cache_id'              => $cacheID,
            'category_data'         => $category,
            'category_name'         => $category->name,
            'content'               => $article,
            'contentId'             => $article->id,
            'time'                  => '12345',
            'o-token'               => $token,
            'x-cache-for'           => '+1 day',
            'x-cacheable'           => empty($token),
            'x-tags'                => 'article,' . $article->id
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
        $article = $cm->getUrlContent($wsUrl . '/ws/articles/complete/' . $dirtyID, true);

        if (is_string($article)) {
            $article = @unserialize($article);
        }

        if (empty($article) ||
            (!empty($article->error) && !empty($article->error->code) && $article->error->code === 404)
        ) {
            throw new ResourceNotFoundException();
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
            'x-tags'                => 'ext-article,' . $article->id
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
        $category = (!isset($category) || ($category == 'home')) ? 0 : $category;

        // TODO: Use $this->get when the function changes to non-static
        $positions      = getService('core.helper.advertisement')
            ->getPositionsForGroup('article_inner', [ 7 ]);
        $advertisements = getService('advertisement_repository')
            ->findByPositionsAndCategory($positions, $category);

        return [ $positions, $advertisements ];
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

    /**
     * Returns the list of suggested contents for an article.
     *
     * @param Article  $article  The current article.
     * @param Category $category The article category.
     *
     * @return array The list of suggested contents.
     */
    private function getSuggested($article, $category)
    {
        $query = sprintf(
            'category_name = "%s" AND pk_content <> %s',
            $category->name,
            $article->id
        );

        return $this->get('automatic_contents')->searchSuggestedContents($query);
    }
}
