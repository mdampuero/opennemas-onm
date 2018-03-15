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

class SubscriptionService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        // Force type value
        $data['type'] = 1;

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $item = parent::getItem($id);

            if ($item->type !== 1) {
                throw new \Exception('Unable to find subscription');
            }

            return $item;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql)
    {
         // Force OQL to include the subscription flag enabled
        $oql = $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type = 1')
            ->getOql();

        return parent::getList($oql);
    }

    /**
     * {@inheritdoc}
     */
    public function patchItem($id, $data)
    {
        // Ignore type value for non-MASTER users
        if (array_key_exists('type', $data)
            && !$this->container->get('core.security')->hasPermission('MASTER')
        ) {
            unset($data['type']);
        }

        parent::patchItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function patchList($ids, $data)
    {
        // Ignore type value for non-MASTER users
        if (array_key_exists('type', $data)
            && !$this->container->get('core.security')->hasPermission('MASTER')
        ) {
            unset($data['type']);
        }

        return parent::patchList($ids, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        $data['type'] = 1;

        parent::updateItem($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($ids)
    {
        $oql = parent::getOqlForList($ids);

         // Force OQL to include the type value
        return $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type = 1')
            ->getOql();
    }
}
