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
use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Redirector
{
    /**
     * The list of media types that can be directly served by the application.
     *
     * @var array
     */
    const MEDIA_TYPES = [ 'attachment', 'photo' ];

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
        return $url->redirection ? $this->getRedirectResponse($request, $url) :
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
        $url     = null;

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
            return $this->container->get('orm.manager')
                ->getRepository('Category')
                ->find($id);
        } catch (\Exception $e) {
            return null;
        }
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
     * Returns a content basing on an id and a content type.
     *
     * @param integer $id          The content id.
     * @param string  $contentType The content type.
     *
     * @return Content The content.
     */
    protected function getContent($id, $contentType)
    {
        $contentType = \classify($contentType);
        $method      = 'get' . $contentType;

        if (method_exists($this, $method)) {
            return $this->{$method}($id);
        }

        return $this->container->get('entity_repository')
            ->find($contentType, $id);
    }

    /**
     * Returns the result of forwarding the current request basing on the Url
     * object.
     *
     * @param Request $request The current request.
     * @param Url     $url     The Url object.
     *
     * @return mixed A Response with the result of forwarding the request if the
     *               target is not a media file. A BinaryFileResponse if the
     *               target is a media file.
     */
    protected function getForwardResponse($request, $url)
    {
        $target = $this->getTarget($request, $url);

        if (!$this->isTargetValid($request, $target)) {
            throw new ResourceNotFoundException();
        }

        if (is_object($target)) {
            if ($this->isMediaFile($target)) {
                return $this->getMediaFileResponse($target);
            }

            $target = $this->container->get('core.helper.url_generator')
                ->generate($target);
        }

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
            'type in [%s] and source = "%s" and enabled = 1 limit 1',
            implode(',', [ 0, 1, 2 ]),
            $source
        );

        if (!empty($contentType)) {
            $oql = sprintf('content_type = "%s"', $contentType)
                . ' and ' . $oql;
        }

        try {
            return $this->service->getItemBy($oql);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns a response with the content of a file.
     *
     * @param Content $content The content object of the file to serve.
     *
     * @return BinaryFileResponse The response with the content of the file.
     */
    protected function getMediaFileResponse($content)
    {
        $path  = $this->container->getParameter('core.paths.public');
        $path .= $this->container->get('core.helper.url_generator')
            ->generate($content);

        $response = new BinaryFileResponse($path);

        $response->headers->set('X-Status-Code', 200);

        return $response;
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
     * @param Request $request The request object.
     * @param Url     $url     The Url object.
     *
     * @return RedirectResponse The redirect response.
     */
    protected function getRedirectResponse(Request $request, Url $url)
    {
        $target = $this->getTarget($request, $url);

        if (!$this->isTargetValid($request, $target)) {
            throw new ResourceNotFoundException();
        }

        if (is_object($target)) {
            $target = $this->container->get('core.helper.url_generator')
                ->generate($target);
        }

        return new RedirectResponse(empty($target) ? '/' : $target, 301);
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
        $oql = sprintf('type in [%s] and enabled = 1', implode(',', [ 3, 4 ]));

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
     * Returns the target basing on the Url.
     *
     * @param Request $request The current request.
     * @param Url     $url     The Url object.
     *
     * @return mixed A content if Url has type 0, 1 or 3 or a string.
     */
    protected function getTarget(Request $request, Url $url)
    {
        // Content, slug or regExp to content
        if (in_array($url->type, [ 0, 1, 3 ])) {
            $target = $url->target;

            // RegExp to content
            if ($url->type === 3) {
                $target = $this->getTargetForRegExpUrl($request, $url);
            }

            return $this->getContent($target, $url->content_type);
        }

        // Slug to slug/URL
        if ($url->type === 2) {
            return $url->target;
        }

        // RegExp to slug/URL
        return $this->getTargetForRegExpUrl($request, $url);
    }

    /**
     * Returns the target for an Url of type 4 basing on the current request.
     *
     * @param Request $request The current request.
     * @param Url     $url     The Url object.
     *
     * @return string The target.
     */
    protected function getTargetForRegExpUrl(Request $request, Url $url)
    {
        $uri = trim($request->getRequestUri(), '/');

        preg_match_all(
            '/' . preg_replace('/\//', '\\\/', $url->source) . '/',
            $uri,
            $matches
        );

        $replacements = [];
        if (!empty($matches[0])) {
            foreach ($matches as $key => $match) {
                $replacements['$' . $key] = $match[0];
            }
        }

        if (!empty($replacements)) {
            return str_replace(
                array_keys($replacements),
                array_values($replacements),
                $url->target
            );
        }

        return $uri;
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

    /**
     * Checks if the content is a media file that can be directly served.
     *
     * @param Content $content The content to check.
     *
     * @return boolean True if the content is a media file. False otherwise.
     */
    protected function isMediaFile($content)
    {
        return !empty($content)
            && in_array($content->content_type_name, self::MEDIA_TYPES);
    }

    /**
     * Checks if the target is valid basing on the current request.
     *
     * @param Request $request The current request.
     * @param string  $target  The target to check.
     *
     * @return boolean True if the target is valid. False otherwise.
     */
    protected function isTargetValid($request, $target)
    {
        return is_object($target)
            || $target !== trim($request->getRequestUri(), '/');
    }
}
