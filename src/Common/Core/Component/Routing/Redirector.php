<?php

namespace Common\Core\Component\Routing;

use Api\Service\Service;
use Opennemas\Cache\Core\Cache;
use Common\Model\Entity\Category;
use Common\Model\Entity\Content;
use Common\Model\Entity\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Framework\Component\MIME\MimeTypeTool;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Common\Core\Component\Exception\ContentNotMigratedException;
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
        $response = $url->redirection
            ? $this->getRedirectResponse($request, $url)
            : $this->getForwardResponse($request, $url);

        $xTags = $response->headers->get('x-tags') . ",url-" . $url->id;

        $response->headers->set('x-tags', trim($xTags, ','));
        $response->headers->set('x-cacheable', true);

        return $response;
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
    public function getUrl($source, $contentType = [])
    {
        if (is_null($source) || $source === '') {
            throw new \InvalidArgumentException();
        }

        $source = $this->container->get('data.manager.filter')
            ->set($source)
            ->filter('url_decode')
            ->get();

        if (!is_array($contentType) && !empty($contentType)) {
            $contentType = [ $contentType ];
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
        if (empty($type)) {
            $type = [];
        }

        return implode('-', [ 'redirector', md5($slug), implode('-', $type) ]);
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
     * Returns a content basing on an id and a content type.
     *
     * @param integer $id          The content id.
     * @param string  $contentType The content type.
     *
     * @return Content The content.
     *
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
     * Returns an content by id.
     *
     * @param integer $id The content id.
     *
     * @return Content The content.
     */
    protected function getContentFromApi(int $id)
    {
        try {
            return $this->container->get('api.service.content')->getItem($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns an event by id.
     *
     * @param integer $id The event id.
     *
     * @return Content The event.
     */
    protected function getEvent(int $id)
    {
        return $this->getContentFromApi($id);
    }

    /**
     * Returns an static page by id.
     *
     * @param integer $id The static page id.
     *
     * @return Content The static page.
     */
    protected function getStaticPage(int $id)
    {
        return $this->getContentFromApi($id);
    }

    /**
     * Returns an user by id.
     *
     * @param integer $id The tag id.
     *
     * @return Tag The tag.
     */
    protected function getTag(int $id)
    {
        try {
            return $this->container->get('api.service.tag')->getItem($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns an user by id.
     *
     * @param integer $id The user id.
     *
     * @return User The user.
     */
    protected function getUser($id)
    {
        try {
            return $this->container->get('orm.manager')
                ->getRepository('User')
                ->find($id);
        } catch (\Exception $e) {
            return null;
        }
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

        if (!$this->isTargetValid($request, $url, $target)) {
            throw new NotFoundHttpException();
        }

        if (is_object($target)) {
            if ($this->isMediaFile($target)) {
                return $this->getMediaFileResponse($target);
            }

            $target = $this->container->get('core.helper.url_generator')
                ->generate($target);
        }

        $info  = parse_url($target);
        $query = [];

        if (array_key_exists('query', $info)) {
            parse_str($info['query'], $query);
        }

        $params  = $this->container->get('router')->match($info['path']);
        $forward = $request->duplicate($query, null, $params);

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
            $oql = sprintf('content_type in ["%s"]', implode('","', $contentType))
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
        $response->headers->set('Content-Type', MimeTypeTool::getMimeType($path));

        return $response;
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

        if (!$this->isTargetValid($request, $url, $target)) {
            throw new NotFoundHttpException();
        }

        if (is_object($target)) {
            $target = $this->container->get('core.helper.url_generator')
                ->generate($target);
        }

        if (strpos($target, '/redirect/content') !== false) {
            $data   = explode('&', explode('?', $target)[1]);
            $params = [];

            foreach ($data as $item) {
                $element             = explode('=', $item);
                $params[$element[0]] = $element[1];
            }

            return $this->getRedirectContent($params);
        }

        return new RedirectResponse(empty($target) ? '/' : $target, 301);
    }

    /**
     * Handles the redirections for all the contents.
     *
     * @param Array Params
     *
     * @return Response The response object
     */
    protected function getRedirectContent($params)
    {
        $id       = !empty($params['content_id']) ? $params['content_id'] : null;
        $slug     = !empty($params['slug']) ? $params['slug'] : null;
        $type     = !empty($params['content_type']) ? $params['content_type'] : null;
        $format   = !empty($params['format']) ? $params['format'] : null;
        $fragment = '';
        $content  = null;

        if (empty($id) && empty($slug)) {
            throw new ResourceNotFoundException();
        }

        $source      = !empty($id) ? $id : $slug;

        $translation = $this->getUrl($source, $type);

        if (empty($translation)) {
            throw new ContentNotMigratedException();
        }

        // Redirect content migrated to another domain
        if ($translation->type === 2) {
            return new RedirectResponse(
                (preg_match('/http(s)?:\/\//', $translation->target) ? '' : '/')
                . $translation->target
            );
        }

        $content = $this->getContent($translation->target, $translation->content_type);

        if (empty($content)) {
            return $this->getRedirectNotMigratedContent($type);
        }

        if ($type === 'comment') {
            $fragment = '#comentarios';
        }

        $url = $this->container->get('core.helper.url_generator')->generate($content);

        if ($format === 'amp' && in_array(
            $content->content_type_name,
            [ 'album', 'article', 'opinion', 'poll', 'video' ]
        )) {
            $url = str_replace('.html', '.amp.html', $url);
        }

        // TODO: Remove when URI target="_blank"' not included for external
        $url = str_replace('" target="_blank', '', $url);

        return new RedirectResponse($url . $fragment, 301);
    }

    /**
     * Returns a response when a content was not found basing on a setting from
     * the instance.
     *
     * @param string $type The content type.
     *
     * @return RedirectResponse The redirection response object to frontpages
     *                         when the instance has redirection to frontpages
     *                         enabled.
     *
     * @throws ContentNotMigratedException When instance has redirection to
     *                                     frontpages disabled.
     */
    protected function getRedirectNotMigratedContent($type)
    {
        $ignored     = [ 'article', 'category' ];
        $redirection = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('redirection');

        if (empty($redirection)) {
            throw new ContentNotMigratedException();
        }

        $router = $this->get('router');
        $route  = preg_replace('/_+/', '_', 'frontend_' . $type . '_frontpage');
        $url    = $router->generate('frontend_frontpage');

        if (!in_array($type, $ignored)
            && $router->getRouteCollection()->get($route)
        ) {
            $url = $router->generate($route);
        }

        return new RedirectResponse($url, 301);
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
            $oql = sprintf('content_type in ["%s"]', implode('","', $contentType))
                . ' and ' . $oql;
        }

        $urls = $this->service->getList($oql);

        if ($urls['total'] === 0) {
            return null;
        }

        foreach ($urls['items'] as $url) {
            $pattern = preg_replace([ '/\//' ], [ '\\/' ], $url->source);

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

        // URI to URI
        if ($url->type === 2) {
            return $this->replaceInternalVariables($url->target);
        }

        // RegExp to URI
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
        $uri = $this->replaceInternalVariables($uri);

        $pattern = preg_replace([ '/\//' ], [ '\\/' ], $url->source);

        preg_match_all(
            '/' . $pattern . '/',
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
     * @param Url     $url     The Url object.
     * @param string  $target  The target to check.
     *
     * @return boolean True if the target is valid. False otherwise.
     */
    protected function isTargetValid($request, $url, $target)
    {
        $contentHelper = $this->container->get('core.helper.content');

        if (empty($target)) {
            return empty($url->target);
        }

        if (is_object($target)) {
            if ($target instanceof \Content || $target instanceof Content) {
                return $contentHelper->isReadyForPublish($target);
            }

            if ($target instanceof Category) {
                return !empty($target->enabled);
            }

            return true;
        }

        return $target !== trim($request->getRequestUri(), '/');
    }

    /**
     * Replaces some valid placeholders by dynamic internal variables in the
     * Url target.
     *
     * @param string $target The Url target.
     *
     * @return string The target after replacements.
     */
    protected function replaceInternalVariables($target)
    {
        $patterns = [
            '/\$THEMES_DEPLOYED_AT/',
            '/\$DEPLOYED_AT/',
            '/\$INSTANCE/',
            '/\$THEME\b/',
        ];

        $replacements = [
            THEMES_DEPLOYED_AT,
            DEPLOYED_AT,
            $this->container->get('core.instance')->internal_name,
            str_replace(
                'es.openhost.theme.',
                '',
                $this->container->get('core.theme')->uuid
            ),
        ];

        return preg_replace($patterns, $replacements, $target);
    }
}
