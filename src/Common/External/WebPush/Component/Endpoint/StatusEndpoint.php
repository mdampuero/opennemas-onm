<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\External\WebPush\Component\Endpoint;

use Common\External\WebPush\Component\Exception\WebPushException;

class StatusEndpoint extends Endpoint
{
    /**
     * Get the data of the given Web Push notification.
     *
     * @return string
     *
     * @throws WebPushException If the action fails.
     */
    public function getStatus($id)
    {
        $url     = $this->url . $this->config['actions']['get_status']['path'] . $id;
        $headers = [
            'content-type'      => 'application/json',
            'webpushrKey'       => $this->auth->getConfiguration()['webpushrKey'],
            'webpushrAuthToken' => $this->auth->getConfiguration()['webpushrAuthToken']
        ];

        $data = [
            'headers' => $headers
        ];

        try {
            $response = $this->client->get($url, $data);
            $body     = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new WebPushException('webpush.satus.get.failure: ' . $e->getMessage());
        }

        return $body;
    }
}
