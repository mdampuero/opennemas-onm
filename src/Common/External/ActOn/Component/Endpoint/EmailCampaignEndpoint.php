<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\ActOn\Component\Endpoint;

use Common\External\ActOn\Component\Exception\ActOnException;

class EmailCampaignEndpoint extends Endpoint
{
    /**
     * Creates a new message in Act-On
     *
     * @param array $params The action parameters.
     *
     * @return integer The message id.
     *
     * @throws InvalidArgumentException If parameters are invalid.
     * @throws ActOnException           If the action fails.
     */
    public function createMessage($params)
    {
        if (!$this->areParametersValid($params, 'create_message')) {
            throw new \InvalidArgumentException();
        }

        $url  = $this->url . $this->config['actions']['create_message']['path'];
        $data = [
            'headers' => [
                'authorization' => 'Bearer ' . $this->auth->getToken(),
            ],
            'form_params' => array_merge([ 'type' => 'draft' ], $params)
        ];

        try {
            $response = $this->client->post($url, $data);
            $body     = json_decode($response->getBody(), true);

            return $body['id'];
        } catch (\Exception $e) {
            throw new ActOnException('acton.email.failure: ' . $e->getMessage());
        }
    }

    /**
     * Sends a new message in Act-On.
     *
     * @param array $params The action parameters.
     *
     * @throws InvalidArgumentException If parameters are invalid.
     * @throws ActOnException           If the action fails.
     */
    public function sendMessage($id, $params)
    {
        if (!$this->areParametersValid($params, 'send_message')) {
            throw new \InvalidArgumentException();
        }

        $url = $this->url . $this->config['actions']['send_message']['path'];
        $url = str_replace('{id}', $id, $url);

        $data = [
            'headers' => [
                'authorization' => 'Bearer ' . $this->auth->getToken(),
            ],
            'form_params' => $params
        ];

        try {
            $this->client->post($url, $data);
        } catch (\Exception $e) {
            throw new ActOnException('acton.email.failure: ' . $e->getMessage());
        }
    }
}
