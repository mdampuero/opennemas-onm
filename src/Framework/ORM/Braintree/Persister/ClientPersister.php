<?php

namespace Framework\ORM\Braintree\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\ClientNotFoundException;

class ClientPersister extends BraintreePersister
{
    /**
     * Saves a new client in FreshBooks.
     *
     * @param Entity $entity The client to save.
     *
     * @return mixed The response from FreshBooks.
     *
     * @throws RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity)
    {
        $this->api->setMethod('client.create');
        $this->api->post([ 'client' => $entity->getData() ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->client_id = $response['client_id'];

            return $response;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @return mixed The response from FreshBooks.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('client.delete');
        $this->api->post([ 'client_id' => $entity->client_id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return $response;
        }

        throw new ClientNotFoundException($this->api->getError());
    }

    /**
     * Updates the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @return mixed The response from FreshBooks.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function update(Entity $entity)
    {
        $this->api->setMethod('client.update');
        $this->api->post([ 'client' => $entity->getData() ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return $response;
        }

        throw new ClientNotFoundException($this->api->getError());
    }
}
