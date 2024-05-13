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

class WebsiteEndpoint extends Endpoint
{
    /**
     * Get the data of the current websites.
     *
     * @return string
     *
     * @throws WebPushException If the action fails.
     */
    public function getList($params = [])
    {
        try {
            $url = $this->url . $this->replaceUriWildCards($this->config['actions']['get_list']['path'], $params);

            $response = $this->client->get($url, [ 'headers' => $this->auth->getAuthHeaders() ]);
            $body     = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            getService('application.log')
                ->error('Error retrieving the websites list from server '
                . $e->getMessage());
            throw new WebPushException('webpush.satus.get.failure: ' . $e->getMessage());
        }

        return $body;
    }
}
