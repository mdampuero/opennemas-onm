<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm;

/**
* Wrappers the AdoDB library for making it container compatible
*/
class DatabaseConnection
{

    /**
     * The database connection
     *
     * @var string
     **/
    public $conn = null;

    /**
     * undocumented class variable
     *
     * @var string
     **/
    public $conn_read_only = null;


    /**
     * undocumented class variable
     *
     * @var string
     **/
    private $useReplication = false;

    private $connnection_params = null;

    public function __construct()
    {
    }

    /**
     * Starts the database connection
     *
     * @return DatabaseConnection the object
     **/
    public function connect($params)
    {
        $this->connection_params = $params;
        $this->useReplication = false;

        if (array_key_exists('database_replication', $params)
            && $params['database_replication'] == true
        ) {
            $this->useReplication = true;
        };

        $this->connection_params = $params['connections'];

        foreach ($this->connection_params as $conn_params) {

            $connection = \ADONewConnection($conn_params['database_driver']);
            $connection->Connect(
                $conn_params['database_host'],
                $conn_params['database_user'],
                $conn_params['database_password'],
                $conn_params['database_name'],
                $conn_params['database_driver']
            );
            $connection->bulkBind = true;

            if ($this->useReplication
                && array_key_exists('slave', $conn_params)
                && $conn_params['slave'] == true) {
                $this->conn_read_only []= $connection;
            } else {
                $this->conn []= $connection;
            }
        }

        return $this;
    }

    /**
     * Returns the proper connection whether is a read or a write query
     *
     * @return the database connection
     **/
    public function getConnection($method, $params)
    {
        $isReadOnlyQuery = stripos($params[0], 'SELECT') !== false;

        if ($this->useReplication && $isReadOnlyQuery) {
            return $this->conn_read_only[0];
        } else {
            return $this->conn[0];
        }
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function __get($property)
    {
        return $this->conn->{$property};
    }

    public function __call($method, $params)
    {
        $connection = $this->getConnection($method, $params);
        // $rs = $connection->Execute('SELECT * FROM articles');
        $rs = call_user_func_array(array($connection, $method), $params);

        $this->error = $connection->ErrorMsg();

        return $rs;
    }
}

