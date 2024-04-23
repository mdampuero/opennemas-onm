<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\WebPush\Component\Authentication;

use Common\External\WebPush\Component\Exception\WebPushException;

class Authentication
{
    /**
     * The HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * The configuration provider.
     *
     * @var ConfigurationProvider
     */
    protected $configProvider;

    /**
     * The token provider.
     *
     * @var TokenProvider
     */
    protected $tokenProvider;

    /**
     * The WebPush API URL.
     *
     * @var string
     */
    protected $url;

    protected $config;

    /**
     * Initializes the Authentication service.
     *
     * @param ConfigurationProvider $configProvider The configuration provider.
     * @param Client                $client         The HTTP client.
     * @param string                $url            The WebPush API URL.
     */
    public function __construct($configProvider, $client, $tokenProvider, $url)
    {
        $this->client         = $client;
        $this->configProvider = $configProvider;
        $this->url            = $url;
        $this->tokenProvider  = $tokenProvider;

        $this->config = $this->configProvider->getConfiguration();

        if ($this->configProvider->isTokenRequired()) {
            $this->tokenProvider->setNamespace(md5(json_encode($this->config)));
        }
    }

    /**
     * Get the Configuration Provider configuration
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    /**
     * Authenticates the current user, check if credetials are valid
     */
    public function authenticate()
    {
        $authParams = $this->configProvider->isTokenRequired() ? $this->getAuthHeaders() : $this->getConfiguration();
        $headers    = array_merge(
            [ 'content-type' => 'application/json' ],
            $authParams
        );

        try {
            $response = $this->client->post(
                $this->url . $this->configProvider->getAuthRoute(),
                [ 'headers' => $headers ]
            );
        } catch (\Exception $e) {
            throw new WebPushException('web_push.authentication.failure: ' . $e->getMessage());
        }

        $body = json_decode($response->getBody(), true);

        if (empty($body)) {
            throw new WebPushException('web_push.authentication.failure: no response');
        }
    }
}
