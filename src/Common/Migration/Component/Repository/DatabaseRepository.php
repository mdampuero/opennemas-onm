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
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $sql = sprintf(
            'SELECT COUNT(*) as total FROM %s',
            $this->config['source']['table']
        );

        // Support single value and array of values
        $parsed  = $this->tracker->count();
        $filters = [];

        if (!empty($this->config['source']['filter'])) {
            $filters[] = $this->config['source']['filter'];
        }

        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }

        $rs = $this->conn->fetchAll($sql);

        return $rs[0]['total'] - $parsed;
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

        $rs = $this->conn->fetchAll($sql);

        return $rs[0]['total'];
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
        $sql = sprintf(
            'SELECT * FROM %s',
            $this->config['source']['table']
        );

        $parsed  = $this->tracker->count();
        $filters = [];

        if (!empty($this->config['source']['filter'])) {
            $filters[] = $this->config['source']['filter'];
        }

        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }

        $sql .= sprintf(' LIMIT 1 OFFSET %s', $parsed);

        $rs = $this->conn->fetchAll($sql);

        if (empty($rs)) {
            return false;
        }

        return $rs[0];
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
}
