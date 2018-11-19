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

/**
 * The PaymentPersister class defines actions to create and remove Payments
 * to Braintree.
 */
class PaymentPersister extends BasePersister
{
    /**
     * Saves a new payment in Braintree.
     *
     * @param Entity $entity The payment to save.
     *
     * @throws \RuntimeException If the the payment can not be saved.
     */
    public function create(Entity &$entity)
    {
        $cr   = $this->factory->get('transaction');
        $data = $this->converter->braintreefy($entity);

        // Force submit for settlement
        $data['options'] = [ 'submitForSettlement' => true ];

        $response = $cr::sale($data);

        if (!$response->success) {
            throw new \RuntimeException(
                sprintf(_('Unable to create a %s'), $this->metadata->name)
            );
        }

        $entity->id = $response->transaction->id;
    }

    /**
     * Voids the payment in Braintree.
     *
     * @param Entity $entity The payment to void.
     *
     * @throws \RuntimeException If the payment does not exist.
     */
    public function remove(Entity $entity)
    {
        $cr       = $this->factory->get('transaction');
        $response = $cr::void($entity->id);

        if (!$response->success) {
            throw new \RuntimeException(
                sprintf(_('Unable to remove the %s'), $this->metadata->name)
            );
        }
    }
}
