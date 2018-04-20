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
use Api\Exception\GetItemException;

class AuthorService extends UserService
{
    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $oql = sprintf('id = %s and user_group_id = 3', $id);

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
    public function deleteItem($id)
    {
        if ($id == $this->container->get('core.user')->id) {
            throw new DeleteItemException('You cannot delete this item', 403);
        }

        try {
            $item = $this->getItem($id);

            unset($item->user_groups[3]);
            $item->fk_user_group = array_diff($item->fk_user_group, [ 3 ]);

            $this->em->persist($item, $item->getOrigin());
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new DeleteItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
         // Force OQL to include type
        return $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('user_group_id = 3')
            ->getOql();
    }
}
