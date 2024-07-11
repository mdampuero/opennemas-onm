<?php

/**
 * This file is part of the Onm package
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\External\PressClipping\Component\Endpoint;

use Common\External\PressClipping\Component\Exception\PressClippingException;

class UploadEndpoint extends Endpoint
{
    /**
     * Uploads data to a specific endpoint by constructing a URL with provided parameters
     * and an authentication token, and then making a GET request to that URL.
     *
     * @param array $params An associative array of parameters to replace in the URL.
     * @return array The response body decoded from JSON as an associative array.
     * @throws PressClippingException If there is an issue with the connection or request.
     */
    public function uploadData($params = [])
    {
        try {
            $token = $this->auth->getToken();

            $url = $this->url . $this->replaceUriWildCards(
                $this->config['actions']['upload_info']['path'],
                $params
            ) . '/' . $token;

            $response = $this->client->get($url);

            $body = json_decode($response->getBody(), true);

            return $body;
        } catch (\Exception $e) {
            throw new PressClippingException((
                'pressclipping.connection.failure: ' . $e->getMessage()
            ));
        }
    }
}
