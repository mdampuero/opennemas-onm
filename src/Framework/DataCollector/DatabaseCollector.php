<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
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
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $connections['orm.connection.manager'] =
            $this->container->get('orm.manager')->getConnection('manager');
        $connections['orm.connection.instance']  =
            $this->container->get('orm.manager')->getConnection('instance');
        $connections['dbal_connection'] =
            $this->container->get('dbal_connection');
        $connections['dbal_connection_manager'] =
            $this->container->get('dbal_connection_manager');

        foreach ($connections as $key => $connection) {
            $data = $connection->getBuffer();

            foreach ($data as &$call) {
                $sql    = array_shift($call['params']);
                $values = array_shift($call['params']);
                $c      = [
                    'name'   => $key,
                    'method' => $call['method'],
                    'time'   => $call['time'],
                    'params' => [
                        'sql'    => $sql,
                        'values' => $values
                    ]
                ];

                $this->data[] = $c;
            }
        }
    }

    /**
     * Returns the list of executed queries.
     *
     * @return array The list of executed queries.
     */
    public function getData()
    {
        usort($this->data, function ($a, $b) {
            return $a['time'] > $b['time'];
        });

        return $this->data;
    }

    /**
     * Returns the list of executed queries grouped by connection name.
     *
     * @return array The list of executed queries grouped by connection name.
     */
    public function getGrouped()
    {
        $grouped = [];

        foreach ($this->data as $value) {
            $grouped[$value['name']][] = $value;
        }

        foreach ($grouped as $value) {
            usort($value, function ($a, $b) {
                return $a['time'] > $b['time'];
            });
        }

        return $grouped;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'database_collector';
    }
}
