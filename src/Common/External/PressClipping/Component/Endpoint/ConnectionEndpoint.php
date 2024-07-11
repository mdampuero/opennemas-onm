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
     * Test API Connection
     *
     * @return string
     *
     * @throws PressClippingException if the action fails
     */
    public function testConnection($params = [])
    {
        try {
            $url = $this->url . $this->replaceUriWildCards(
                $this->config['actions']['test_connection']['path'],
                $params
            );

            $response = $this->client->get($url);

            // Decodificar el cuerpo de la respuesta
            $body = json_decode($response->getBody(), true);

            return $body;
        } catch (\Exception $e) {
            throw new PressClippingException(
                'pressclipping.connection.get.failure: ' . $e->getMessage()
            );
        }

        return $body;
    }
}
