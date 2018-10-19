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
     * @param Service    $service The API service for URLs.
     * @param Connection $cache   The cache connection.
     */
    public function __construct(Service $service, Cache $cache)
    {
        $this->cache   = $cache;
        $this->service = $service;
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
     * Checks if the current repository has cache.
     *
     * @return boolean True if the repository has cache. False, otherwise.
     */
    protected function hasCache()
    {
        return !empty($this->cache);
    }
}
