<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Routing;

use Api\Service\Service;
use Common\Cache\Core\Cache;
use Common\ORM\Entity\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Redirector
{
    /**
     * The cache connection.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The API service.
     *
     * @var Service
     */
    protected $service;

    /**
     * Initializes the Redirector.
     *
     * @param ServiceContainer $container  The service container.
     * @param Service          $service    The API service for URLs.
     * @param Connection       $cache      The cache connection.
     */
    public function __construct($container, Service $service, Cache $cache)
    {
        $this->cache     = $cache;
        $this->container = $container;
        $this->service   = $service;
    }

    /**
     * Returns a response basing on the current request and the Url object.
     *
     * @param Request $request The current request.
     * @param Url     $url     The Url object.
     *
     * @return mixed The redirect response if the Url has redirection enabled.
     *               The result of forwarding the action in the Url has the
     *               redirection disabled.
     */
    public function getResponse(Request $request, Url $url)
    {
        return $url->redirection ? $this->getRedirectResponse($url) :
            $this->getForwardResponse($request, $url);
    }

    /**
     * Returns an Url basing on the source value and the content type.
     *
     * @param string $source      The source value.
     * @param string $contentType The content type.
     *
     * @return mixed The Url object, if found. Null otherwise.
     *
     * @throws \InvalidArgumentException When no source value provided.
     */
    public function getUrl($source, $contentType = null)
    {
        if (empty($source)) {
            throw new \InvalidArgumentException();
        }

        $cacheId = $this->getCacheId($source, $contentType);

        if ($this->hasCache() && $this->cache->exists($cacheId)) {
            return $this->cache->get($cacheId);
        }

        $url = $this->getLiteralUrl($source, $contentType);

        if (empty($url)) {
            $url = $this->getRegExpUrl($source, $contentType);
        }

        if (!empty($url) && $this->hasCache()) {
            $this->cache->set($cacheId, $url);
        }

        return $url;
    }

    /**
     * Returns the cache id basing on the parameters.
     *
     * @param string $slug The source value.
     * @param string $type The content type.
     *
     * @return string The cache id.
     */
    protected function getCacheId($slug, $type)
    {
        return implode('-', [ 'redirector', $slug, $type ]);
    }

    /**
     * Returns an category by id.
     *
     * @param integer $id The category id.
     *
     * @return Category The category.
     */
    protected function getCategory($id)
    {
        try {
            $content = $this->container->get('orm.manager')
                ->getRepository('Category')
                ->find($id);
        } catch (\Exception $e) {
            return null;
        }

        return $content;
    }

    /**
     * Returns an comment by id.
     *
     * @param integer $id The comment id.
     *
     * @return Comment The comment.
     */
    protected function getComment($id)
    {
        $comment = new \Comment($id);

        if (empty($comment->content_id)) {
            return null;
        }

        return new \Content($comment->content_id);
    }

    /**
     * Returns a content basing on a Url.
     *
     * @param Url $url The Url object.
     *
     * @return Content The content.
     */
    protected function getContent(Url $url)
    {
        $contentType = \classify($url->content_type);
        $method      = 'get' . $contentType;

        if (method_exists($this, $method)) {
            return $this->{$method}($url->target);
        }

        return $this->container->get('entity_repository')
            ->find($contentType, $url->target);
    }

    /**
     * Returns the result of forwarding the current request basing on the Url
     * object.
     *
     * @param Request $request The current request.
     * @param Url     $url     The Url object.
     *
     * @return Response The result of forwarding the request.
     */
    protected function getForwardResponse($request, $url)
    {
        $content = $this->getContent($url);

        if (empty($content)) {
            throw new ResourceNotFoundException();
        }

        $target = $this->container->get('core.helper.url_generator')
            ->generate($content);

        $params  = $this->container->get('router')->match($target);
        $forward = $request->duplicate([], null, $params);

        return $this->container->get('kernel')
            ->handle($forward, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * Returns an URL with source value equals to provided paramter.
     *
     * @param string $source The source value.
     *
     * @return mixed The Url that matches the URI. Null otherwise.
     */
    protected function getLiteralUrl($source, $contentType = null)
    {
        $oql = sprintf(
            'type in [%s] and source = "%s" limit 1',
            implode(',', [ 0, 1, 2 ]),
            $source
        );

        if (!empty($contentType)) {
            $oql = sprintf('content_type = "%s"', $contentType)
                . ' and ' . $oql;
        }

        $items = $this->service->getList($oql);

        if ($items['total'] === 1) {
            return $items['items'][0];
        }

        return null;
    }

    /**
     * Returns an opinion by id.
     *
     * @param integer $id The opinion id.
     *
     * @return Opinion The opinion.
     */
    protected function getOpinion($id)
    {
        return $this->container->get('opinion_repository')
            ->find('Opinion', $id);
    }

    /**
     * Returns a RedirectReponse basing on the Url object.
     *
     * @param Url $url The Url object.
     *
     * @return RedirectResponse The redirect response.
     */
    protected function getRedirectResponse(Url $url)
    {
        if (!in_array($url->type, [ 0, 1, 3 ])) {
            return new RedirectResponse($url->target, 301);
        }

        $content = $this->getContent($url);

        if (empty($content)) {
            throw new ResourceNotFoundException();
        }

        $target = $this->container->get('core.helper.url_generator')
            ->generate($content);

        return new RedirectResponse($target, 301);
    }

    /**
     * Returns an Url that matches the URI.
     *
     * @param string $uri The URI to match.
     *
     * @return mixed The Url that matches the URI. Null otherwise.
     */
    protected function getRegExpUrl($uri, $contentType = null)
    {
        $oql = sprintf('type in [%s]', implode(',', [ 3, 4 ]));

        if (!empty($contentType)) {
            $oql = sprintf('content_type = "%s"', $contentType)
                . ' and ' . $oql;
        }

        $urls = $this->service->getList($oql);

        if ($urls['total'] === 0) {
            return null;
        }

        foreach ($urls['items'] as $url) {
            $pattern = preg_replace('/\//', '\\/', $url->source);

            if (preg_match('/' . $pattern . '/', $uri)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Checks if the current repository has cache.
     *
     * @return boolean True if the repository has cache. False, otherwise.
     */
    protected function hasCache()
    {
        return !empty($this->cache);
    }
}
