<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Model\Database\Repository;

use Common\Model\Entity\ContentPosition;
use Common\ORM\Database\Repository\BaseRepository;

class ContentPositionRepository extends BaseRepository
{
    /**
     * Number of max elements allowd in a frontpage, excluding advertisements
     *
     * @var int
     **/
    const MAX_NUMBER_OF_CONTENTS = 100;

    /**
     * Returns a list of content positions for a given category and frontpage id
     *
     * @param int $categoryId the category id to get contents from
     * @param int $frontpageVersionId the category id to get contents from
     *
     * @return array
     */
    public function getContentPositions($categoryId, $frontpageId)
    {
        $sql = 'select cp.pk_fk_content, cp.fk_category, cp.position,'
            . ' cp.placeholder, cp.params, cp.content_type,'
            . ' cp.frontpage_version_id'
            . ' from content_positions as cp'
            . ' inner join contents as c on c.pk_content = cp.pk_fk_content'
            . ' where ';

        $filterVal = null;
        if (empty($frontpageId)) {
            $sql      .= ' fk_category = ?';
            $filterVal = $categoryId;
        } else {
            $sql      .= ' frontpage_version_id = ?';
            $filterVal = $frontpageId;
        }

        $sql .= ' order by placeholder, position';

        $rs = $this->conn->fetchAll($sql, [$filterVal]);

        $contentPositions = [];
        $contentPosition  = null;
        foreach ($rs as $value) {
            $contentPosition = new ContentPosition($value);

            if (!array_key_exists($contentPosition->placeholder, $contentPositions)) {
                $contentPositions[$contentPosition->placeholder] = [];
            }

            $contentPositions[$contentPosition->placeholder][] = $contentPosition;
        }
        return $contentPositions;
    }

    /**
     * Returns an array of categories grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of categories.
     */
    public function getCategoriesWithManualFrontpage()
    {
        $sql = 'SELECT DISTINCT(fk_category) '
            . ' from content_positions as cp';

        $rs              = $this->conn->fetchAll($sql);
        $categories      = [];
        $contentPosition = null;
        foreach ($rs as $value) {
            $categories[] = $value['fk_category'];
        }

        return $categories;
    }
}
