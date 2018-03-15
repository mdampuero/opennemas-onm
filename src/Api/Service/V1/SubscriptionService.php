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

use Api\Exception\GetItemException;

class SubscriptionService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            $oql = sprintf('pk_user_group = %s and type = 1', $id);

            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
            throw new GetItemException();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getList($oql = '')
    {
        $oql = $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type = 1')
            ->getOql();

        return parent::getList($oql);
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
