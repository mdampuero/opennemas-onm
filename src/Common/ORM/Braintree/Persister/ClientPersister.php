<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Braintree\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

class ClientPersister extends BraintreePersister
{
    /**
     * Saves a new client in Braintree.
     *
     * @param Entity $entity The client to save.
     *
     * @throws RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity)
    {
        $cr   = $this->factory->get('customer');
        $data = $this->clean($entity);

        $response = $cr::create($data);

        if ($response->success) {
            $entity->id = (int) $response->customer->id;

            return;
        }

        throw new \RuntimeException();
    }

    /**
     * Removes the client in Braintree.
     *
     * @param Entity $entity The client to remove.
     *
     * @throws EntityNotFoundException If the client does not exist.
     */
    public function remove(Entity $entity)
    {
        $cr       = $this->factory->get('customer');
        $response = $cr::delete($entity->id);

        if ($response->success) {
            return;
        }

        throw new EntityNotFoundException($entity->client_id, $this->source);
    }

    /**
     * Updates the client in Braintree.
     *
     * @param Entity $entity The client to update.
     *
     * @throws ClientNotFoundException If the client does not exist.
     */
    public function update(Entity $entity)
    {
        $cr   = $this->factory->get('customer');
        $data = $this->clean($entity);

        $response = $cr::update($entity->id, $data);

        if ($response->success) {
            return;
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
    private function clean($entity)
    {
        $data = [
            'id'        => $entity->id,
            'firstName' => $entity->first_name,
            'lastName'  => $entity->last_name,
            'email'     => $entity->email,
            'company'   => $entity->company,
            'phone'     => $entity->phone,
        ];

        return $data;
    }
}
