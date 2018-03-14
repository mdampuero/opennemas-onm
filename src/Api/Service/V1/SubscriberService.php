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

use Api\Exception\CreateItemException;
use Api\Exception\DeleteItemException;
use Api\Exception\DeleteListException;
use Api\Exception\GetItemException;
use Api\Exception\PatchListException;
use Api\Exception\UpdateItemException;

class SubscriberService extends BaseService
{
    /**
     * {@inheritdoc}
     */
    public function createItem($data)
    {
        try {
            if (!array_key_exists('email', $data)) {
                throw new \Exception('The email is required', 400);
            }

            $oql   = sprintf('email = "%s"', $data['email']);
            $items = $this->getList($oql);

            if (!empty($items['results'])) {
                throw new \Exception('The email is already in use', 409);
            }
        } catch (\Exception $e) {
            throw new CreateItemException($e->getMessage(), $e->getCode());
        }

        // Force type value
        $data['type']      = 1;
        $data['activated'] = false;
        $data['username']  = $data['email'];

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $item = parent::getItem($id);

            if ($item->type !== 1 && $item->type !== 2) {
                throw new \Exception('Unable to find subscriber', 404);
            }

            return $item;
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
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
    public function responsify($item)
    {
        if (is_array($item)) {
            foreach ($item as &$i) {
                $i = $this->responsify($i);
            }

            return $item;
        }

        if ($item instanceof $this->class) {
            $item->eraseCredentials();
            return parent::responsify($item);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function updateItem($id, $data)
    {
        try {
            if (!array_key_exists('email', $data)) {
                throw new \Exception('The email is required', 400);
            }

            $oql   = sprintf('id != "%s" and email = "%s"', $id, $data['email']);
            $items = $this->getList($oql);

            if (!empty($items['results'])) {
                throw new \Exception('The email is already in use', 409);
            }
        } catch (\Exception $e) {
            throw new UpdateItemException($e->getMessage(), $e->getCode());
        }

        // Force type value for non-MASTER users
        if (!array_key_exists('type', $data)
            || !$this->container->get('core.security')->hasPermission('MASTER')
        ) {
            $data['type'] = 1;
        }

        $data['username'] = $data['email'];

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
