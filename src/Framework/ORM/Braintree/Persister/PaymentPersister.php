<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Braintree\Persister;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\PaymentNotFoundException;

class PaymentPersister extends BraintreePersister
{
    /**
     * Saves a new payment in Braintree.
     *
     * @param Entity $entity The payment to save.
     *
     * @throws RuntimeException If the the payment can not be saved.
     */
    public function create(Entity &$entity)
    {
        $cr   = $this->factory->get('transaction');
        $data = $this->clean($entity);

        $response = $cr::sale($data);

        if (!$response->success) {
            throw new \RuntimeException($response);
        }

        $entity->payment_id = $response->transaction->id;
    }

    /**
     * Voids the payment in Braintree.
     *
     * @param Entity $entity The payment to void.
     *
     * @throws PaymentNotFoundException If the payment does not exist.
     */
    public function remove(Entity $entity)
    {
        $cr       = $this->factory->get('transaction');
        $response = $cr::void($entity->payment_id);

        if (!$response->success) {
            throw new PaymentNotFoundException($entity->payment_id, $this->source);
        }
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
            'customerId' => $entity->client_id,
            'amount'     => $entity->amount,
            'options'    => [
                'submitForSettlement' => true
            ]
        ];

        if ($entity->nonce) {
            $data['paymentMethodNonce'] = $entity->nonce;
        }

        return $data;
    }
}
