<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Braintree\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

/**
 * The ClientPersister class defines actions to create, update and remove
 * Clients to Braintree.
 */
class ClientPersister extends BasePersister
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
        $data = $this->converter->braintreefy($entity->getData());

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

        throw new EntityNotFoundException($this->metadata->name, $entity->id);
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
        $data = $this->converter->braintreefy($entity->getData());

        $response = $cr::update($entity->id, $data);

        if ($response->success) {
            return;
        }

        throw new EntityNotFoundException('Client', $entity->id);
    }
}
