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

class ContactEndpoint extends Endpoint
{
    /**
     * Adds a new contact in Act-On list.
     *
     * @param array $params The action parameters.
     *
     * @return string Record inserted message.
     *
     * @throws InvalidArgumentException If parameters are invalid.
     * @throws ActOnException           If the action fails.
     */
    public function addContact($listId, $params)
    {
        if (!$this->areParametersValid($params, 'add_contact')) {
            throw new \InvalidArgumentException();
        }

        $url  = $this->url . $this->config['actions']['add_contact']['path'];
        $url  = str_replace('{listId}', $listId, $url);
        $data = [
            'headers' => [
                'authorization' => 'Bearer ' . $this->auth->getToken(),
                'content-type'  => 'application/json'
            ],
            'body' => $params['contact']
        ];

        try {
            $response = $this->client->post($url, $data);
            $body     = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new ActOnException('acton.contact.add.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('status', $body)
            || $body['status'] !== 'success'
            || !array_key_exists('message', $body)
        ) {
            throw new ActOnException('acton.contact.add.failure');
        }

        return $body['message'];
    }

    /**
     * Gets a contact from an Act-On list.
     *
     * @param string $listId The Act-on list id.
     * @param array  $params The action parameters.
     *
     * @return array The contact data.
     *
     * @throws \InvalidArgumentException If parameters are invalid.
     * @throws ActOnException            If the action fails.
     */
    public function getContact($listId, $params)
    {
        if (!$this->areParametersValid($params, 'get_contact')) {
            throw new \InvalidArgumentException();
        }

        $url = $this->url . $this->config['actions']['get_contact']['path'];
        $url = str_replace('{listId}', $listId, $url);

        $data = [
            'headers' => [
                'authorization' => 'Bearer ' . $this->auth->getToken(),
            ],
            'query' => $params
        ];

        try {
            $response = $this->client->get($url, $data);
            $body     = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new ActOnException('acton.contact.get.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('contactID', $body)) {
            throw new ActOnException('acton.contact.get.failure');
        }

        return $body;
    }

    /**
     * Checks for an existing contact on Act-On list.
     *
     * @param string $listId The Act-on list id.
     * @param string $email  The contact email.
     *
     * @return boolean true  If the contact exists.
     *
     * @throws ActOnException If the action fails.
     */
    public function existContact($listId, $email)
    {
        try {
            $contact = $this->getContact($listId, [ 'email' => $email ]);
        } catch (\Exception $e) {
            // Check for contact not found
            if (preg_match('/errorCode.*10162/is', $e->getMessage())) {
                return false;
            }

            throw new ActOnException('acton.contact.get.failure: ' . $e->getMessage());
        }

        if (!array_key_exists('contactID', $contact)) {
            throw new ActOnException('acton.contact.get.failure');
        }

        return true;
    }
}
