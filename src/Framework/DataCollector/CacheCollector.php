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
     * @param CacheInterface $cache The cache service.
     */
    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = $this->cache->getBuffer();
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
        return 'cache_collector';
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
