<?php

namespace Common\ORM\Braintree\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

class ClientPersister extends BraintreePersister
{
    /**
     * Saves a new client in FreshBooks.
     *
     * @param Entity $entity The client to save.
     *
     * @throws RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity)
    {
        $cr   = $this->factory->get('customer');
        $data = $this->entityToArray($entity);

        $response = $cr::create($data);

        if ($response->success) {
            $entity->client_id = $response->customer->id;

            return $this;
        }

        throw new \RuntimeException();
    }

    /**
     * Removes the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @throws EntityNotFoundException If the client does not exist.
     */
    public function remove(Entity $entity)
    {
        $cr       = $this->factory->get('customer');
        $response = $cr::delete($entity->client_id);

        if ($response->success) {
            return $this;
        }

        throw new EntityNotFoundException($entity->client_id, $this->source);
    }

    /**
     * Updates the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @throws EntityNotFoundException If the client does not exist.
     */
    public function update(Entity $entity)
    {
        $cr   = $this->factory->get('customer');
        $data = $this->entityToArray($entity);

        $response = $cr::update($entity->client_id, $data);

        if ($response->success) {
            return $this;
        }

        throw new EntityNotFoundException($entity->client_id, $this->source);
    }

    /**
     * Converts an entity to an array to send to Braintree.
     *
     * @param Entity $entity The entity to convert.
     *
     * @return array The array.
     */
    private function entityToArray($entity)
    {
        $data = [
            'id'        => $entity->client_id,
            'firstName' => $entity->first_name,
            'lastName'  => $entity->last_name,
            'email'     => $entity->email,
            'company'   => $entity->organization,
            'phone'     => $entity->phone,
        ];

        return $data;
    }
}
