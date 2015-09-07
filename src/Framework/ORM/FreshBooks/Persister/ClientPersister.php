<?php

namespace Framework\ORM\FreshBooks\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\ClientNotFoundException;

class ClientPersister extends FreshBooksPersister
{
    /**
     * Saves a new client in FreshBooks.
     *
     * @param Entity $entity The client to save.
     * @param boolean $next  Whether to continue to the next persister.
     *
     * @throws RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity, $next = true)
    {
        $this->api->setMethod('client.create');
        $this->api->post([ 'client' => $entity->getData() ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->client_id = $response['client_id'];

            if ($next && $this->hasNext()) {
                $this->next()->create($entity);
            }

            return $this;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     * @param boolean $next  Whether to continue to the next persister.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function remove(Entity $entity, $next = true)
    {
        $this->api->setMethod('client.delete');
        $this->api->post([ 'client_id' => $entity->client_id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            if ($next && $this->hasNext()) {
                $this->next()->remove($entity);
            }

            return $this;
        }

        throw new ClientNotFoundException(
            $entity->client_id,
            $this->source,
            $this->api->getError()
        );
    }

    /**
     * Updates the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     * @param boolean $next  Whether to continue to the next persister.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function update(Entity $entity, $next = true)
    {
        $this->api->setMethod('client.update');
        $this->api->post([ 'client' => $entity->getData() ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            if ($next && $this->hasNext()) {
                $this->next()->update($entity);
            }

            return $this;
        }

        throw new ClientNotFoundException(
            $entity->client_id,
            $this->source,
            $this->api->getError()
        );
    }
}
