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
     * @throws InvalidArgumentException If parameters are invalid.
     * @throws WebPushException         If the action fails.
     */
    public function getSubscribers()
    {
        $url     = $this->url . $this->config['actions']['get_subscribers']['path'];
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
            throw new WebPushException('webpush.subscribers.get.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('total_life_time_subscribers', $body)
            && !array_key_exists('active_subscribers', $body)
        ) {
            throw new WebPushException('webpush.subscribers.get.failure');
        }

        return $body;
    }
}
