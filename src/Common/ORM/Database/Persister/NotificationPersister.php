<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Persister;

use Common\ORM\Core\Entity;

class NotificationPersister extends BasePersister
{
    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        parent::update($entity);

        $changes = $entity->getChanges();

        // Startime changed
        if (array_key_exists('start', $changes)) {
            $this->updateUserNotifications($entity->id);
        }
    }

    /**
     * Updates all user notifications for this notification.
     *
     * @param type variable Description
     *
     * @return type Description
     */
    protected function updateUserNotifications($id)
    {
        $this->conn->delete('user_notification', [ 'notification_id' => $id ]);

        if ($this->hasCache()) {
            $this->cache->removeByPattern('*user_notification-' . $id . '*');
        }
    }
}
