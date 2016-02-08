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

use Framework\ORM\Core\Entity;

class ExtensionPersister extends DatabasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        list($data, $metas) = $this->databasify($entity);

        $this->conn->insert('extension', $data);

        $entity->id = $this->mconn->lastInsertId();

        if ($this->metadata[$entity->getClassName()]->mapping['metas']) {
            $this->persistMetas($metas);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->conn->delete('extension', [ 'id' => $entity->id ]);
        $this->cache->delete($entity->getCachedId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        list($data, $metas) = $this->databasify($entity);
        unset($data['id']);

        $this->conn->update('extension', $data, [ 'id' => $entity->id ]);
        $this->cache->delete($entity->getCachedId());

        if ($this->metadata[$entity->getClassName()]->mapping['metas']) {
            $this->persistMetas($metas);
        }
    }
}
