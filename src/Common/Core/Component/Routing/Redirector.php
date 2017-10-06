<?php

namespace Common\Core\Component\Routing;

class Redirector
{
    /**
     * The cache connection.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the Redirector.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        if ($container->get('cache.manager')->hasConnection('instance')) {
            $this->cache = $container->get('cache.manager')
                ->getConnection('instance');
        }

        $this->conn = $container->get('orm.manager')->getConnection('instance');
    }

    /**
     * Returns a translation given the content slug, type and/or id.
     *
     * @param string  $slug The content slug.
     * @param string  $type The content type.
     * @param integer $id   The content id.
     *
     * @return array The translation values.
     *
     * @throws \InvalidArgumentException When no slug or id provided.
     */
    public function getTranslation($slug, $type = null, $id = null)
    {
        if (empty($slug) && empty($id)) {
            throw new \InvalidArgumentException();
        }

        $cacheId = $this->getCacheId($slug, $type, $id);

        if ($this->hasCache() && $this->cache->exists($cacheId)) {
            return $this->cache->get($cacheId);
        }

        $translation = !empty($id) ? $this->getTranslationById($id, $type) :
            $this->getTranslationBySlug($slug, $type);

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
     * Returns a translation by id.
     *
     * @param integer $id The content id.
     *
     * @return array The content translation values.
     */
    protected function getTranslationById($id, $type = null)
    {
        $sql    = 'SELECT * FROM `translation_ids` WHERE `pk_content_old` = ? LIMIT 1';
        $params = [ $id ];

        if (!empty($type)) {
            $sql = 'SELECT * FROM `translation_ids` WHERE `pk_content_old` = ? AND `type` = ? LIMIT 1';
            array_push($params, $type);
        }

        return $this->conn->fetchAssoc($sql, $params);
    }

    /**
     * Returns a translation by slug and type.
     *
     * @param string $slug The content slug.
     * @param string $type The content type.
     *
     * @return array The content translation values.
     */
    protected function getTranslationBySlug($slug, $type)
    {
        return $this->conn->fetchAssoc(
            'SELECT * FROM `translation_ids` WHERE `slug` = ? AND `type` = ? LIMIT 1',
            [ $slug, $type ]
        );
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
