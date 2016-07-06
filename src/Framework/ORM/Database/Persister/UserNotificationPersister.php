<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Database\Persister;

use Framework\ORM\Entity\Entity;

class UserNotificationPersister extends DatabasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $data = $this->dbfy($entity);

        $this->mconn->insert('user_notification', $data);

        $entity->id = $this->mconn->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->mconn->delete(
            'user_notification',
            [
                'instance_id'     => $entity->instance_id,
                'user_id'         => $entity->user_id,
                'notification_id' => $entity->notification_id
            ]
        );

        $this->mcache->delete($entity->getCachedId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $data = $this->dbfy($entity);

        unset($data['notification_id']);
        unset($data['user_id']);

        $this->mconn->update(
            'user_notification',
            $data,
            [
                'instance_id'     => $entity->instance_id,
                'user_id'         => $entity->user_id,
                'notification_id' => $entity->notification_id
            ]
        );

        $this->mcache->delete($entity->getCachedId());
    }
}
