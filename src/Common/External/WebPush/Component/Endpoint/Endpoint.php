<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\WebPush\Component\Endpoint;

class Endpoint
{
    /**
     * The WebPush authentication service.
     *
     * @var Authentication
     */
    protected $auth;

    /**
     * The HTTP client
     *
     * @var object
     */
    protected $client;

    /**
     * The endpoint configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * The WebPush API URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Initializes the endpoint.
     *
     * @param ConfigurationProvider $configProvider The configuration provider.
     * @param Client                $client         The HTTP client.
     * @param string                $url            The WebPush API URL.
     */
    public function __construct($auth, $client, $url)
    {
        $this->client = $client;
        $this->auth   = $auth;
        $this->url    = $url;
    }

    /**
     * Checks if parameters are valid.
     *
     * @param array  $params The array of parameters.
     * @param string $action The name of the action.
     *
     * @return boolean True parameters are valid. False otherwise.
     */
    public function areParametersValid($params, $action)
    {
        if (!array_key_exists($action, $this->config['actions'])
            || !array_key_exists('parameters', $this->config['actions'][$action])
            || !is_array($params)
        ) {
            return false;
        }

        $parameters = $this->config['actions'][$action]['parameters'];
        $required   = [];
        $invalid    = [];

        if (array_key_exists('required', $parameters)) {
            $required = array_diff($parameters['required'], array_keys($params));
        }

        if (array_key_exists('optional', $parameters)) {
            $invalid = array_diff(
                array_keys($params),
                $parameters['required'],
                $parameters['optional']
            );
        }

        return empty($required) && empty($invalid);
    }

    /**
     * Returns the endpoint configuration.
     *
     * @return array The endpoint configuration.
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Sets the endpoint configuration.
     *
     * @param array $config The endpoint configuration.
     */
    public function setConfiguration($config = [])
    {
        if (!is_array($config)) {
            return;
        }

        $this->config = $config;
    }

    public function replaceUriWildCards($uri, $parameters)
    {
        preg_match_all('@.*({.+})@U', $uri, $matches);
        if (!array_key_exists(1, $matches) || empty($matches[1])) {
            return $uri;
        }

        foreach ($matches[1] as $wildcard) {
            $key = trim($wildcard, '{}');
            if (!array_key_exists($key, $parameters)) {
                throw new \Exception("Error procesing endpoint uri wildcards");
            }

            $uri = str_replace($wildcard, (string) $parameters[$key], $uri);
        }

        return $uri;
    }
}
