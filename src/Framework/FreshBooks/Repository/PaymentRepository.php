<?php

namespace Framework\FreshBooks\Repository;

use Framework\FreshBooks\Entity\Payment;
use Framework\FreshBooks\Exception\PaymentNotFoundException;
use Framework\FreshBooks\Exception\InvalidCriteriaException;

class PaymentRepository extends Repository
{
    /**
     * Find a payment by id.
     *
     * @param integer $id The payment id.
     *
     * @return payment The payment.
     *
     * @throws paymentNotFoundException When the payment id is invalid.
     */
    public function find($id)
    {
        $this->api->setMethod('payment.get');
        $this->api->post([ 'payment_id' => $id ]);
        $this->api->request();

        if ($this->api->success()) {
            $response = $this->api->getResponse();

            return new Payment($response['payment']);
        }

        throw new PaymentNotFoundException($this->api->getError());
    }

    /**
     * Finds a list of payments basing on a criteria.
     *
     * @param array $criteria The criteria.
     *
     * @return array The list of payments.
     */
    public function findBy($criteria = null)
    {
        $this->api->setMethod('payment.list');
        $this->api->post($criteria);
        $this->api->request();

        $response = [];

        if ($this->api->success()) {
            $payments = [];

            $response = $this->api->getResponse();

            if (array_key_exists('payments', $response)
                && array_key_exists('payment', $response['payments'])
            ) {
                if ($response['payments']['@attributes']['total'] == 1) {
                    $payments[] = new Payment($response['payments']['payment']);
                } else {
                    $response = $response['payments']['payment'];

                    foreach ($response as $data) {
                        $payments[] = new Payment($data);
                    }
                }
            }

            return $payments;
        }

        throw new InvalidCriteriaException($this->api->getError());
    }
}
