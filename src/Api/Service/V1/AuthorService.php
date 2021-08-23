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
    protected $defaults = [
        'type'        => 0,
        'activated'   => 0,
        'user_groups' => [ [ 'user_group_id' => 3, 'status' => 1 ] ]
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

            $oql = sprintf('id = %s and type != 1 and user_group_id = 3', $id);

            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findOneBy($oql);
        } catch (\Exception $e) {
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
            ->addCondition('type != 1 and user_group_id = 3')
            ->getOql();
    }
}
