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
            $url = $this->url
            . $this->replaceUriWildCards($this->config['actions']['send_notification']['path'], $params);

            $data = array_key_exists('data', $params) && !empty($params['data']) ? $params['data'] : [];

            $response = $this->client->post(
                $url,
                [
                    'headers'     => $this->auth->getAuthHeaders(),
                    'form_params' => $data
                ]
            );

            $body = json_decode($response->getBody(), true);

            if ($response->getStatusCode() == 200) {
                $notificationID = $body['ID'] ?? $body['id'] ?? '';
                getService('application.log')
                    ->info('Webpush notification was sent successfully (ID: ' . $notificationID . ' )');
            } else {
                getService('application.log')
                    ->info('Webpush notification has failed');
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
