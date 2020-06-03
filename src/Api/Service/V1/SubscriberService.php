<?php

namespace Api\Service\V1;

use Api\Exception\GetItemException;
use Api\Exception\GetListException;

class SubscriberService extends UserService
{
    /**
     * {@inheritdoc}
     */
    protected $defaults = [
        'type' => 1
    ];

    /**
     * The default type value for users.
     */
    protected $type = 1;

    /**
     * The type for subscriber + user when converted to user.
     */
    protected $ctype = 0;

    /**
     * {@inheritdoc}
     */
    public function getItem($id)
    {
        try {
            if (empty($id)) {
                throw new \InvalidArgumentException();
            }

            $oql = sprintf('id = %s and type != 0', $id);

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
            ->addCondition('type != 0')
            ->getOql();
    }

    /**
     * Get all the subscribers users for report.
     *
     * @return array The list of items.
     */
    public function getReport()
    {
        try {
            return $this->container->get('orm.manager')
                ->getRepository($this->entity, $this->origin)
                ->findSubscribers();
        } catch (\Exception $e) {
            throw new GetListException($e->getMessage(), $e->getCode());
        }
    }
}
