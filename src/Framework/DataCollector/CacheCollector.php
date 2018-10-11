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

class CacheCollector extends DataCollector
{
    /**
     * The service container
     *
     * @var \Symfony\Component\DependencyInjection\Container
     **/
    public $container;

    /**
     * Initializes the DatabaseCollector
     *
     * @param \Symfony\Component\DependencyInjection\Container $container The service container.
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
        $caches['cache.connection.manager']  =
            $this->container->get('cache.manager')->getConnection('manager');
        $caches['cache.connection.instance'] =
            $this->container->get('cache.manager')->getConnection('instance');

        $caches['cache_manager'] = $this->container->get('cache_manager');
        $caches['cache']         = $this->container->get('cache');

        foreach ($caches as $key => $cache) {
            $data = $cache->getBuffer();

            foreach ($data as $call) {
                $this->data[] = [
                    'name'   => $key,
                    'method' => $call['method'],
                    'time'   => $call['time'],
                    'params' => $call['params']
                ];
            }
        }
    }

    /**
     * Returns the amount of non-MRU queries.
     *
     * @return integer The amount of non-MRU queries.
     */
    public function countNonMru()
    {
        return $this->getNonMruQueries($this->data);
    }

    /**
     * Returns the amount of MRU queries.
     *
     * @return integer The amount of MRU queries.
     */
    public function countMru()
    {
        return $this->getMruQueries($this->data);
    }

    /**
     * Returns the list of cache connections.
     *
     * @return array The list of executed queries.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the list of executed calls grouped by cache name.
     *
     * @return array The list of executed queries grouped by connection name.
     */
    public function getGrouped()
    {
        $grouped = [];

        foreach ($this->data as $value) {
            $grouped[$value['name']]['data'][] = $value;
        }

        foreach ($grouped as $key => $value) {
            $grouped[$key]['normal'] = $this->getNonMruQueries($value['data']);
            $grouped[$key]['mru']    = $this->getMruQueries($value['data']);

            usort($value['data'], function ($elemA, $elemB) {
                return $elemA['time'] > $elemB['time'];
            });
        }

        return $grouped;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'cache_collector';
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * Returns the list of MRU queries.
     *
     * @param array $calls The list of function calls.
     *
     * @return int The list of MRU queries.
     */
    protected function getMruQueries($calls)
    {
        return count(array_filter($calls, function ($item) {
            return array_key_exists('mru', $item['params']) && $item['params']['mru'];
        }));
    }

    /**
     * Returns the list of non-MRU queries.
     *
     * @param array $calls The list of function calls.
     *
     * @return int The list of cache queries.
     */
    protected function getNonMruQueries($calls)
    {
        return count(array_filter($calls, function ($item) {
            return !array_key_exists('mru', $item['params'])
                || !$item['params']['mru'];
        }));
    }
}
