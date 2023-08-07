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

class NotificationEndpoint extends Endpoint
{
    /**
     * Sends a push notification to all WebPush subscribers.
     *
     * @param array  $params The notification parameters.
     *
     * @return array
     *
     * @throws \InvalidArgumentException If parameters are invalid.
     * @throws WebPushException            If the action fails.
     */
    public function sendNotification($params)
    {
        $url = $this->url . $this->config['actions']['send_notification']['path'];

        $headers = [
            'content-type'      => 'application/json',
            'webpushrKey'       => $this->auth->getConfiguration()['webpushrKey'],
            'webpushrAuthToken' => $this->auth->getConfiguration()['webpushrAuthToken']
        ];

        $data = [
            'headers' => $headers,
            'json' => [
                'title'          => $params['title'],
                'message'        => $params['message'],
                'target_url'     => $params['target_url']
            ]
        ];

        try {
            $response = $this->client->post($url, $data);
            $body     = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new WebPushException('webpush.notification.send.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('status', $body)
            && !array_key_exists('description', $body)
            && !array_key_exists('ID', $body)
        ) {
            throw new WebPushException('webpush.subscribers.get.failure');
        }

        return $body;
    }
}
