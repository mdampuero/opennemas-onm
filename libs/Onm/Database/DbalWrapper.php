<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Database;

/**
 * Wrapper for the Doctrine DBAL.
 */
class DbalWrapper
{
    /**
     * The function call buffer.
     *
     * @var array
     */
    private $buffer = [];

    /**
     * The database connection.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection = null;

    /**
     * The current environment.
     *
     * @var string
     */
    private $env;

    /**
     * Creates a new Wrapper to Doctrine DBAL.
     *
     * @param string $params The array of parameters.
     * @param string $env    The current environment.
     *
     * @throws \Exception
     */
    public function __construct($params, $env)
    {
        $this->connectionParams = [];
        $this->env              = $env;

        if (!array_key_exists('dbal', $params)
            || (array_key_exists('dbal', $params)
            && (!array_key_exists('default_connection', $params['dbal'])
                || !array_key_exists('connections', $params['dbal'])))
        ) {
            throw new \Exception();
        }

        $default = $params['dbal']['default_connection'];

        $this->connectionParams['driver'] = 'pdo_mysql'; //$params['dbal']['connections'][$default]['driver'];

        if (array_key_exists('slaves', $params['dbal']['connections'][$default])) {
            $this->connectionParams['master'] = $params['dbal']['connections'][$default];
            unset($this->connectionParams['master']['slaves']);

            $this->connectionParams['slaves'] = array();
            $this->connectionParams['wrapperClass'] = 'Doctrine\DBAL\Connections\MasterSlaveConnection';

            foreach ($params['dbal']['connections'][$default]['slaves'] as $slave) {
                $this->connectionParams['slaves'][] = $slave;
            }
        } else {
            $this->connectionParams = array_merge($this->connectionParams, $params['dbal']['connections'][$default]);
        }
    }

    /**
     * Overwrites the slave database name with the given name.
     *
     * @param string $databaseName Database name to use as slave.
     *
     * @return DbalWrapper The current wrapper.
     */
    public function selectDatabase($databaseName)
    {
        $this->connectionParams = $this->replaceKeyInArray(
            function ($key, $value, $databaseName) {
                if ($key == 'dbname' && !is_null($value)) {
                    $value = $databaseName;
                }
                return $value;
            },
            $this->connectionParams,
            $databaseName
        );

        $this->resetConnection();

        return $this;
    }

    /**
     * Replaces the values in the array by using the given callback.
     *
     * @param callable $callback Function used to replace.
     * @param array    $array    Array where replace.
     * @param string   $value    New value.
     *
     * @return array Array with the replaced values.
     */
    private function replaceKeyInArray($callback, $array, $databaseName)
    {
        foreach (array_keys($array) as $key) {
            if (is_array($array[$key])) {
                $array[$key] = $this->replaceKeyInArray($callback, $array[$key], $databaseName);
            } else {
                $array[$key] = call_user_func($callback, $key, $array[$key], $databaseName);
            }
        }
        return $array;
    }

    /**
     * Redirects all the calls to the AdodbConnection instance.
     *
     * @param string $method the method to call.
     * @param array  $params the list of parameters to pass to the method.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, $params)
    {
        $connection = $this->getConnection();

        $this->addCallToBuffer($method, $params);

        $rs = call_user_func_array(array($connection, $method), $params);

        return $rs;
    }

    /**
     * Returns the buffer of function calls.
     *
     * @return array The current function calls buffer.
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Adds a method and params to the buffer
     *
     * @param string $method The called method.
     * @param mixed  $params The called params.
     */
    public function addCallToBuffer($method, $params)
    {
        if ($this->env !== 'prod'
            && !in_array($method, ['setFetchMode', 'SetFetchMode'])
        ) {
            $this->buffer[] = [
                'method' => $method,
                'params' => $params,
                'time'   => microtime(true)
            ];
        }
    }

    /**
     * Returns the current database connection.
     *
     * @return \Doctrine\DBAL\Connection The current database connection.
     * 
     * @throws \Exception
     */
    public function getConnection()
    {
        if (!is_object($this->connection)) {
            $config = new \Doctrine\DBAL\Configuration();
            $this->connection = \Doctrine\DBAL\DriverManager::getConnection($this->connectionParams, $config);
        }

        return $this->connection;
    }

    /**
     * Closes and deletes the current connection.
     */
    public function resetConnection()
    {
        if (is_object($this->connection)) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}
