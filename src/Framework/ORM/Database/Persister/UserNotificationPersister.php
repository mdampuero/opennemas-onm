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

use Framework\ORM\Core\Entity;

class UserNotificationPersister extends DatabasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $data = $this->dbfy($entity);

        $this->iconn->insert('user_notification', $data);

        $entity->id = $this->iconn->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->iconn->delete(
            'user_notification',
            [ 'user_id' => $entity->user_id, 'notification_id' => $entity->notification_id ]
        );

        $this->icache->delete($entity->getCachedId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $data = $this->dbfy($entity);

        unset($data['notification_id']);
        unset($data['user_id']);

        $this->iconn->update(
            'user_notification',
            $data,
            [ 'user_id' => $entity->user_id, 'notification_id' => $entity->notification_id ]
        );

        $this->icache->delete($entity->getCachedId());
    }
}
