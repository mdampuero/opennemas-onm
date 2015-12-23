<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class DatabaseCollector extends DataCollector
{
    /**
     * Initializes the DatabaseCollector
     *
     * @param DbalWrapper $conn The database connection.
     */
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->conn->getBuffer();

        foreach ($this->data as &$query) {
            $sql    = array_slice($query['params'], 0, 1);
            $params = array_key_exists(0, array_slice($query['params'], 1)) ? array_slice($query['params'], 1)[0] : array();
            $query  = [
                'method' => $query['method'],
                'sql'    => $sql,
                'params' => (count($params) > 0) ? print_r($params, true) : '',
            ];
        }
    }

    /**
     * Returns the amount of executed queries.
     *
     * @return integer The amount of executed queries.
     */
    public function getCount()
    {
        return count($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'database_collector';
    }

    /**
     * Returns the list of executed queries.
     *
     * @return array The list of executed queries.
     */
    public function getQueries()
    {
        return $this->data;
    }
}
