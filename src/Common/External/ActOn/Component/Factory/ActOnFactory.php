<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\ActOn\Component\Factory;

use Common\External\ActOn\Component\Authentication\Authentication;
use GuzzleHttp\Client;

class ActOnFactory
{
    /**
     * The Act-On authentication service.
     *
     * @var Authentication
     */
    protected $auth;

    /**
     * The HTTP client
     *
     * @var Client
     */
    protected $client;

    /**
     * The Act-On configuration.
     *
     * @var array
     */
    protected $config;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the factory.
     *
     * @param ServiceContainer $container The service container.
     * @param array            $config    The Act-On configuration.
     */
    public function __construct($container, $config)
    {
        $this->config    = $config;
        $this->container = $container;
    }

    /**
     * Returns a configured authentication service.
     *
     * @return Authentication A configured authentication service.
     */
    public function getAuthentication()
    {
        if (empty($this->auth)) {
            $this->auth = new Authentication(
                $this->container->get($this->config['config_provider']),
                $this->container->get($this->config['token_provider']),
                $this->container->get($this->config['http_client']),
                $this->config['url']
            );
        }

        return $this->auth;
    }

    /**
     * Returns a new Guzzle client.
     *
     * @return Client The new Guzzle client.
     */
    public function getClient()
    {
        if (empty($this->client)) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Returns the service to interact with the requested endpoint.
     *
     * @param string $name The endpoint name.
     *
     * @return object The service to interact with the requested endpoint.
     */
    public function getEndpoint($name)
    {
        $container = $this->container;

        $class = '\\' . $this->config['endpoints'][$name]['class'];
        $class = new \ReflectionClass($class);
        $args  = [
            $this->container->get($this->config['http_client']),
            $this->container->get($this->config['config_provider']),
            $this->container->get($this->config['token_provider']),
            $this->config['url']
        ];

        if (array_key_exists('args', $this->config['endpoints'][$name])) {
            $args = array_merge(
                $args,
                array_map(function ($a) use ($container) {
                    return $a[0] === '@' ? $container->get($a) :
                        $container->getParameter($a);
                }, $this->config['endpoints'][$name]['args'])
            );
        }

        $endpoint = $class->newInstanceArgs($args);

        if (array_key_exists('config', $this->config['endpoints'][$name])) {
            $endpoint->setConfiguration($this->config['endpoints'][$name]['config']);
        }

        return $endpoint;
    }
}
