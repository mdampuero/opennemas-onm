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
     * Initializes the CacheCollector
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container= $container;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $caches['cache.connection.manager'] =
            $this->container->get('cache.manager')->getConnection('manager');
        $caches['cache.connection.instance']  =
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

            usort($value['data'], function ($a, $b) {
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
        return 'cache_collector';
    }

    /**
     * Returns the list of MRU queries.
     *
     * @param array $calls The list of function calls.
     *
     * @return array The list of MRU queries.
     */
    protected function getMruQueries($calls)
    {
        return count(array_filter($calls, function ($a) {
            return array_key_exists('mru', $a['params']) && $a['params']['mru'];
        }));
    }

    /**
     * Returns the list of non-MRU queries.
     *
     * @param array $calls The list of function calls.
     *
     * @return array The list of cache queries.
     */
    protected function getNonMruQueries($calls)
    {
        return count(array_filter($calls, function ($a) {
            return !array_key_exists('mru', $a['params'])
                || !$a['params']['mru'];
        }));
    }
}
