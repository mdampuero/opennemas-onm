<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\ORM\Database\Persister;

use Framework\ORM\Entity\Entity;

class ClientPersister extends DatabasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $data = $this->dbfy($entity);

        $this->mconn->insert('client', $data);

        $entity->id = $this->mconn->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->mconn->delete('client', [ 'id' => $entity->id ]);
        $this->mcache->delete($entity->getCachedId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $data = $this->dbfy($entity);
        unset($data['id']);

        $this->mconn->update('client', $data, [ 'id' => $entity->id ]);
        $this->mcache->delete($entity->getCachedId());
    }
}
