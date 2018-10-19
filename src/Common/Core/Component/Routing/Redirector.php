<?php

namespace Common\Core\Component\Routing;

use Api\Service\Service;
use Common\Cache\Core\Cache;

class Redirector
{
    /**
     * The cache connection.
     *
     * @var Cache
     */
    protected $cache;

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
     * Returns a translation given the content slug, type and/or id.
     *
     * @param string  $slug        The content slug.
     * @param string  $contentType The content type.
     * @param integer $id          The content id.
     *
     * @return array The translation values.
     *
     * @throws \InvalidArgumentException When no slug or id provided.
     */
    public function getTranslation($slug, $contentType = null, $id = null)
    {
        if (empty($slug) && empty($id)) {
            throw new \InvalidArgumentException();
        }

        $cacheId = $this->getCacheId($slug, $contentType, $id);

        if ($this->hasCache() && $this->cache->exists($cacheId)) {
            return $this->cache->get($cacheId);
        }

        $type   = [ 1, 2 ];
        $source = $slug;

        if (!empty($id)) {
            $type   = [ 0 ];
            $source = $id;
        }

        $oql = sprintf(
            'type in [%s] and source = "%s" limit 1',
            implode(',', $type),
            $source
        );

        if (!empty($contentType)) {
            $oql = sprintf('content_type = "%s"', $contentType) . ' and ' . $oql;
        }

        $items       = $this->service->getList($oql);
        $translation = $items['total'] === 1 ? $items['items'][0] : [];

        if ($this->hasCache()) {
            $this->cache->set($cacheId, $translation);
        }

        return $translation;
    }

    /**
     * Returns the cache id basing on the parameters.
     *
     * @param string  $slug The content slug.
     * @param string  $type The content type.
     * @param integer $id   The content id.
     *
     * @return string The cache id.
     */
    protected function getCacheId($slug, $type, $id)
    {
        return implode('-', [ 'redirector', $slug, $type, $id ]);
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
     * Returns a content from a translation value.
     *
     * @param array $translation The translation value.
     *
     * @return Content The content.
     */
    protected function getContent($url)
    {
        if (empty($url)) {
            return null;
        }

        $contentType = \classify($url->content_type);
        $method      = 'get' . $contentType;

        if (method_exists($this, $method)) {
            return $this->{$method}($url->target);
        }

        return $this->container->get('entity_repository')
            ->find($contentType, $url->target);
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
