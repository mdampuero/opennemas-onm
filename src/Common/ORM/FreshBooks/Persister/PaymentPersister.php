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

class PaymentPersister extends BasePersister
{
    /**
     * Saves a new payment in FreshBooks.
     *
     * @param Entity $entity The payment to save.
     *
     * @throws RuntimeException If the the payment can not be saved.
     */
    public function create(Entity &$entity)
    {
        $data = $this->converter->freshbooksfy($entity);

        $this->api->setMethod('payment.create');
        $this->api->post([ 'payment' => $data ]);

        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the payment in FreshBooks.
     *
     * @param Entity $entity The payment to update.
     *
     * @throws EntityNotFoundException If the payment does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('payment.delete');
        $this->api->post([ 'payment_id' => $entity->client_id ]);
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
     * Updates the payment in FreshBooks.
     *
     * @param Entity $entity The payment to update.
     *
     * @throws EntityNotFoundException If the payment does not exist.
     */
    public function update(Entity $entity)
    {
        $data = $this->converter->freshbooksfy($entity->getData());

        $this->api->setMethod('payment.update');
        $this->api->post([ 'payment' => $data ]);
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
