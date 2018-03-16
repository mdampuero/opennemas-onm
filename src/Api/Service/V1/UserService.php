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

class UserService extends OrmService
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

        return parent::createItem($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $oql = sprintf('id = %s and type != 1', $id);

            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
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
         // Force OQL to include the type
        $oql = $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type != 1')
            ->getOql();

        return parent::getList($oql);
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
