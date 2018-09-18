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

class BaseDataSet extends DataSet
{
    /**
     * The list of autoloaded values.
     *
     * @var array
     */
    protected $autoloaded = [];

    /**
     * The list of keys to have always loaded.
     *
     * @var array
     */
    protected $toAutoload;

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
     * The DataSet metadata.
     *
     * @var Metadata
     */
    protected $metadata;

    /**
     * Initializes the DataSet.
     *
     * @param Connection $conn       The database connection.
     * @param Metadata   $metadata   The DataSet metadata.
     * @param Cache      $cache      The cache service.
     * @param array      $toAutoload The list of keys to have always loaded.
     */
    public function __construct(Connection $conn, Metadata $metadata, Cache $cache = null, $toAutoload = [])
    {
        $this->cache      = $cache;
        $this->conn       = $conn;
        $this->metadata   = $metadata;
        $this->toAutoload = $toAutoload;

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
        $sql = sprintf(
            'delete from %s where %s in (?)',
            $this->metadata->getTable(),
            $this->metadata->getDataSetKey()
        );

        $this->conn->executeQuery(
            $sql,
            [ $key ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        if ($this->hasCache()) {
            $this->cache->remove($key);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $needle = $key;

        if (!is_array($key)) {
            $needle  = [ $key ];
            $default = [ $default ];
        }

        if (!is_array($default)) {
            $default = array_fill(0, count($key), $default);
        }

        $data   = array_intersect_key($this->autoloaded, array_flip($needle));
        $missed = array_diff($needle, array_keys($data));

        // Data missing, search cache first
        if (!empty($missed) && $this->hasCache()) {
            $data   = array_merge($data, $this->cache->get($missed));
            $missed = array_diff($needle, array_keys($data));
        }

        // Some data still missing, search database
        if (!empty($missed)) {
            $keyName   = $this->metadata->getDataSetKey();
            $valueName = $this->metadata->getDataSetValue();

            $sql = sprintf(
                'select * from %s where %s in (?)',
                $this->metadata->getTable(),
                $keyName
            );

            $values = $this->conn->fetchAll(
                $sql,
                [ $missed ],
                [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );

            foreach ($values as $value) {
                $data[$value[$keyName]] = @unserialize($value[$valueName]);
                $this->cache->set($value[$keyName], $data[$value[$keyName]]);
            }
        }

        $default = array_combine($needle, $default);
        $data    = array_merge($default, $data);

        if (!is_array($key)) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value = null)
    {
        if (empty($key)) {
            return;
        }

        if (is_array($key)) {
            $empty = array_filter($key, function ($a) {
                return is_null($a) || $a === '';
            });

            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }

            $this->delete(array_keys($empty));

            return;
        }

        $data  = [ $key, serialize($value), serialize($value) ];
        $types = [ \PDO::PARAM_STR, \PDO::PARAM_STR, \PDO::PARAM_STR ];

        $sql = sprintf(
            'insert into %s (%s, %s) values (?,?) on duplicate key update %s = ?',
            $this->metadata->getTable(),
            $this->metadata->getDataSetKey(),
            $this->metadata->getDataSetValue(),
            $this->metadata->getDataSetValue()
        );

        $this->conn->executeQuery($sql, $data, $types);

        if ($this->hasCache()) {
            $this->cache->remove($key);
        }
    }

    /**
     * Loads the values configured to autoload.
     */
    protected function autoload()
    {
        if (empty($this->toAutoload)) {
            return;
        }

        if ($this->hasCache()) {
            $data = $this->cache->get($this->toAutoload);

            if (!empty($data) && is_array($data)) {
                $this->autoloaded = array_merge($this->autoloaded, $data);
            }
        }

        $missed = array_diff($this->toAutoload, array_keys($this->autoloaded));

        if (empty($missed)) {
            return;
        }

        $this->autoloaded = array_merge($this->autoloaded, $this->get($missed));
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
}
