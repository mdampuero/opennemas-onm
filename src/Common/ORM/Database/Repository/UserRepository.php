<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Database\Repository;

class UserRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    protected function refresh($ids)
    {
        $entities   = parent::refresh($ids);
        $categories = $this->getCategories($ids);

        foreach ($entities as $key => &$value) {
            $value->categories = [];

            if (array_key_exists($key, $categories)) {
                $value->categories = $categories[$key];
            }

            $value->refresh();
        }

        return $entities;
    }

    /**
     * Returns an array of categories grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of categories.
     */
    protected function getCategories($ids)
    {
        $filters = [];

        foreach ($ids as $id) {
            $filters[] = 'pk_fk_user=' . $id['id'];
        }

        $sql = 'select * from users_content_categories where '
            . implode(' or ', $filters);

        $rs = $this->conn->fetchAll($sql);

        $categories = [];
        foreach ($rs as $value) {
            $categories[$value['pk_fk_user']][] =
                (int) $value['pk_fk_content_category'];
        }

        return $categories;
    }
}
