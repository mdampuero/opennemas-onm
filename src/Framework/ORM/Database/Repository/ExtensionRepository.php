<?php

namespace Framework\ORM\Database\Repository;

use Framework\ORM\Entity\Entity;
use Framework\ORM\Exception\EntityNotFoundException;

class ExtensionRepository extends DatabaseRepository
{
    /**
     * Refresh an entity with fresh data from database.
     *
     * @param Entity $entity The entity to refresh.
     */
    public function refresh(Entity &$entity)
    {
        parent::refresh($entity);

        $sql = 'SELECT meta_key, meta_value FROM extension_meta WHERE extension_id = '
            . $entity->id;

        $rs = $this->conn->fetchAll($sql);

        if ($rs) {
            foreach ($rs as $meta) {
                $entity->metas[$meta['meta_key']] = $meta['meta_value'];

                $value = @unserialize($meta['meta_value']);

                if ($value) {
                    $entity->metas[$meta['meta_key']] = $value;
                }
            }

            $entity->refresh();
        }

    }
}
