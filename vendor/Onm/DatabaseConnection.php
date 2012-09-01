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
    private $conn;

    public function __construct($dbType = 'mysqli')
    {
        $this->conn = \ADONewConnection($dbType);
    }

    /**
     * Starts the database connection
     *
     * @return DatabaseConnection the object
     **/
    public function connect($dbHost, $dbUser, $dbPass, $dbDatabase)
    {
        $this->conn->Connect($dbHost, $dbUser, $dbPass, $dbDatabase);

        $this->conn->bulkBind = true;

        return $this->conn;
    }

    /**
     * Forward all the undefined functions to Adodb object
     *
     **/
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->conn, $method), $args);
    }

    /**
     * Forward all the property getters to the Adodb object
     *
     **/
    public function __get($prop)
    {
        return $this->conn->{$prop};
    }

    /**
     * Forward all the property setters to the Adodb object
     *
     **/
    public function __set($method, $value)
    {
        return $this->conn->{$property} = $value;
    }
}

