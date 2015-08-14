<?php

namespace Framework\FreshBooks\Persister;

use Framework\FreshBooks\Entity\Entity;
use Framework\FreshBooks\Exception\PaymentNotFoundException;

class PaymentPersister extends Persister
{
    /**
     * Saves a new payment in FreshBooks.
     *
     * @param Entity $entity The payment to save.
     *
     * @return mixed The response from FreshBooks.
     *
     * @throws RuntimeException If the the payment can not be saved.
     */
    public function create(Entity &$entity)
    {
        $this->api->setMethod('payment.create');
        $this->api->post([ 'payment' => $entity->getData() ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            $entity->payment_id = $response['payment_id'];

            return $response;
        }

        throw new \RuntimeException($this->api->getError());
    }

    /**
     * Removes the payment in FreshBooks.
     *
     * @param Entity $entity The payment to update.
     *
     * @return mixed The response from FreshBooks.
     *
     * @throws paymentNotFoundException If the payment does not exist.
     */
    public function remove(Entity $entity)
    {
        $this->api->setMethod('payment.delete');
        $this->api->post([ 'payment_id' => $entity->payment_id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return $response;
        }

        throw new PaymentNotFoundException($this->api->getError());
    }

    /**
     * Updates the payment in FreshBooks.
     *
     * @param Entity $entity The payment to update.
     *
     * @return mixed The response from FreshBooks.
     *
     * @throws paymentNotFoundException If the payment does not exist.
     */
    public function update(Entity $entity)
    {
        $this->api->setMethod('payment.update');
        $this->api->post([ 'payment' => $entity->getData() ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return $response;
        }

        throw new PaymentNotFoundException($this->api->getError());
    }
}
