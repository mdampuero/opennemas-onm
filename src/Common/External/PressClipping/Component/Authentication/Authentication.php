<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\External\PressClipping\Component\Authentication;

use Common\External\PressClipping\Component\Exception\PressClippingException;
use GuzzleHttp\Client;

class Authentication
{
    /**
     * The HTTP Client
     *
     * @var Client
     */
    protected $client;

    /**
     * The configuration provider
     *
     * @var ConfigurationProvider
     */
    protected $configProvider;

    /**
     * The token provider
     *
     * @var TokenProvider
     */
    protected $tokenProvider;

    /**
     * The PressClipping API URL
     *
     * @var string
     */
    protected $url;

    /**
     * The Authentication config.
     *
     * @var mixed
     */
    protected $config;

    /**
     * Initializes the Authentication service.
     *
     * @param ConfigurationProvider $configProvider The Configuration provider.
     * @param TokenProvider         $tokenProvider  The token provider.
     * @param string                $url            The PressClipping API URL.
     */
    public function __construct($configProvider, $tokenProvider, string $url, Client $client = null)
    {
        $this->configProvider = $configProvider;
        $this->url            = $url;
        $this->tokenProvider  = $tokenProvider;
        $this->client         = $client ?? new Client();

        $this->config = $this->configProvider->getConfiguration();

        if ($this->configProvider->isTokenRequired()) {
            $this->tokenProvider->setNamespace(md5(json_encode($this->config)));
        }
    }

    /**
     * Get the Configuration Provider configuration
     *
     * @return mixed Configuration data
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Authenticates the current user, check if credentials are valid
     *
     * @throws PressClippingException
     */
    public function authenticate()
    {
        $requestParams = $this->configProvider->getConfigParams()['form_params'];

        if ($requestParams) {
            $this->tokenProvider->setAccessToken(
                $requestParams['pressClippingApiKey'],
                '3600'
            );
            $this->tokenProvider->setPubID(
                $requestParams['pressClippingPubID']
            );
        }
    }

    /**
     * Returns the access token
     *
     * @throws PressClippingException
     *
     * @return String The access token
     */
    public function getToken()
    {
        $this->authenticate();

        return $this->tokenProvider->getAccessToken();
    }

    public function getPubID()
    {
        $this->authenticate();

        return $this->tokenProvider->getPubID();
    }
}
