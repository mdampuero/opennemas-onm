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
        $data  = $this->dbfy($entity);
        $metas = $data['metas'];

        unset($data['metas']);

        $types = [];
        foreach ($data as $value) {
            if (is_null($value)) {
                $types[] = \PDO::PARAM_NULL;
            } elseif (is_integer($value)) {
                $types[] = \PDO::PARAM_INT;
            } else {
                $types[] = \PDO::PARAM_STR;
            }
        }

        $this->mconn->insert('extension', $data, $types);

        $entity->id = $this->mconn->lastInsertId();

        foreach ($metas as $key => $value) {
            $sql = "REPLACE INTO `extension_meta` SET extension_id = ?,"
                ." meta_key = ?, meta_value = ?";
            $params = [ $entity->id, $key, $value ];

            $this->mconn->executeUpdate($sql, $params);
        }
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

        $types = [];
        foreach ($data as $value) {
            if (is_null($value)) {
                $types[] = \PDO::PARAM_NULL;
            } elseif (is_integer($value)) {
                $types[] = \PDO::PARAM_INT;
            } else {
                $types[] = \PDO::PARAM_STR;
            }
        }

        $this->mconn->update('extension', $data, [ 'id' => $entity->id ], $types);

        foreach ($metas as $key => $value) {
            if (!empty($value)) {
                $sql = "REPLACE INTO `extension_meta` SET extension_id = ?,"
                    ." meta_key = ?, meta_value = ?";
                $params = [ $entity->id, $key, $value ];

                $this->mconn->executeUpdate($sql, $params);
            }
        }

        if (!empty(array_keys($metas))) {
            $sql = "DELETE FROM `extension_meta` WHERE extension_id = ?"
                . " AND meta_key NOT IN (?)";

            $this->mconn->executeUpdate(
                $sql,
                [ $entity->id, array_keys($metas) ],
                [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
            );
        }

        $this->mcache->delete($entity->getCachedId());
    }
}
