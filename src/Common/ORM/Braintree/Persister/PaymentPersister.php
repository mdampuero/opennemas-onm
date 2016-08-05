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
use Common\ORM\Exception\EntityNotFoundException;

class PaymentPersister extends BasePersister
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
        // Force submit for settlement
        $entity->options = [ 'submitForSettlement' => true ];

        $cr   = $this->factory->get('transaction');
        $data = $this->converter->braintreefy($entity);

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
     * @throws EntityNotFoundException If the payment does not exist.
     */
    public function remove(Entity $entity)
    {
        $cr       = $this->factory->get('transaction');
        $response = $cr::void($entity->payment_id);

        if (!$response->success) {
            throw new EntityNotFoundException(
                $this->metadata->name,
                $entity->id,
                $this->api->getError()
            );
        }
    }
}
