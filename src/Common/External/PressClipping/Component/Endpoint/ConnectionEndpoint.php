<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\PressClipping\Component\Endpoint;

use Common\External\PressClipping\Component\Exception\PressClippingException;

class ConnectionEndpoint extends Endpoint
{
    /**
     * Tests the connection to a specific endpoint by constructing a URL with
     * an authentication token and then making a GET request to that URL.
     *
     * @return array The response body decoded from JSON as an associative array.
     * @throws PressClippingException If there is an issue with the connection or request.
     */
    public function testConnection()
    {
        try {
            // Get authentication token
            $token = $this->auth->getToken();

            // Construct the URL with the token
            $url = $this->url . $this->replaceUriWildCards(
                $this->config['actions']['test_connection']['path'],
                []
            ) . '/' . $token;

            // Make a GET request to the URL
            $response = $this->client->get($url);

            // Decode the JSON response body to an associative array
            $body = json_decode($response->getBody(), true);

            // Return the response body
            return $body;
        } catch (\Exception $e) {
            // Throw a custom exception if there is a connection failure
            throw new PressClippingException(
                'pressclipping.connection.get.failure: ' . $e->getMessage()
            );
        }
    }
}
