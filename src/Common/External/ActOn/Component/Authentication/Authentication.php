<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\ActOn\Component\Authentication;

use Common\External\ActOn\Component\Exception\ActOnException;

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
    protected $configurationProvider;

    /**
     * The token provider.
     *
     * @var TokenProvider
     */
    protected $tokenProvider;

    /**
     * The Act-On API URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Initializes the Authentication service.
     *
     * @param ConfigurationProvider $configProvider The configuration provider.
     * @param TokenProvider         $tokenProvider  The token provider.
     * @param Client                $client         The HTTP client.
     * @param string                $url            The Act-On API URL.
     */
    public function __construct($configProvider, $tokenProvider, $client, $url)
    {
        $this->client         = $client;
        $this->configProvider = $configProvider;
        $this->tokenProvider  = $tokenProvider;
        $this->url            = $url;

        $this->config = $this->configProvider->getConfiguration();

        $this->tokenProvider->setNamespace(md5(json_encode($this->config)));
    }

    /**
     * Authenticates the current user and saves the returned tokens in the token
     * provider.
     */
    public function authenticate()
    {
        $data = [
            'form_params' => [
                'grant_type'    => 'password',
                'username'      => $this->config['username'],
                'password'      => $this->config['password'],
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret']
            ]
        ];

        try {
            $response = $this->client->post($this->url . '/token', $data);
        } catch (\Exception $e) {
            throw new ActOnException('act_on.authentication.failure: ' . $e->getMessage());
        }

        $body = json_decode($response->getBody(), true);

        if (empty($body)
            || !array_key_exists('access_token', $body)
            || !array_key_exists('expires_in', $body)
            || !array_key_exists('refresh_token', $body)
        ) {
            throw new ActOnException('act_on.authentication.failure: no token found');
        }

        $this->tokenProvider
            ->setAccessToken($body['access_token'], $body['expires_in'])
            ->setRefreshToken($body['refresh_token']);
    }

    /**
     * Returns the access token.
     *
     * @return string The access token.
     *
     * @throws ActOnException
     */
    public function getToken()
    {
        if ($this->tokenProvider->hasAccessToken()) {
            return $this->tokenProvider->getAccessToken();
        }

        if ($this->tokenProvider->hasRefreshToken()) {
            $this->refreshToken();
            return $this->tokenProvider->getAccessToken();
        }

        $this->authenticate();

        return $this->tokenProvider->getAccessToken();
    }

    /**
     * Refreshes the access token.
     *
     * @throws ActOnException
     */
    public function refreshToken()
    {
        $data = [
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->tokenProvider->getRefreshToken(),
                'client_id'     => $this->config['client_id'],
                'client_secret' => $this->config['client_secret']
            ]
        ];

        try {
            $response = $this->client->post($this->url . '/token', $data);
        } catch (\Exception $e) {
            throw new ActOnException('act_on.authentication.failure');
        }

        $body = json_decode($response->getBody(), true);

        if (empty($body)
            || !array_key_exists('access_token', $body)
            || !array_key_exists('expires_in', $body)
            || !array_key_exists('refresh_token', $body)
        ) {
            throw new ActOnException('act_on.authentication.failure: no token found');
        }

        $this->tokenProvider
            ->setAccessToken($body['access_token'], $body['expires_in'])
            ->setRefreshToken($body['refresh_token']);
    }
}
