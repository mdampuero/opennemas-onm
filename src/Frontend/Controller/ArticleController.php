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
    protected $extension = 'ARTICLE_MANAGER';

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
     * {@inheritdoc}
     */
    protected $service = 'api.service.article';

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
        $categoryName = $request->query->filter('category_slug', 'home', FILTER_SANITIZE_STRING);

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

        if (!$article->isReadyForPublish()) {
            throw new ResourceNotFoundException();
        }

        list($positions, $advertisements) = $this->getAdvertisements();

        return $this->render('article/article.tpl', [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'article'        => $article,
            'cache_id'       => $cacheID,
            'content'        => $article,
            'contentId'      => $article->id,// Used on module_comments.tpl
            'ext'            => 1,
            'photoInt'       => $article->photoInt,
            'suggested'      => $article->suggested,
            'videoInt'       => $article->videoInt,
            'o_content'      => $article,
            'x-cacheable'    => true,
            'x-tags'         => 'ext-article,' . $article->id
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($params, $item = null)
    {
        $params = parent::getParameters($params, $item);

        if (array_key_exists('o_category', $params) && !empty($params['o_category'])) {
            $params['o_layout'] = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')->get(
                    'frontpage_layout_' . $params['o_category']->id,
                    'default'
                );
        }

        if (!empty($item)) {
            $params[$item->content_type_name] = $item;

            if (array_key_exists('bodyLink', $item->params)) {
                $params['o_external_link'] = $item->params['bodyLink'];
            }
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function hydrateShow(array &$params = []) : void
    {
        $suggested = $this->get('core.helper.content')->getSuggested(
            $params['content']->pk_content,
            'article',
            $params['o_category']->id
        );

        $params['tags']      = $this->getTags($params['content']);
        $params['suggested'] = $suggested[0];
        $params['photos']    = $suggested[1];

        $em = $this->get('entity_repository');

        if (!empty($params['content']->img2)) {
            $params['photoInt'] = $em->find('Photo', $params['content']->img2);
        }

        if (!empty($params['content']->fk_video2)) {
            $params['videoInt'] = $em->find('Video', $params['content']->fk_video2);
        }
    }

    /**
     * Updates the list of parameters and/or the item when the response for
     * the current request is not cached.
     *
     * @param array $params The list of parameters already in set.
     */
    protected function hydrateShowAmp(array &$params = []) : void
    {
        parent::hydrateShowAmp($params);

        $em = $this->get('entity_repository');
        if (!empty($params['content']->img2)) {
            $photoInt = $em->find('Photo', $params['content']->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        if (!empty($params['content']->fk_video2)) {
            $videoInt = $em->find('Video', $params['content']->fk_video2);
            $this->view->assign('videoInt', $videoInt);
        }
    }
}
