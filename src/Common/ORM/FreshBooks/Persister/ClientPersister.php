<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\FreshBooks\Persister;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

/**
 * The ClientPersister class defines actions to create, update and remove
 * Clients from FreshBooks.
 */
class ClientPersister extends BasePersister
{
    /**
     * Saves a new client in FreshBooks.
     *
     * @param Entity $entity The client to save.
     *
     * @throws \RuntimeException If the the client can not be saved.
     */
    public function create(Entity &$entity)
    {
        $data = $this->converter->freshbooksfy($entity->getData());

        $this->api->setMethod('client.create');
        $this->api->post([ 'client' => $data ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->id = (int) $response['client_id'];

            return;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @throws EntityNotFoundException
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('client.delete');
        $this->api->post([ 'client_id' => $entity->id ]);
        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new EntityNotFoundException(
            $this->metadata->name,
            $entity->id,
            $this->api->getError()
        );
    }

    /**
     * Updates the client in FreshBooks.
     *
     * @param Entity $entity The client to update.
     *
     * @throws EntityNotFoundException
     */
    public function update(Entity $entity)
    {
        $data = $this->converter->freshbooksfy($entity->getData());

        $this->api->setMethod('client.update');
        $this->api->post([ 'client' => $data ]);
        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new EntityNotFoundException(
            $this->metadata->name,
            $entity->id,
            $this->api->getError()
        );
    }
}
