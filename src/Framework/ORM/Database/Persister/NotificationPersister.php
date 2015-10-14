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

class NotificationPersister extends DatabasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $this->mconn->insert('notification', $entity->getData());

        $entity->id = $this->mconn->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->mconn->delete('notification', [ 'id' => $entity->id ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $data = $entity->getData();
        unset($data['id']);

        $this->mconn->update('notification', $data, [ 'id' => $entity->id ]);
    }
}
