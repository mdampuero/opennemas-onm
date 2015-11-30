<?php

namespace Framework\ORM\Braintree\Persister;

use Framework\ORM\Core\Entity;
use Framework\ORM\Exception\ClientNotFoundException;

class ClientPersister extends BraintreePersister
{
    /**
     * Saves a new client in FreshBooks.
     *
     * @param Entity  $entity The client to save.
     * @param boolean $next   Whether to continue to the next persister.
     *
     * @throws RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity, $next = true)
    {
        $cr   = $this->factory->get('customer');
        $data = $this->entityToArray($entity);

        $response = $cr::create($data);

        if ($response->success) {
            $entity->client_id = $response->customer->id;

            if ($next && $this->hasNext()) {
                $this->next()->create($entity);
            }

            return $this;
        }

        throw new \RuntimeException();
    }

    /**
     * Removes the client in FreshBooks.
     *
     * @param Entity  $entity The client to update.
     * @param boolean $next   Whether to continue to the next persister.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function remove(Entity $entity, $next = true)
    {
        $cr       = $this->factory->get('customer');
        $response = $cr::delete($entity->client_id);

        if ($response->success) {
            if ($next && $this->hasNext()) {
                $this->next()->remove($entity);
            }

            return $this;
        }

        throw new ClientNotFoundException($entity->client_id, $this->source);
    }

    /**
     * Updates the client in FreshBooks.
     *
     * @param Entity  $entity The client to update.
     * @param boolean $next   Whether to continue to the next persister.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function update(Entity $entity, $next = true)
    {
        $cr   = $this->factory->get('customer');
        $data = $this->entityToArray($entity);

        $response = $cr::update($entity->client_id, $data);

        if ($response->success) {
            if ($next && $this->hasNext()) {
                $this->next()->remove($entity);
            }

            return $this;
        }

        throw new ClientNotFoundException($entity->client_id, $this->source);
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
