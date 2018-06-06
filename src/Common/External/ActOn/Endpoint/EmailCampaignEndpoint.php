<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\ActOn\Endpoint;

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
     */
    public function createMessage($params)
    {
        if (!$this->areParametersValid($params, 'create_message')) {
            throw new \InvalidArgumentException();
        }

        $response = $this->post(array_merge([ 'type' => 'draft' ], $params));

        return $response['id'];
    }

    /**
     * Sends a new message in Act-On.
     *
     * @param array $params The action parameters.
     *
     * @throws InvalidArgumentException If parameters are invalid.
     */
    public function sendMessage($params)
    {
        if (!$this->areParametersValid($params, 'send_message')) {
            throw new \InvalidArgumentException();
        }

        $this->post($params);
    }
}
