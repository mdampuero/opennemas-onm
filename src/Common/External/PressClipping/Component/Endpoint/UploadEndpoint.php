<?php

/**
 * This file is part of the Onm package
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\External\PressClipping\Component\Endpoint;

use Common\External\PressClipping\Component\Exception\PressClippingException;

class UploadEndpoint extends Endpoint
{
    /**
     * Uploads data (articles) to a specific endpoint by constructing a URL with an authentication token
     * and then making a POST request to that URL with article data.
     *
     * @param array $articles An array of articles to be uploaded.
     * @return array The response body decoded from JSON as an associative array.
     * @throws PressClippingException If there is an issue with the connection or request.
     */
    public function uploadData($articles)
    {
        try {
            // Get authentication token
            $token = $this->auth->getToken();

            // Get PublicationID
            $pubID = $this->auth->getPubID();

            // Get Core URL
            $coreUrl = getService('core.decorator.url');
            // Add PublicationID each Article
            foreach ($articles as &$article) {
                $article['publicationID'] = $pubID;
            }

            // Construct the URL with the token
            $url = $this->url . $this->config['actions']['upload_info']['path'] . '/' . $token;

            // Create the payload with the articles data
            $payload = [
                [
                    'name'     => 'actions',
                    'contents' => json_encode($articles),
                ]
            ];

            // Make a POST request to the URL with the payload
            $response = $this->client->post($url, [
                'multipart' => $payload,
            ]);

            // Decode the JSON response body to an associative array
            $body = json_decode($response->getBody(), true);

            // Return the response body
            return $body;
        } catch (\Exception $e) {
            // Throw a custom exception if there is a connection failure
            throw new PressClippingException(
                'pressclipping.connection.failure: ' . $e->getMessage()
            );
        }
    }
}
