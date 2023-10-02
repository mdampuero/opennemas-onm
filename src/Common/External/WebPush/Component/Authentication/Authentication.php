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
    protected $configurationProvider;

    /**
     * The WebPush API URL.
     *
     * @var string
     */
    protected $url;

    /**
     * Initializes the Authentication service.
     *
     * @param ConfigurationProvider $configProvider The configuration provider.
     * @param Client                $client         The HTTP client.
     * @param string                $url            The WebPush API URL.
     */
    public function __construct($configProvider, $client, $url)
    {
        $this->client         = $client;
        $this->configProvider = $configProvider;
        $this->url            = $url;
        $this->config         = $this->configProvider->getConfiguration();
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
        $data = [
            'headers' => [
                'content-type'      => 'application/json',
                'webpushrKey'       => $this->config['webpushrKey'],
                'webpushrAuthToken' => $this->config['webpushrAuthToken']
            ]
        ];

        try {
            $response = $this->client->post($this->url . 'v1/authentication', $data);
        } catch (\Exception $e) {
            throw new WebPushException('web_push.authentication.failure: ' . $e->getMessage());
        }

        $body = json_decode($response->getBody(), true);

        if (empty($body)) {
            throw new WebPushException('web_push.authentication.failure: no response');
        }
    }
}
