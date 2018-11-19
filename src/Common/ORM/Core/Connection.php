<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Core;

use Framework\Component\Data\DataBuffer;
use Common\ORM\Core\Validation\Validable;

/**
 * The Connection class represents a database connection.
 */
class Connection extends DataBuffer implements Validable
{
    /**
     * The database connection.
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $conn = null;

    /**
     * Redirects all the calls to the doctrine connection.
     *
     * @param string $method the method to call.
     * @param array  $params the list of parameters to pass to the method.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, $params)
    {
        $conn = $this->getConnection();

        $this->addToBuffer($method, $params);

        $rs = call_user_func_array([ $conn, $method ], $params);

        return $rs;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassName()
    {
        return 'Connection';
    }

    /**
     * Returns the current database connection.
     *
     * @return \Doctrine\DBAL\Connection The current database connection.
     */
    public function getConnection()
    {
        if (empty($this->conn)) {
            $this->conn = \Doctrine\DBAL\DriverManager::getConnection(
                $this->getData(),
                new \Doctrine\DBAL\Configuration()
            );
        }

        return $this->conn;
    }

    /**
     * Closes and deletes the current connection.
     */
    public function resetConnection()
    {
        if (is_object($this->conn)) {
            $this->conn->close();
            $this->conn = null;
        }
    }

    /**
     * Changes the database name.
     *
     * @param string $database The database name.
     */
    public function selectDatabase($database)
    {
        $this->dbname = $database;

        $this->resetConnection();
    }
}
