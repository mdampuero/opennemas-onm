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
            . ' GROUP_CONCAT(user_group_id) as user_groups,'
            . ' meta_value as register_date FROM users'
            . ' LEFT JOIN user_user_group ON user_user_group.user_id = id'
            . ' LEFT JOIN usermeta ON usermeta.user_id = id AND meta_key = "register_date"'
            . ' WHERE type != 0'
            . ' GROUP BY id';

        $subscribers = $this->conn->fetchAll($sql);

        // Prepare user_groups and register_date user data
        foreach ($subscribers as &$subscriber) {
            $subscriber['user_groups'] = !empty($subscriber['user_groups'])
                ? explode(',', $subscriber['user_groups'])
                : [];

            $subscriber['register_date'] = !empty($subscriber['register_date'])
                ? date('Y-m-d', strtotime($subscriber['register_date']))
                : '';
        }

        return $subscribers;
    }
}
