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
     * @throws WebPushException If the action fails.
     */
    public function sendNotification($params)
    {
        try {
            $url     = $this->url . $this->config['actions']['send_notification']['path'];
            $headers = [
                'content-type'      => 'application/json',
                'webpushrKey'       => $this->auth->getConfiguration()['webpushrKey'],
                'webpushrAuthToken' => $this->auth->getConfiguration()['webpushrAuthToken']
            ];

            if (array_key_exists('image', $params)) {
                $parts = explode('.', $params['image']);
                if (empty(end($parts))) {
                    unset($params['image']);
                }
            }

            $data = [
                'headers' => $headers,
                'json' => [
                    'title'          => $params['title'],
                    'message'        => $params['message'],
                    'target_url'     => $params['target_url'],
                    'icon'           => $params['icon'] ?? null,
                    'image'          => $params['image'] ?? null
                ]
            ];

            $data['json'] = array_filter($data['json']);
            $response     = $this->client->post($url, $data);
            $body         = json_decode($response->getBody(), true);
            if ($body['status'] == 'success') {
                getService('application.log')
                    ->info('Notification ' . $body['ID'] . ' was sent successfully');
            }
            if ($body['status'] != 'success') {
                getService('application.log')
                    ->info('Notification sending has failed because of ' . $body['description']);
            }
        } catch (\Exception $e) {
            getService('application.log')
                ->error('Error sending the notification to server with params '
                . json_encode($params)
                . $e->getMessage());
            throw new WebPushException('webpush.notification.send.failure: ' . $e->getMessage());
        }
        return $body;
    }
}
