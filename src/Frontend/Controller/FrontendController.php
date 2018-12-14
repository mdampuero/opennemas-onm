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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FrontendController extends Controller
{
    /**
     * {@inheritdoc}
     */
    protected $params = [
        'x-cache-for' => '+1 day',
        'x-cacheable' => true
    ];

    /**
     * Displays a content basing on the parameters in the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function showAction(Request $request)
    {
        $ugh  = $this->get('core.helper.url_generator');
        $item = $this->getItem($request);

        if (empty($item)) {
            throw new ResourceNotFoundException();
        }

        if (!$ugh->isValid($item, $request->getRequestUri())) {
            return new RedirectResponse($ugh->generate($item));
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

        if (!$this->isCached($params)) {
            $this->hydrate($params, $item);
        }

        return $this->render($this->getTemplate($params), $params);
    }

    /**
     * Returns the cache id basing on the action and an item.
     *
     * @return string The cache id.
     */
    protected function getCacheId()
    {
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
            'cache_id'   => $this->getCacheId($item),
            'content'    => $item,
            'o_content'  => $item,
            'o_category' => null,
            'x-tags'     => $this->get('core.globals')->getExtension()
        ]);

        if (!empty($item)) {
            $params['contentId'] = $item->id;
            $params['x-tags']    = $params['x-tags'] . ',' . $item->id;
            $params['o_token']   = $this->get('core.helper.subscription')
                ->getToken($item);

            $params['x-cacheable'] = empty($params['o_token']);
        }

        if (array_key_exists('category_name', $params)) {
            $params['o_category'] = $this->getCategory($params['category_name']);
        }

        // TODO: Clean this ASAP
        if (!empty($params['o_category'])) {
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
            'ads_positions'  => $positions,
            'advertisements' => $advertisements
        ]);
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
     * {@inheritdoc}
     */
    protected function getTemplate($params = [])
    {
        if (!array_key_exists('o_layout', $params)
            || empty($params['o_layout'])
        ) {
            return parent::getTemplate($params);
        }

        return sprintf(
            'extends:layouts/%s.tpl|%s',
            $params['o_layout'],
            parent::getTemplate($params)
        );
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
                $this->getTemplate($params),
                $params['cache_id']
            );
    }
}
