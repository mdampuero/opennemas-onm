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
            . ' LEFT JOIN usermeta ON usermeta.user_id = id'
            . ' WHERE type != 0'
            . ' GROUP BY id';

        $subscribers = $this->conn->fetchAll($sql);
        $usermetas   = $this->conn->fetchAll('SELECT * FROM usermeta');

        // Prepare user_groups and register_date user data
        foreach ($subscribers as &$subscriber) {
            $subscriber['user_groups'] = !empty($subscriber['user_groups'])
                ? explode(',', $subscriber['user_groups'])
                : [];

            foreach ($usermetas as $usermeta) {
                if ($subscriber['id'] === $usermeta['user_id']) {
                    $subscriber[$usermeta['meta_key']] = $usermeta['meta_value'];
                }
            }
        }

        return $subscribers;
    }
}
