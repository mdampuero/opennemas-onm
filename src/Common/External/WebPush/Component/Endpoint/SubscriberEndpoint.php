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

class SubscriberEndpoint extends Endpoint
{
    /**
     * Get the total and the active amount of webpush subscribers
     *
     * @param array $params The action parameters.
     *
     * @return string Record inserted message.
     *
     * @throws WebPushException If the action fails.
     */
    public function getSubscribers()
    {
        try {
            $url     = $this->url . $this->config['actions']['get_subscribers']['path'];
            $headers = [
                'content-type'      => 'application/json',
                'webpushrKey'       => $this->auth->getConfiguration()['webpushrKey'],
                'webpushrAuthToken' => $this->auth->getConfiguration()['webpushrAuthToken']
            ];

            $data = [
                'headers' => $headers
            ];

            $response = $this->client->get($url, $data);
            $body     = json_decode($response->getBody(), true);
            if ($body['active_subscribers'] >= '0') {
                getService('application.log')
                    ->info('Web Push active subscribers amount was retrieved successfully');
            }
        } catch (\Exception $e) {
            getService('application.log')
                ->error('Error retrieving the amount of Web Push active subscribers from server'
                . $e->getMessage());
            throw new WebPushException('webpush.subscribers.get.failure: ' . $e->getMessage());
        }

        return $body;
    }
}
