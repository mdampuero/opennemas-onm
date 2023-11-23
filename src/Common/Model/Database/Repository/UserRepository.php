<?php

namespace Common\Model\Database\Repository;

use Opennemas\Orm\Database\Repository\BaseRepository;

class UserRepository extends BaseRepository
{
    /**
     * Returns a list of subscribers users.
     *
     * @return array The list of activated users.
     */
    public function findSubscribers()
    {
        $sql = 'SELECT id, email, name, activated,'
            . ' GROUP_CONCAT(DISTINCT user_group_id) as user_groups FROM users '
            . ' LEFT JOIN user_user_group ON user_user_group.user_id = id'
            . ' WHERE type != 0'
            . ' GROUP BY id';

        $subscribers = $this->conn->fetchAll($sql);
        $metas       = $this->conn->fetchAll('SELECT * FROM usermeta');

        // Parse users for groups and metas
        $users = [];
        foreach ($subscribers as &$subscriber) {
            $subscriber['user_groups'] = !empty($subscriber['user_groups'])
                ? explode(',', $subscriber['user_groups'])
                : [];

            $users[$subscriber['id']] = $subscriber;
        }

        foreach ($metas as $meta) {
            if (!array_key_exists($meta['user_id'], $users)) {
                // If this happens there is a database inconsistency
                continue;
            }

            $users[$meta['user_id']][$meta['meta_key']] = $meta['meta_value'];
        }

        return $users;
    }

       /**
     * Returns a list where key is the user id and value is the number of
     * contents assigned to the user.
     *
     * @param mixed $ids A user id or a list of user ids.
     *
     * @return array The list where keys are the user ids and values are the
     *               number of contents.
     */
    public function countContents($ids)
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException();
        }
        if (!is_array($ids)) {
            $ids = [ $ids ];
        }
        $sql = 'SELECT fk_author AS "id", COUNT(1) AS "contents" '
            . 'FROM contents '
            . 'WHERE fk_author IN (?) '
            . 'GROUP BY fk_author';

        $data = $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $contents = [];
        foreach ($data as $value) {
            $contents[$value['id']] = $value['contents'];
        }

        return $contents;
    }

    /**
     * Moves all contents assigned to users basing on a user id
     *
     * @param integer $id     The user id
     * @param integer $target The user id of the target user.
     *
     * @return array The list of ids and content types of the moved contents.
     */
    public function moveContents($ids, $target)
    {
        if (empty($ids) || empty($target)) {
            throw new \InvalidArgumentException();
        }
        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        $sql = 'SELECT pk_content AS "id", content_type_name AS "type"'
            . ' FROM contents'
            . ' WHERE fk_author IN (?)';

        $contents = $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
        if (empty($contents)) {
            return [];
        }
        $sql = 'UPDATE IGNORE contents SET fk_author = ?'
            . ' WHERE fk_author IN (?)';
        $this->conn->executeQuery(
            $sql,
            [ $target, $ids ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
        $sql = 'DELETE FROM contents WHERE fk_author IN (?)';
        $this->conn->executeQuery(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        return $contents;
    }

    /**
     * Returns a list of all users.
     *
     * @return array The list of users.
     */
    public function findUsers()
    {
        $sql = 'SELECT *
        FROM users
        LEFT JOIN user_user_group ON users.id = user_user_group.user_id;';
        try {
            $users = $this->conn->fetchAll($sql);

            return $users;
        } catch (\Exception $e) {
            throw new \RuntimeException("Error al obtener etiquetas: " . $e->getMessage());
        }
    }
}
