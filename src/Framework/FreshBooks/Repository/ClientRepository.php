<?php

namespace Framework\FreshBooks\Repository;

use Framework\FreshBooks\Exception\ClientNotFoundException;
use Framework\FreshBooks\Exception\InvalidCriteriaException;

class ClientRepository extends Repository
{
    /**
     * Find a client by id.
     *
     * @param integer $id The client id.
     *
     * @return array The client.
     *
     * @throws ClientNotFoundException When the client id is invalid.
     */
    public function findClient($id)
    {
        $this->api->setMethod('client.get');
        $this->api->post([ 'client_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            unset($response['@attributes']);

            return $response = $response['client'];
        }

        throw new ClientNotFoundException($this->api->getError());
    }

    /**
     * Finds a list of clients basing on a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of clients.
     */
    public function findClients($criteria)
    {
        $this->api->setMethod('client.list');
        $this->api->post($criteria);
        $this->api->request();

        $response = [];

        if ($this->api->success()) {
            $response = $this->api->getResponse();
            $response = $response['clients'];

            unset($response['@attributes']);

            return array_values($response);
        }

        throw new InvalidCriteriaException($this->api->getError());
    }
}
