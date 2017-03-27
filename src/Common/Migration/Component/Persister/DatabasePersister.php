<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Migration\Component\Persister;

use Common\ORM\Core\Connection;

/**
 * The DatabasePersister class provides methods to save items to a data source.
 */
class DatabasePersister implements Persister
{
    /**
     * The persister configuration
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
     * Initializes the Persister.
     *
     * @param array $config The persister configuration.
     */
    public function __construct($config)
    {
        $config['connection']['dbname'] = $config['source']['database'];

        $this->config = $config;
        $this->conn   = new Connection($config['connection']);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($data)
    {
        $keys = array_flip($this->config['source']['id']);
        $id   = array_intersect_key($data, $keys);
        $data = array_diff_key($data, $keys);

        $this->conn->update($this->config['source']['table'], $data, $id);
    }
}
