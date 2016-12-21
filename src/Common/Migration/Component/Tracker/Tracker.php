<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Tracker;

/**
 * The Tracker class provides methods to track contents during migration
 * process.
 */
class Tracker
{
    /**
     * The number of parsed items.
     *
     * @var string
     */
    protected $count = null;

    /**
     * Initializes the Tracker.
     *
     * @param Connection $conn   The database connection.
     * @param array      $config The tracker configuration.
     */
    public function __construct($conn, $config)
    {
        $this->conn   = $conn;
        $this->config = $config;
    }

    /**
     * Checks and creates table to track fixed items.
     */
    public function start()
    {
        $q      = 'CREATE TABLE IF NOT EXISTS migration_fix(%s)';
        $fields = [];

        foreach ($this->config['fields'] as $field) {
            $fields[] = $field . ' VARCHAR(255) DEFAULT NULL';
        }

        $q = sprintf($q, implode(',', $fields));

        $this->conn->executeQuery($q);
    }

    /**
     * Adds a new item to the table of fixed items.
     *
     * @param array $fixed Array of values to identify the fixed item.
     */
    public function add($fixed)
    {
        if (is_null($this->count)) {
            $this->count();
        }

        $data = [ $this->config['fields'][0] => $fixed ];

        $this->conn->insert('migration_fix', $data);
        $this->count++;
    }

    /**
     * Returns number of already fixed items.
     *
     * @return mixed The number of already fixed items.
     */
    public function count()
    {
        if (is_null($this->count)) {
            $sql = "SELECT COUNT(*) AS total FROM migration_fix";
            $r   = $this->conn->fetchAll($sql);

            $this->count = (int) $r[0]['total'];
        }

        return $this->count;
    }

    /**
     * Removes the table to track fixed items.
     */
    public function end()
    {
        $this->conn->executeQuery('DROP TABLE IF EXISTS migration_fix');
    }
}
