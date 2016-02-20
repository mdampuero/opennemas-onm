<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Repository;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Repository;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

class BaseRepository extends Repository
{
    /**
     * The cache object.
     *
     * @var CacheInterface
     */
    protected $cache;

    /**
     * The cache separator.
     *
     * @var string
     */
    protected $cacheSeparator = '-';

    /**
     * The value to save in cache when the entity is not in database.
     *
     * @var string
     */
    protected $lostValue = '-lost-';

    /**
     * The database connection.
     *
     * @var DbalWrapper
     */
    protected $conn;

    /**
     * The source name.
     *
     * @var string
     */
    protected $source = 'Database';

    /**
     * Initializes a new DatabasePersister.
     *
     * @param CacheInterface $cache  The cache service.
     * @param DbalWrapper    $conn   The database connection.
     * @param DbalWrapper    $source The source name.
     */
    public function __construct(CacheInterface $cache, DbalWrapper $conn, $source)
    {
        $this->cache  = $cache;
        $this->conn   = $conn;
        $this->source = $source;
    }

    /**
     * Returns the number of entities that match the criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return integer The number of entities.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = 'SELECT COUNT(id) FROM `' . $this->getCachePrefix() .
            '` WHERE ' . $filterSQL;

        $rs = $this->conn->fetchArray($sql);

        if (!$rs) {
            return false;
        }

        return (integer) $rs[0];
    }


    /**
     * Finds an entity by id.
     *
     * @param integer $id The entity id.
     *
     * @return Entity The entity.
     *
     * @throws EntityNotFoundException If the entity is not found.
     */
    public function find($id)
    {
        if (empty($id)) {
            throw new EntityNotFoundException();
        }

        $cacheId = $id;

        if (is_array($cacheId)) {
            $cacheId = implode($this->cacheSeparator, $cacheId);
        }

        $cacheId = $this->getCachePrefix() . $this->cacheSeparator .  $cacheId;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $class = 'Framework\\ORM\\Entity\\' . $this->getEntityName();

            $entity = new $class();

            if (!is_array($id)) {
                $id = [ 'id' => $id ];
            }

            foreach ($id as $key => $value) {
                $entity->{$key} = $value;
            }

            $this->refresh($entity);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Finds enties given a criteria.
     *
     * @param array   $criteria        The criteria.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The current page.
     * @param integer $offset          The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`id` ASC';

        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT id FROM `" . $this->getCachePrefix() . "` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $rs = $this->conn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $item) {
            $ids[] = $item['id'];
        }

        return $this->findMulti($ids);
    }

    /**
     * Find multiple contents from an array of entity ids.
     *
     * @param array $data Array of entity ids.
     *
     * @return array Array of entities.
     */
    public function findMulti($data)
    {
        $ids  = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[]  = $this->getCachePrefix() . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $entities = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($entities as $entity) {
            $cachedIds[] = $entity->getCachedId();
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $entity) {
            $cacheId = explode($this->cacheSeparator, $entity);
            array_shift($cacheId);

            $entity = $this->find($cacheId[0]);
            if ($entity) {
                $entities[] = $entity;
            }
        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($entities) && $entities[$i]->id != $id) {
                $i++;
            }

            if ($i < count($entities)) {
                $ordered[] = $entities[$i];
            }
        }

        return $ordered;
    }

    /**
     * Returns the cache prefix for entities search by this repository.
     *
     * @return string The cache prefix.
     */
    public function getCachePrefix()
    {
        $prefix = $this->getEntityName();
        $prefix = preg_replace('/([a-z])([A-Z])/', '$1_$2', $prefix);

        return strtolower($prefix);
    }

    /**
     * Returns the entity class name for the current repository.
     *
     * @return string The entity class name.
     */
    public function getEntityName()
    {
        $name = get_class($this);
        $name = substr($name, strrpos($name, '\\') + 1);
        $name = str_replace('Repository', '', $name);

        return $name;
    }

    /**
     * Refresh an entity with fresh data from database.
     *
     * @param Entity $entity The entity to refresh.
     */
    public function refresh(Entity &$entity)
    {
        if (empty($entity->id)) {
            throw new EntityNotFoundException(
                "Could not find entity with id = 'null'"
            );
        }

        $sql = 'SELECT * FROM ' . $this->getCachePrefix() . ' WHERE id = '
            . $entity->id;

        $rs = $this->conn->fetchAssoc($sql);

        if (!$rs) {
            throw new EntityNotFoundException(
                'Could not find ' . $this->getCachePrefix() . ' with id = \''.
                $entity->id .'\''
            );
        }

        foreach ($rs as $key => $value) {
            $entity->{$key} = $value;

            $value = @unserialize($value);

            if ($value) {
                $entity->{$key} = $value;
            }
        }

        $entity->refresh();
    }

    /**
     * Convert database values to valid entity values.
     *
     * @param array $source The data from database.
     *
     * @return array The converted data.
     */
    protected function objectify($source)
    {
        if (!array_key_exists('columns', $this->metadata->mapping)) {
            throw new \Exception();
        }

        $data = [];
        foreach ($source as $key => $value) {
            if (array_key_exists($key, $this->metadata->properties)
                && array_key_exists($key, $this->metadata->mapping['columns'])
            ) {
                $from = \classify($this->metadata->mapping['columns'][$key]['type']);
                $to   = $this->metadata->properties[$key];

                $mapper = '\\Framework\\ORM\\Core\\DataMapper\\' . ucfirst($to)
                    . 'DataMapper';

                $mapper = new $mapper();
                $method = 'from' . ucfirst($from);

                $data[$key] = $mapper->{$method}($value);
            }
        }

        return $data;
    }
}
