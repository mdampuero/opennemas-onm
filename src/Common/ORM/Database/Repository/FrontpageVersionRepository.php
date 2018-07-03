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

class FrontpageVersionRepository extends BaseRepository
{


    /**
     * Returns an array of categories grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of categories.
     */
    public function getCatFrontpageRel()
    {
        $sql = 'SELECT DISTINCT category_id, frontpage_id'
            . ' from frontpage_versions';

        $rs = $this->conn->fetchAll($sql);

        $catFrontpageRel = [];

        foreach ($rs as $value) {
            $catFrontpageRel[$value['category_id']] =
                (int) $value['frontpage_id'];
        }

        return $catFrontpageRel;
    }


     /**
     * Returns an array of categories grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of categories.
     */
    public function getCurrentVerForCat($categoryId)
    {
        $sql = 'SELECT id FROM frontpage_versions'
            . ' WHERE category_id = ? AND publish_date <= CURDATE()'
            . ' ORDER BY publish_date desc LIMIT 1';

        $rs = $this->conn->fetchAll($sql, [$categoryId]);

        $frontpageVersionId = null;

        if (empty($rs)) {
            return null;
        }

        return $rs[0]['id'];
    }

    /**
     * Returns an array of categories grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of categories.
     */
    public function getNextVerForCat($categoryId)
    {
        $sql = 'SELECT id FROM frontpage_versions'
            . ' WHERE category_id = ? AND publish_date > CURDATE()'
            . ' ORDER BY publish_date asc LIMIT 1';

        $rs = $this->conn->fetchAll($sql, [$categoryId]);

        $frontpageVersionId = null;

        if (empty($rs)) {
            return null;
        }

        return $rs[0]['id'];
    }
}
