<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\DataSet;

use Common\Cache\Core\Cache;
use Common\ORM\Core\Connection;
use Common\ORM\Core\DataSet;
use Common\ORM\Core\Metadata;
use Common\Data\Serialize\Serializer\PhpSerializer;

class BaseDataSet extends DataSet
{
    /**
     * The cache service.
     *
     * @var Cache.
     */
    protected $cache;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * The information managed by the database
     *
     * @var array
     */
    protected $data = [];

    /**
     * The DataSet metadata.
     *
     * @var Metadata
     */
    protected $metadata;

    /**
     * Initializes the DataSet.
     *
     * @param Connection $conn     The database connection.
     * @param Metadata   $metadata The DataSet metadata.
     * @param Cache      $cache    The cache service.
     */
    public function __construct(Connection $conn, Metadata $metadata, Cache $cache = null)
    {
        $this->cache    = $cache;
        $this->conn     = $conn;
        $this->metadata = $metadata;

        $this->autoload();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (empty($key)) {
            return;
        }

        $key = is_array($key) ? $key : [ $key ];

        if (empty(array_intersect_key($this->data, array_flip($key)))) {
            return;
        }

        $this->deleteFromDatabase($key);

        $this->data = array_diff_key($this->data, array_flip($key));

        if ($this->hasCache()) {
            $this->cache->set($this->getCacheId(), $this->data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (is_array($key)) {
            $default = is_array($default) ?
                array_combine($key, $default) : array_fill_keys($key, $default);

            $data = array_intersect_key($this->data, array_flip($key));

            return array_merge($default, $data);
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value = null)
    {
        if (empty($key)) {
            return;
        }

        if (!is_array($key)) {
            $key = [ $key => $value ];
        }

        $toDelete = array_filter($key, function ($a) {
            return is_null($a) || $a === '';
        });

        $toSave = array_diff_key($key, $toDelete);

        $this->data = array_diff_key($this->data, $toDelete);
        $this->data = array_merge($this->data, $toSave);

        $this->saveToDatabase($toSave);
        $this->deleteFromDatabase(array_keys($toDelete));

        if ($this->hasCache()) {
            $this->cache->set($this->getCacheId(), $this->data);
        }
    }

    /**
     * Loads the values configured to autoload.
     */
    protected function autoload()
    {
        if (!empty($this->data)) {
            return;
        }

        if ($this->hasCache()) {
            $data = $this->cache->get($this->getCacheId());

            if (!empty($data) && is_array($data)) {
                $this->data = $data;
                return;
            }
        }

        $this->data = $this->load();
    }

    /**
     * Deletes values from database
     *
     * @param array $keys The list of values to delete.
     */
    protected function deleteFromDatabase($values)
    {
        if (empty($values)) {
            return;
        }

        $sql = sprintf(
            'delete from %s where %s in (?)',
            $this->metadata->getTable(),
            $this->metadata->getDataSetKey()
        );

        $this->conn->executeQuery(
            $sql,
            [ $values ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
    }

    /**
     * Returns the cache id for the current dataset.
     *
     * @return string The cache id for the current dataset.
     */
    protected function getCacheId()
    {
        return \underscore($this->metadata->name);
    }

    /**
     * Checks if the current data set has cache.
     *
     * @return boolean True if the repository has cache. False, otherwise.
     */
    protected function hasCache()
    {
        return !empty($this->cache);
    }

    /**
     * Loads all values from the database.
     *
     * @return array An array of all values in database.
     */
    protected function load()
    {
        $data      = [];
        $sql       = sprintf('select * from %s', $this->metadata->getTable());
        $keyName   = $this->metadata->getDataSetKey();
        $valueName = $this->metadata->getDataSetValue();

        $values = $this->conn->fetchAll($sql);

        foreach ($values as $value) {
            $data[$value[$keyName]] =
                PhpSerializer::unserialize($value[$valueName]);
        }

        if ($this->hasCache()) {
            $this->cache->set($this->getCacheId(), $data);
        }

        return $data;
    }

    /**
     * Saves values to database.
     *
     * @param array $values The values to save.
     */
    protected function saveToDatabase($values)
    {
        if (empty($values)) {
            return;
        }

        $data  = [];
        $types = [];
        foreach ($values as $key => $value) {
            $data  = array_merge($data, [ $key, PhpSerializer::serialize($value) ]);
            $types = array_merge($types, [ \PDO::PARAM_STR, \PDO::PARAM_STR ]);
        }

        $sql = sprintf(
            'insert into %s (%s, %s) values '
            . trim(str_repeat('(?,?),', count($values)), ',')
            . ' on duplicate key update %s = values(%s)',
            $this->metadata->getTable(),
            $this->metadata->getDataSetKey(),
            $this->metadata->getDataSetValue(),
            $this->metadata->getDataSetValue(),
            $this->metadata->getDataSetValue()
        );

        $this->conn->executeQuery($sql, $data, $types);
    }
}
