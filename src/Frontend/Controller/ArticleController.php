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

        list($article, $related) = unserialize($cm->getUrlContent(
            $wsUrl . '/ws/articles/complete/' . $dirtyID,
            true
        ));

        if (empty($article)) {
            throw new ResourceNotFoundException();
        }

        $this->view->assign([
            'article'   => $article,
            'item'      => $article,
            'content'   => $article,
            'related'   => $related,
            'o_content' => $article,
        ]);

        // Setup templating cache layer
        $this->view->setConfig('articles');
        $cacheID = $this->view->getCacheId('sync', 'content', $dirtyID);

        if (!$this->get('core.helper.content')->isReadyForPublish($article)) {
            throw new ResourceNotFoundException();
        }

        $this->getAdvertisements();

        return $this->render('article/article.tpl', [
            'cache_id'    => $cacheID,
            'ext'         => 1,
            'x-cacheable' => true,
            'x-tags'      => 'ext-article,' . $article->id
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getParameters($params, $item = null)
    {
        $params = parent::getParameters($params, $item);

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
        $params['suggested'] = $this->get('core.helper.content')->getSuggested(
            $params['content']->pk_content,
            'article',
            $params['o_category']->id
        );
    }
}
