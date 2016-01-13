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

class ExtensionPersister extends DatabasePersister
{
    /**
     * {@inheritdoc}
     */
    public function create(Entity &$entity)
    {
        $data = $this->dbfy($entity);

        $metas = $data['metas'];
        unset($data['metas']);

        $this->mconn->insert('extension', $data);

        $entity->id = $this->mconn->lastInsertId();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Entity $entity)
    {
        $this->mconn->delete('extension', [ 'id' => $entity->id ]);
        $this->mcache->delete($entity->getCachedId());
    }

    /**
     * {@inheritdoc}
     */
    public function update(Entity $entity)
    {
        $data  = $this->dbfy($entity);
        $metas = $data['metas'];

        unset($data['id']);
        unset($data['metas']);

        $this->mconn->update('extension', $data, [ 'id' => $entity->id ]);

        foreach ($metas as $key => $value) {
            $sql = "REPLACE INTO `extension_meta` SET extension_id = ?,"
                ." meta_key = ?, meta_value = ?";
            $params = [ $entity->id, $key, $value ];

            $this->mconn->executeUpdate($sql, $params);
        }

        $this->mcache->delete($entity->getCachedId());
    }
}
