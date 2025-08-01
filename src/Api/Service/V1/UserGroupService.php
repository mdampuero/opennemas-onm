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
use Api\Exception\ApiException;

class UserGroupService extends OrmService
{
    /**
     * {@inheritdoc}
     */
    protected $defaults = [
        'type' => 0
    ];

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $oql = sprintf('pk_user_group = %s and type = 0', $id);

            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
        } catch (\Exception $e) {
            throw new GetItemException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns the number of users associated to a user group in a list of user
     * groups.
     *
     * @param array $items The list of user groups.
     *
     * @return array A list where the key is a user groups id and the value is
     *               the number of users associated to the user group.
     */
    public function getEmails($items)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = array_map(function ($a) {
            return $a->pk_user_group;
        }, $items);

        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findUsers($ids);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns the number of users associated to a user group in a list of user
     * groups.
     *
     * @param array $items The list of user groups.
     *
     * @return array A list where the key is a user groups id and the value is
     *               the number of users associated to the user group.
     */
    public function getStats($items)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = array_map(function ($a) {
            return $a->pk_user_group;
        }, $items);

        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->countUsers($ids);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getOqlForList($oql)
    {
         // Force OQL to include type
        return $this->container->get('orm.oql.fixer')->fix($oql)
            ->addCondition('type = 0')
            ->getOql();
    }
}
