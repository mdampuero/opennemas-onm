<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\FreshBooks\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\PaymentNotFoundException;

class PaymentPersister extends FreshBooksPersister
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
        $this->api->setMethod('payment.create');
        $this->api->post([ 'payment' => $this->clean($entity) ]);

        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the payment in FreshBooks.
     *
     * @param Entity $entity The payment to update.
     *
     * @throws PaymentNotFoundException If the payment does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('payment.delete');
        $this->api->post([ 'payment_id' => $entity->client_id ]);
        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new PaymentNotFoundException(
            $entity->payment_id,
            $this->source,
            $this->api->getError()
        );
    }

    /**
     * Updates the payment in FreshBooks.
     *
     * @param Entity $entity The payment to update.
     *
     * @throws PaymentNotFoundException If the payment does not exist.
     */
    public function update(Entity $entity)
    {
        $data = $this->clean($entity);

        $this->api->setMethod('payment.update');
        $this->api->post([ 'payment' => $data ]);
        $this->api->request();

        if ($this->api->success()) {
            return;
        }

        throw new PaymentNotFoundException(
            $entity->payment_id,
            $this->source,
            $this->api->getError()
        );
    }

    /**
     * Converts an entity to an array to send to FreshBooks.
     *
     * @param Entity $entity The entity to convert.
     *
     * @return array The array.
     */
    private function clean($entity)
    {
        $data = [
            'invoice_id' => $entity->invoice_id,
            'date'       => $entity->date,
            'notes'      => $entity->notes,
            'type'       => $entity->type,
            'amount'     => str_replace(',', '.', $entity->amount),
        ];

        return $data;
    }
}
