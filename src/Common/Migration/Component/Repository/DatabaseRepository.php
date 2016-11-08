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
        $this->config  = $config;
        $this->conn    = new Connection($config['connection']);
        $this->tracker = $tracker;

        $this->conn->selectDatabase($config['database']);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        $sql = sprintf(
            'SELECT COUNT(*) as total FROM %s',
            $this->config['mapping']['table']
        );

        // Support single value and array of values
        $parsed = $this->tracker->getParsed();
        $filter = '%s > "%s"';
        if (is_array($parsed)) {
            $filter = '%s NOT IN ("%s")';
            $parsed = implode('", "', $parsed);
        }

        $filters[] = sprintf(
            $filter,
            $this->config['mapping']['id'],
            $parsed
        );

        if (!empty($this->config['mapping']['filter'])) {
            $filters[] = $this->config['mapping']['filter'];
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
    public function countAll()
    {
        $sql = sprintf(
            'SELECT COUNT(*) as total FROM %s',
            $this->config['mapping']['table']
        );

        $rs = $this->conn->fetchAll($sql);

        return $rs[0]['total'];
    }

    /**
     * {@inheritdoc}
     */
    public function countMigrated()
    {
        $sql = sprintf(
            'SELECT COUNT(*) as total FROM %s',
            $this->config['mapping']['table']
        );

        // Support single value and array of values
        $parsed = $this->tracker->getParsed();
        $filter = '%s <= "%s"';
        if (is_array($parsed)) {
            $filter = '%s IN ("%s")';
            $parsed = implode('", "', $parsed);
        }

        $filters[] = sprintf(
            $filter,
            $this->config['mapping']['id'],
            $parsed
        );

        if (!empty($this->config['mapping']['filter'])) {
            $filters[] = $this->config['mapping']['filter'];
        }

        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }

        $sql .= sprintf(
            ' ORDER BY %s ASC',
            $this->config['mapping']['id']
        );

        $rs = $this->conn->fetchAll($sql);

        return $rs[0]['total'];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $sql = sprintf(
            'SELECT * FROM %s',
            $this->config['mapping']['table']
        );

        // Support single value and array of values
        $parsed = $this->tracker->getParsed();
        $filter = '%s > "%s"';
        if (is_array($parsed)) {
            $filter = '%s NOT IN ("%s")';
            $parsed = implode('", "', $parsed);
        }

        $filters[] = sprintf(
            $filter,
            $this->config['mapping']['id'],
            $parsed
        );

        if (!empty($this->config['mapping']['filter'])) {
            $filters[] = $this->config['mapping']['filter'];
        }

        if (!empty($filters)) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }

        $sql .= sprintf(
            ' ORDER BY %s ASC LIMIT 1',
            $this->config['mapping']['id']
        );

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
