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

use Api\Exception\ApiException;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
     * The API service to use to return the content.
     *
     * @var string
     */
    protected $service = 'api.service.content_old';

    /**
     * The list of templates per action.
     *
     * @var array
     */
    protected $templates = [
        'showamp' => 'amp/content.tpl',
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
        $this->checkSecurity($this->extension);

        $action = $this->get('core.globals')->getAction();
        $params = $this->getQueryParameters($action, $request->query->all());

        $expected = $this->getExpectedUri($action, $params);

        if ($request->getRequestUri() !== $expected) {
            return new RedirectResponse($expected, 301);
        }

        $params = $this->getParameters($request);

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateList($params);
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
        $this->checkSecurity($this->extension);

        $action = $this->get('core.globals')->getAction();
        $item   = $this->getItem($request);

        $expected = $this->getExpectedUri($action, [ 'item' => $item ]);

        if ($request->getPathInfo() !== $expected
            && empty($this->get('request_stack')->getParentRequest())
        ) {
            return new RedirectResponse($expected, 301);
        }

        $params = $this->getParameters($request, $item);

        if ($this->hasExternalLink($params)) {
            return new RedirectResponse($this->getExternalLink($params), 301);
        }

        if ($this->hasSubscription($params)
            && $this->isBlocked($this->getSubscriptionToken($params))
        ) {
            throw new AccessDeniedException();
        }

        $this->view->setConfig($this->getCacheConfiguration($action));

        if (!$this->isCached($params)) {
            $this->hydrateShow($params);
        }

        return $this->render(
            $this->getTemplate($this->get('core.globals')->getAction()),
            $params
        );
    }

    /**
     * Displays a content amp format basing on the parameters in the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function showAmpAction(Request $request)
    {
        $this->checkSecurity($this->extension);
        $this->checkSecurity('AMP_MODULE');

        // Avoid NewRelic js script
        if (extension_loaded('newrelic')) {
            newrelic_disable_autorum();
        }

        $action = $this->get('core.globals')->getAction();
        $item   = $this->getItem($request);

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
            $this->hydrateShowAmp($params);
        }

        return $this->render(
            $this->getTemplate($this->get('core.globals')->getAction()),
            $params
        );
    }

    /**
     * Checks if the action can be executed basing on the extension and action
     * to execute.
     *
     * @param string $extension  The required extension.
     * @param string $permission The required permission.
     *
     * @throws ResourceNotFoundException If the action can not be executed.
     */
    protected function checkSecurity($extension, $permission = null)
    {
        if (!empty($extension)
            && !$this->get('core.security')->hasExtension($extension)) {
            throw new ResourceNotFoundException();
        }

        if (!empty($permission)
            && !$this->get('core.security')->hasPermission($permission)) {
            throw new ResourceNotFoundException();
        }
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
     * Returns the expire time for cache basing on the endtime of all items in
     * a list.
     *
     * @param array $items The list of items.
     *
     * @return string The expire time for cache.
     */
    protected function getCacheExpire(array $items)
    {
        $listOfExpires = array_filter($items, function ($a) {
            return !empty($a->endtime);
        });

        if (empty($listOfExpires)) {
            return null;
        }
        return min(array_map(function ($a) {
            return $a->endtime instanceof \Datetime
                ? $a->endtime->format('Y-m-d H:i:s')
                : $a->endtime;
        }, $listOfExpires));
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
        $cacheParams = [
            $this->get('core.globals')->getExtension(),
            $this->get('core.globals')->getAction(),
        ];

        if (array_key_exists('o_content', $params)) {
            $cacheParams = [
                'content',
                $params['o_content']->id,
                $params['o_token'],
                array_key_exists('_format', $params) ? $params['_format'] : null
            ];
        }

        $cacheParams = array_merge($cacheParams, array_values(
            $this->getKnownParameters(
                $this->get('core.globals')->getAction(),
                $params
            )
        ));

        return $this->view->getCacheId($cacheParams);
    }

    /**
     * Returns the canonical URL for the action basing on the current
     * parameters.
     *
     * @param string $action The current action.
     * @param array  $params The list of parameters.
     *
     * @return string The canonical URL for the action.
     */
    protected function getCanonicalUrl($action, $params)
    {
        if (array_key_exists('o_content', $params)) {
            $url = $this->get('core.helper.url_generator')
                ->generate($params['o_content'], [ 'absolute' => true ]);

            return $this->get('core.helper.l10n_route')->localizeUrl($url);
        }

        $params = $this->getKnownParameters($action, $params);
        $route  = $this->getRoute($action, $params);

        $url = $this->get('router')->generate(
            $route,
            $params,
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->get('core.helper.l10n_route')->localizeUrl($url);
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
            return $this->get('api.service.category')->getItemBySlug($name);
        } catch (ApiException $e) {
            throw new ResourceNotFoundException();
        }
    }

    /**
     * Returns the expected URI for the list action basing on the current
     * action and a list of parameters.
     *
     * @param string $action The current action.
     * @param array  $params The list of parameters.
     *
     * @return string The expected URI.
     */
    protected function getExpectedUri($action, $params = [])
    {
        if (array_key_exists('item', $params)) {
            $expected = $this->get('core.helper.url_generator')
                ->generate($params['item']);

            return $this->get('core.helper.l10n_route')->localizeUrl($expected);
        }

        $route = $this->getRoute($action, $params);

        // Do not support page=1 in query string
        if (array_key_exists('page', $params) && $params['page'] == 1) {
            unset($params['page']);
        }

        $expected = $this->get('router')->generate($route, $params);

        return $this->get('core.helper.l10n_route')->localizeUrl($expected);
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
        try {
            $item = $this->get($this->service)
                ->getItem($this->getIdFromRequest($request));
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        if (empty($item) || !$item->isReadyForPublish()) {
            throw new ResourceNotFoundException();
        }

        return $item;
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
        $action = $this->get('core.globals')->getAction();
        $params = array_merge($request->query->all(), [
            'o_category' => null,
            'o_token'    => null,
            'x-tags'     => [
                $this->get('core.globals')->getExtension(),
                $action
            ]
        ]);

        if (!empty($item)) {
            $params['content']     = $item;
            $params['contentId']   = $item->id;
            $params['item']        = $item;
            $params['o_content']   = $item;
            $params['o_token']     = $this->get('core.helper.subscription')
                ->getToken($item);

            $params['x-cacheable'] = empty($params['o_token'])
                && empty($this->get('session')->getFlashBag()->peekAll());

            $params['x-tags'][] = $item->id;

            // Ensure that all templates are using params['content'] and
            // then remove the line below
            $params[$item->content_type_name] = $item;
        }

        if (array_key_exists('category_name', $params)) {
            $params['o_category'] = $this->getCategory($params['category_name']);
            $params['category']   = $params['o_category'];
            $params['categories'] = [];
        }

        if ($action == 'showamp') {
            $params['ads_format'] = 'amp';
        }

        list($positions, $advertisements) =
            $this->getAdvertisements($params['o_category'], $params['o_token']);

        return array_merge($this->params, $params, [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'cache_id'       => $this->getCacheId($params),
            'o_canonical'    => $this->getCanonicalUrl($action, $params),
            'x-tags'         => implode(',', $params['x-tags'])
        ]);
    }

    /**
     * Returns the list of valid query parameters from the request for the
     * provided action.
     *
     * @param string $action The action name.
     * @param array  $params The list of parameters.
     *
     * @return array The list of valid parameters.
     */
    protected function getQueryParameters(string $action, array $params)
    {
        return array_merge(
            $this->getKnownParameters($action, $params),
            $this->getUnknownParameters($action, $params)
        );
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
     *
     * @param array $params The list of parameters already in set.
     */
    protected function hydrateShow(array &$params = []) : void
    {
    }

    /**
     * Updates the list of parameters and/or the item when the response for
     * the current request is not cached.
     *
     * @param array $params The list of parameters already in set.
     */
    protected function hydrateList(array &$params = []) : void
    {
    }

    /**
     * Updates the list of parameters and/or the item when the response for
     * the current request is not cached.
     *
     * @param array $params Thelist of parameters already in set.
     */
    protected function hydrateShowAmp(array &$params = []) : void
    {
        $siteColor = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('site_color', '#005689');

        $this->view->assign('site_color', $siteColor);

        // Get instance logo size
        $logo = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('site_logo');

        if (!empty($logo)) {
            $logoPath     = $this->get('core.instance')->getMediaShortPath() . '/sections/' . rawurlencode($logo);
            $logoUrl      = $this->get('core.instance')->getBaseUrl() . $logoPath;
            $logoFilePath = SITE_PATH . $logoPath;

            $logoSize = file_exists($logoFilePath)
                ? @getimagesize($logoFilePath)
                : null;

            if (is_array($logoSize)) {
                $this->view->assign([
                    'logoSize' => $logoSize,
                    'logoUrl'  => $logoUrl
                ]);
            }
        }

        $em = $this->get('entity_repository');
        if (isset($params['content']->img2) && ($params['content']->img2 > 0)) {
            $photoInt = $em->find('Photo', $params['content']->img2);
            $this->view->assign('photoInt', $photoInt);
        }

        if (isset($params['content']->fk_video2) && ($params['content']->fk_video2 > 0)) {
            $videoInt = $em->find('Video', $params['content']->fk_video2);
            $this->view->assign('videoInt', $videoInt);
        }

        //Get suggested contents
        $suggestedContents = $this->get('core.helper.content')->getSuggested(
            $params['content']->pk_content,
            $params['content']->content_type_name,
            $params['o_category']->id ?? null
        );

        $suggested = $suggestedContents[0];
        $photos    = $suggestedContents[1];

        $this->view->assign([
            'related'   => $this->getRelated($params['content']),
            'suggested' => $suggested,
            'photos'    => $photos
        ]);
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
     * Parses and returns the list of known parameters for the action.
     *
     * @param string $action The action to get known parameters for.
     * @param array  $params The list of query parameters.
     *
     * @return array The list of known parameters for the action.
     */
    protected function getKnownParameters($action, $params)
    {
        $params = array_key_exists($action, $this->queries)
            ? array_intersect_key($params, array_flip($this->queries[$action]))
            : [];

        if (array_key_exists('page', $params) && (int) $params['page'] === 1) {
            unset($params['page']);
        }

        return $params;
    }

    /**
     * Parses and returns the list of unknown parameters for the action.
     *
     * @param string $action The action to get unknown parameters for.
     * @param array  $params The list of query parameters.
     *
     * @return array The list of unknown parameters for the action.
     */
    protected function getUnknownParameters($action, $params)
    {
        return array_key_exists($action, $this->queries)
            ? array_diff_key($params, array_flip($this->queries[$action]))
            : $params;
    }

    /**
     * Returns the list of related contents for a content.
     *
     * @param Content $content The content object.
     *
     * @return array The list of rellated contents.
     */
    protected function getRelated($content)
    {
        $relations = $this->get('related_contents')
            ->getRelations($content->id, 'inner');

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

            if ($content->fk_content_type == 1 && !empty($content->img1)) {
                $content->photo = $em->find('Photo', $content->img1);
            } elseif ($content->fk_content_type == 1 && !empty($content->fk_video)) {
                $content->video = $em->find('Video', $content->fk_video);
            }

            $related[] = $content;
        }

        return $related;
    }

    /**
     * Returns the list of tags from a list of contents.
     *
     * @param array $contents The list of contents to fetch tags from.
     *
     * @return array The list of tags.
     */
    protected function getTags($contents)
    {
        if (!is_array($contents)) {
            $contents = [ $contents ];
        }

        $tagIds = [];
        foreach ($contents as $content) {
            $tagIds = array_merge($tagIds, $content->tags);
        }

        return $this->get('api.service.tag')->getListByIdsKeyMapped(
            $tagIds,
            $this->get('core.locale')->getRequestLocale()
        )['items'];
    }
}
