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

class SubscriberService extends UserService
{
    /**
     * {@inheritdoc}
     */
    public function deleteItem($id)
    {
        try {
            $item = $this->getItem($id);

            // Convert to user if subscriber + user
            if ($item->type === 2) {
                $item->type = 0;

                $this->em->persist($item, $item->getOrigin());
                return;
            }

            $this->em->remove($item, $item->getOrigin());
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteList($ids)
    {
        if (!is_array($ids) || empty($ids)) {
            throw new DeleteListException('Invalid ids', 400);
        }

        $oql = $this->getOqlForIds($ids);

        try {
            $response = parent::getList($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteListException($e->getMessage(), $e->getCode());
        }

        $deleted = 0;
        foreach ($response['items'] as $item) {
            try {
                // Convert to user if subscriber + user
                if ($item->type === 2) {
                    $item->type = 0;

                    $this->em->persist($item, $item->getOrigin());
                    $deleted++;
                    continue;
                }

                $this->em->remove($item, $item->getOrigin());
                $deleted++;
            } catch (\Exception $e) {
                $this->container->get('error.log')->error($e->getMessage());
            }
        }

        return $deleted;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $oql = sprintf('id = %s and type != 0', $id);

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
    protected function getOqlForList($oql)
    {
         // Force OQL to include type
        return $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type != 0')
            ->getOql();
    }
}
