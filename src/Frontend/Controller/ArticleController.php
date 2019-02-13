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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Defines the frontend controller for the articles.
 */
class ArticleController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    protected $caches = [
        'show'    => 'articles',
        'showamp' => 'articles',
    ];

    /**
     * {@inheritdoc}
     */
    protected $extension = 'article';

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'extShow' => 'article_inner',
        'show'    => 'article_inner',
        'showamp' => 'amp_inner',
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'article_inner' => [ 7 ]
    ];

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

        list($positions, $advertisements) = $this->getAdvertisements();

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
            'o_content'             => $article,
            'x-cache-for'           => '+1 day',
            'x-tags'                => 'ext-article,' . $article->id
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($params, $item = null)
    {
        $locale = $this->get('core.locale')->getRequestLocale();
        $params = parent::getParameters($params, $item);

        if (array_key_exists('o_category', $params) && !empty($params['o_category'])) {
            $params['o_layout'] = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')->get(
                    'frontpage_layout_' . $params['o_category']->pk_content_category,
                    'default'
                );
        }

        if (!empty($item)) {
            $params[$item->content_type_name] = $item;

            $params['tags'] = $this->get('api.service.tag')
                ->getListByIdsKeyMapped($item->tag_ids, $locale)['items'];

            if (array_key_exists('bodyLink', $item->params)) {
                $params['o_external_link'] = $item->params['bodyLink'];
            }
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow($params = [], $item = null)
    {
        $params = [
            'relationed' => $this->getRelated($item),
            'suggested'  => $this->getSuggested($item, $params['o_category'])
        ];

        $em = $this->get('entity_repository');

        if (!empty($item->img2)) {
            $params['photoInt'] = $em->find('Photo', $item->img2);
        }

        if (!empty($item->fk_video2)) {
            $params['videoInt'] = $em->find('Video', $item->fk_video2);
        }

        $this->view->assign($params);
    }

    /**
     * Returns the list of related contents for an article.
     *
     * @param Article $article The article object.
     *
     * @return array The list of rellated contents.
     */
    protected function getRelated($article)
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
    protected function getSuggested($article, $category = null)
    {
        $query = sprintf(
            'category_name = "%s" AND pk_content <> %s',
            $category->name,
            $article->id
        );

        return $this->get('automatic_contents')
            ->searchSuggestedContents('article', $query);
    }
}
