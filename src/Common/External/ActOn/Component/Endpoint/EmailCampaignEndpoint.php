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
        } catch (\Exception $e) {
            throw new ActOnException('acton.email.create.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('status', $body)
            || $body['status'] !== 'success'
            || !array_key_exists('id', $body)
        ) {
            throw new ActOnException('acton.email.create.failure');
        }

        return $body['id'];
    }

    /**
     * Sends a new message in Act-On.
     *
     * @param array $params The action parameters.
     *
     * @return array
     *
     * @throws \InvalidArgumentException If parameters are invalid.
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
            $response = $this->client->post($url, $data);
            $body     = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new ActOnException('acton.email.send.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('status', $body)
            || $body['status'] !== 'success'
            || !array_key_exists('message', $body)
        ) {
            throw new ActOnException('acton.email.create.failure');
        }

        return $body['message'];
    }
}
