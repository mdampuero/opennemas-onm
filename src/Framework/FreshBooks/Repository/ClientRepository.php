<?php

namespace Framework\FreshBooks\Repository;

use Framework\FreshBooks\Entity\Client;
use Framework\FreshBooks\Exception\ClientNotFoundException;
use Framework\FreshBooks\Exception\InvalidCriteriaException;

class ClientRepository extends Repository
{
    /**
     * Find a client by id.
     *
     * @param integer $id The client id.
     *
     * @return Client The client.
     *
     * @throws ClientNotFoundException When the client id is invalid.
     */
    public function find($id)
    {
        $this->api->setMethod('client.get');
        $this->api->post([ 'client_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return new Client($response['client']);
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
    public function findBy($criteria = null)
    {
        $this->api->setMethod('client.list');
        $this->api->post($criteria);
        $this->api->request();

        $response = [];

        if ($this->api->success()) {
            $response = $this->api->getResponse();
            $response = $response['clients'];

            $clients = [];
            foreach($response as $data) {
                $clients[] = new Client($data);
            }

            return $clients;
        }

        throw new InvalidCriteriaException($this->api->getError());
    }
}
