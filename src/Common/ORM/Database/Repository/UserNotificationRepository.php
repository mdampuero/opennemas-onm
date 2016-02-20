<?php

namespace Common\ORM\Database\Repository;

use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

class UserNotificationRepository extends DatabaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`notification_id` ASC';

        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT notification_id, user_id FROM `" . $this->getCachePrefix() . "` "
            ."WHERE $filterSQL ORDER BY $orderBySQL $limitSQL";

        $rs = $this->conn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $item) {
            $key = 'user_notification-' . $item['notification_id'] . '-'
                . $item['user_id'];

            $ids[$key] = [
                'notification_id' => $item['notification_id'],
                'user_id'         => $item['user_id']
            ];
        }

        return $this->findMulti($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function findMulti($data)
    {
        $ids  = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[]  = $this->getCachePrefix() . $this->cacheSeparator
                . implode('-', $value);

            $keys[] = $value;
        }

        $entities = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($entities as $entity) {
            $cachedIds[] = $entity->getCachedId();
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $entity) {
            $entity = $this->find($data[$entity]);
            if ($entity) {
                $entities[] = $entity;
            }
        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($entities)
                && ($entities[$i]->notification_id != $id['notification_id']
                || $entities[$i]->user_id != $id['user_id'])
            ) {
                $i++;
            }

            if ($i < count($entities)) {
                $ordered[] = $entities[$i];
            }
        }

        return $ordered;
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(Entity &$entity)
    {
        if (empty($entity->notification_id) || empty($entity->user_id)) {
            throw new EntityNotFoundException(
                "Could not find user_notification with notification_id = "
                . $entity->user_notification . "and user_id = "
                . $entity->user_id
            );
        }

        $sql = 'SELECT * FROM ' . $this->getCachePrefix()
            . ' WHERE notification_id = ' . $entity->notification_id
            . ' AND user_id = ' . $entity->user_id;

        $rs = $this->conn->fetchAssoc($sql);

        if (!$rs) {
            throw new EntityNotFoundException(
                "Could not find user_notification with notification_id = "
                . $entity->user_notification . "and user_id = "
                . $entity->user_id
            );
        }

        foreach ($rs as $key => $value) {
            $entity->{$key} = $value;

            $value = @unserialize($value);

            if ($value) {
                $entity->{$key} = $value;
            }
        }

        $entity->refresh();
    }
}
