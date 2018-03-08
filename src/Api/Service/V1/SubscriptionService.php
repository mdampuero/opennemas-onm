<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\PatchListException;
use Api\Exception\UpdateItemException;

class SubscriptionService extends UserGroupService
{
    /**
     * Creates a new subscription.
     *
     * @param array $data The subscription data.
     *
     * @return UserGroup The new subscription.
     */
    public function createItem($data)
    {
        // Force subscription value
        $data['subscription'] = true;

        return parent::createItem($data);
    }

    /**
     * Delete a subscription basing on the id.
     *
     * @param integer $id The subscription id.
     *
     * @throws DeleteItemException If the subscription could not be removed.
     */
    public function deleteItem($id)
    {
        try {
            $subscription = $this->getItem($id);

            if (!$subscription->subscription) {
                throw new \Exception('Unable to find subscription');
            }

            parent::deleteItem($id);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteItemException();
        }
    }

    /**
     * Deletes a list of subscriptions.
     *
     * @param array $ids The list of ids.
     *
     * @return integer The number of successfully deleted subscriptions.
     */
    public function deleteList($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        $em  = $this->container->get('orm.manager');
        $oql = sprintf(
            'subscription = 1 and pk_user_group in [%s]',
            implode(',', $ids)
        );

        $userGroups = $em->getRepository('UserGroup', $this->origin)
            ->findBy($oql);

        $deleted = 0;
        foreach ($userGroups as $userGroup) {
            try {
                $em->remove($userGroup, $userGroup->getOrigin());
                $deleted++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $deleted;
    }

    /**
     * Returns the subscription basing on the id.
     *
     * @param integer $id The subscription id.
     *
     * @return UserGroup The subscription.
     *
     * @throws GetItemException If the subscription was not found.
     */
    public function getItem($id)
    {
        try {
            $subscription = parent::getItem($id);

            if (!$subscription->subscription) {
                throw new \Exception('Unable to find subscription');
            }

            return $subscription;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException();
        }
    }

    /**
     * Returns a list of subscriptions basing on a criteria.
     *
     * @param string $oql The criteria.
     *
     * @return array The list of subscriptions.
     */
    public function getList($oql)
    {
         // Force OQL to include the subscription flag enabled
        $oql = $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('subscription = 1')
            ->getOql();

        return parent::getList($oql);
    }

    /**
     * Updates some subscription properties.
     *
     * @param integer $id   The subscription id.
     * @param array   $data The new subscription information.
     */
    public function patchItem($id, $data)
    {
        // Ignore subscription flag for  non-MASTER users
        if (array_key_exists('subscription', $data)
            && !$this->container->get('core.security')->hasPermission('MASTER')
        ) {
            unset($data['subscription']);
        }

        parent::patchItem($id, $data);
    }

    /**
     * Updates some properties for a list of subscriptions.
     *
     * @param array $ids  The list of ids.
     * @param array $data The properties to update.
     *
     * @return integer The number of successfully updated subscriptions.
     */
    public function patchList($ids, $data)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new PatchListException('Invalid ids', 400);
        }

        $em   = $this->container->get('orm.manager');
        $data = $em->getConverter('UserGroup')->objectify($data);
        $oql  = sprintf(
            'subscription = 1 and pk_user_group in [%s]',
            implode(',', $ids)
        );

        $userGroups = $em->getRepository('UserGroup', $this->origin)
            ->findBy($oql);

        $updated = 0;
        foreach ($userGroups as $userGroup) {
            try {
                $userGroup->merge($data);
                $em->persist($userGroup);

                $updated++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $updated;
    }

    /**
     * Updates a subscription.
     *
     * @param integer $id   The subscription id.
     * @param array   $data The subscription information.
     */
    public function updateItem($id, $data)
    {
        $data['subscription'] = true;

        parent::updateItem($id, $data);
    }
}
