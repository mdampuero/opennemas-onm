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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FrontendController extends Controller
{
    /**
     * The list of configuration ids for Smarty cache per action.
     *
     * @var array
     */
    protected $caches = [];

    /**
     * {@inheritdoc}
     */
    protected $params = [
        'x-cache-for' => '+1 day',
        'x-cacheable' => true
    ];

    /**
     * The list of valid query parameters per action.
     *
     * @var array
     */
    protected $queries = [
        'list' => [ 'page' ]
    ];

    /**
     * Displays a frontpage basing on the parameters in the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function listAction(Request $request)
    {
        $action = $this->get('core.globals')->getAction();
        $query  = $this->parseQuery($this->getQueryParameters($action, $request));
        $route  = $this->getRoute($action);

        $expected = $this->get('router')->generate($route, $query);

        if ($request->getRequestUri() !== $expected) {
            return new RedirectResponse($expected);
        }

        $params = $this->getParameters($request);

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrate($params);
        }

        return $this->render($this->getTemplate($action), $params);
    }

    /**
     * Displays a content basing on the parameters in the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function showAction(Request $request)
    {
        $action = $this->get('core.globals')->getAction();
        $item   = $this->getItem($request);

        $expected = $this->get('core.helper.url_generator')->generate($item);

        if (empty($item) || !$item->isReadyForPublish()) {
            throw new ResourceNotFoundException();
        }

        if ($request->getRequestUri() !== $expected
            && empty($this->get('request_stack')->getParentRequest())
        ) {
            return new RedirectResponse($expected);
        }

        $params = $this->getParameters($request, $item);

        if ($this->hasExternalLink($params)) {
            return new RedirectResponse($this->getExternalLink($params));
        }

        if ($this->hasSubscription($params)
            && $this->isBlocked($this->getSubscriptionToken($params))
        ) {
            throw new AccessDeniedException();
        }

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrate($params, $item);
        }

        return $this->render(
            $this->getTemplate($this->get('core.globals')->getAction()),
            $params
        );
    }

    /**
     * Returns the cache configuration name basing on the action name.
     *
     * @param string $action The action name.
     *
     * @return string The cache configuration name.
     */
    protected function getCacheConfiguration($action)
    {
        return array_key_exists($action, $this->caches)
            ? $this->caches[$action]
            : $this->get('core.globals')->getExtension() . '-' . $action;
    }

    /**
     * Returns the cache id basing on the list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return string The cache id.
     */
    protected function getCacheId($params)
    {
        if (array_key_exists('o_content', $params)) {
            return $this->view->getCacheId(
                'content',
                $params['o_content']->id,
                $params['o_token']
            );
        }

        return $this->view->getCacheId(
            $this->get('core.globals')->getExtension(),
            $this->get('core.globals')->getAction()
        );
    }

    /**
     * Returns the category basing on the name included in the request URI.
     *
     * @param string $name The category name.
     *
     * @return Category The category.
     */
    protected function getCategory($name)
    {
        try {
            $category = $this->get('orm.manager')->getRepository('Category')
                ->findOneBy(sprintf('name = "%s"', $name));

            $category->title = $this->get('data.manager.filter')
                ->set($category->title)
                ->filter('localize')
                ->get();

            return $category;
        } catch (EntityNotFoundException $e) {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Returns the external link from the list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return mixed The external link if present in the list of parameters or
     *               null if not present in the list of parameters.
     */
    protected function getExternalLink($params)
    {
        return array_key_exists('o_external_link', $params)
            ? $params['o_external_link']
            : null;
    }

    /**
     * Returns the item id basing on the parameters in the current request.
     *
     * @param Request $request The request object.
     *
     * @return integer The item id.
     */
    protected function getIdFromRequest($request)
    {
        return (int) $request->get('id');
    }

    /**
     * Returns a content basing on the parameters in the current request and
     * the current controller.
     *
     * @param Request $request The request object.
     *
     * @return Content The content.
     */
    protected function getItem(Request $request)
    {
        return $this->get('entity_repository')->find(
            \classify($this->get('core.globals')->getExtension()),
            $this->getIdFromRequest($request)
        );
    }

    /**
     * Returns the list of parameters basing on an item and the current request.
     *
     * @param Content $item    An item.
     * @param Request $request The current request.
     *
     * @return array The list of parameters.
     */
    protected function getParameters($request, $item = null)
    {
        $params = array_merge($request->query->all(), [
            'o_category' => null,
            'x-tags'     => $this->get('core.globals')->getExtension()
        ]);

        if (!empty($item)) {
            $params['o_token'] = $this->get('core.helper.subscription')
                ->getToken($item);

            $params['x-tags'] .= ',' . $item->id;

            $params['content']     = $item;
            $params['contentId']   = $item->id;
            $params['o_content']   = $item;
            $params['x-cacheable'] = empty($params['o_token']);
        }

        if (array_key_exists('category_name', $params)) {
            $params['o_category'] = $this->getCategory($params['category_name']);
        }

        // TODO: Clean this ASAP
        if (array_key_exists('o_category', $params)
            && !empty($params['o_category'])
        ) {
            $params = array_merge($params, [
                'actual_category'       => $params['o_category']->name,
                'actual_category_id'    => $params['o_category']->pk_content_category,
                'actual_category_title' => $params['o_category']->title,
                'category_data'         => $params['o_category'],
                'category_name'         => $params['o_category']->name,
            ]);
        }

        list($positions, $advertisements) =
            $this->getAdvertisements($params['o_category']);

        return array_merge($this->params, $params, [
            'cache_id'       => $this->getCacheId($params),
            'ads_positions'  => $positions,
            'advertisements' => $advertisements
        ]);
    }

    /**
     * Returns the list of valid query parameters from the request for the
     * provided action.
     *
     * @param string  $action  The action name.
     * @param Request $request The current request.
     *
     * @return array The list of valid parameters.
     */
    protected function getQueryParameters($action, Request $request)
    {
        return array_key_exists($action, $this->queries)
            ? array_intersect_key(
                $request->query->all(),
                array_flip($this->queries[$action])
            ) : [];
    }

    /**
     * Returns the subscription token from the list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return mixed The subscrption token if present in the list of parameters
     *               or null if not present in the list of parameters.
     */
    protected function getSubscriptionToken($params)
    {
        return array_key_exists('o_token', $params) ? $params['o_token'] : null;
    }

    /**
     * Checks if there is a non-empty external link in the list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return boolean True if there is a non-empty external link in the list
     *                 of parameters. False otherwise.
     */
    protected function hasExternalLink($params)
    {
        return !empty($this->getExternalLink($params));
    }

    /**
     * Checks if there is a subscription token in the list of parameters.
     *
     * @param array $params The list of parameters.
     *
     * @return boolean True if there is a non-empty subscription token in the
     *                 list of parameters. False otherwise.
     */
    protected function hasSubscription($params)
    {
        return !empty($this->getSubscriptionToken($params));
    }

    /**
     * Updates the list of parameters and/or the item when the response for
     * the current request is not cached.
     */
    protected function hydrate()
    {
    }

    /**
     * Checks if the current content is blocked basing on the provided token.
     *
     * @param string $token The subscription token.
     *
     * @return boolean True if the content is blocked. False otherwise.
     */
    protected function isBlocked($token)
    {
        return $this->get('core.helper.subscription')
            ->isBlocked($token, 'access');
    }

    /**
     * Checks if the response for the current request is already cached basing
     * on all parameters provided.
     *
     * @param array $params The list of parameters.
     *
     * @return boolean True if the response is already cached. False otherwise.
     */
    protected function isCached($params)
    {
        return array_key_exists('cache_id', $params)
            && !empty($this->view->getCaching())
            && $this->view->isCached(
                $this->getTemplate($this->get('core.globals')->getAction()),
                $params['cache_id']
            );
    }

    /**
     * Parses all query parameters.
     *
     * @param array $query The list of query parameters.
     *
     * @return array The parsed query parameters
     */
    protected function parseQuery($query)
    {
        if (array_key_exists('page', $query)) {
            $query['page'] = (int) $query['page'];

            if ($query['page'] < 2) {
                unset($query['page']);
            }
        }

        return $query;
    }
}
