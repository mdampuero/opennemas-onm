<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Repository;

use Common\ORM\Core\Connection;

/**
 * The DatabaseRepository class provides methods to search items to migrate from
 * a database data source.
 */
class DatabaseRepository implements Repository
{
    /**
     * The repository configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $conn;

    /**
     * Initializes the DatabaseRepository.
     *
     * @param Connection $conn The database connection.
     */
    public function __construct($config, $tracker)
    {
        $config['connection']['dbname'] = $config['source']['database'];

        $this->config  = $config;
        $this->conn    = new Connection($config['connection']);
        $this->tracker = $tracker;

        $this->conn->getConfiguration()->setSQLLogger(null);
    }

    public function start()
    {
        $ids   = implode(',', $this->config['source']['id']);
        $table = $this->config['source']['table'];

        $filters = [];
        $where   = '';

        if (!empty($this->config['source']['filter'])) {
            $filters[] = $this->config['source']['filter'];
        }

        if (!empty($filters)) {
            $where = ' WHERE ' . implode(' AND ', $filters);
        }

        $query = sprintf(
            'CREATE TABLE IF NOT EXISTS migration_fix_items (PRIMARY KEY (%s)) AS SELECT DISTINCT %s FROM %s%s',
            $ids,
            $ids,
            $table,
            $where
        );

        $this->conn->executeQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $sql = 'SELECT COUNT(*) as total FROM migration_fix_items';

        $result = $this->conn->fetchAll($sql);

        return $result[0]['total'];
    }

    /**
     * {@inheritdoc}
     */
    public function countAll()
    {
        $sql = sprintf(
            'SELECT COUNT(*) as total FROM %s',
            $this->config['source']['table']
        );

        $filters = [];

        if (!empty($this->config['source']['filter'])) {
            $filters[] = $this->config['source']['filter'];
        }

        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }

        $result = $this->conn->fetchAll($sql);

        return $result[0]['total'];
    }

    /**
     * {@inheritdoc}
     */
    public function countFixed()
    {
        return $this->tracker->count();
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $table = $this->config['source']['table'];

        $join = [];

        foreach ($this->config['source']['id'] as $id) {
            $join[] = sprintf(
                'fixing.%s = %s.%s',
                $id,
                $table,
                $id
            );
        }

        $sql = sprintf(
            'SELECT %s.* FROM %s JOIN (SELECT * FROM migration_fix_items LIMIT 1) fixing ON %s',
            $table,
            $table,
            implode(' AND ', $join)
        );

        $result = $this->conn->fetchAll($sql);

        if (empty($result)) {
            return false;
        }

        $where = [];

        foreach ($this->config['source']['id'] as $id) {
            $where[] = sprintf(
                '%s = %s',
                $id,
                $result[0][$id]
            );
        }

        $sql = 'DELETE FROM migration_fix_items WHERE ' . implode(' AND ', $where);

        $this->conn->executeQuery($sql);

        return $result[0];
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($sqls)
    {
        foreach ($sqls as $sql) {
            $this->conn->executeQuery($sql);
        }
    }

    public function end()
    {
        $this->conn->executeQuery('DROP TABLE IF EXISTS migration_fix_items');
    }
}
