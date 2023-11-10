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
}
