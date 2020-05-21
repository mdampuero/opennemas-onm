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

use Opennemas\Orm\Database\Repository\BaseRepository;

class CategoryRepository extends BaseRepository
{
    /**
     * Returns a list where key is the category id and value is the number of
     * contents assigned to the category.
     *
     * @param mixed $ids A category id or a list of category ids.
     *
     * @return array The list where keys are the category ids and values are the
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

        $sql = 'SELECT category_id AS "id", COUNT(1) AS "contents" '
            . 'FROM content_category '
            . 'WHERE category_id IN (?) '
            . 'GROUP BY category_id';

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
     * Returns a list of contents assigned to categories basing on a category
     * id or a list of category ids.
     *
     * @param mixed $ids The category id or the list of category ids.
     *
     * @return array The list of ids and content types of the contents assigned
     *               to the categories.
     */
    public function findContents($ids)
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException();
        }

        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        $sql = 'SELECT pk_content AS "id", content_type_name AS "type" '
            . 'FROM contents '
            . 'LEFT JOIN content_category '
            . 'ON pk_content = content_id '
            . 'WHERE category_id IN (?)';

        return $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
    }

    /**
     * Moves all contents assigned to categories basing on a category id or a
     * list of category ids to another category.
     *
     * @param mixed   $ids    The category id or the list of category ids.
     * @param integer $target The category id of the target category.
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

        $sql = 'SELECT content_id AS "id", content_type_name AS "type"'
            . ' FROM content_category'
            . ' INNER JOIN contents'
            . ' ON content_category.content_id = contents.pk_content'
            . ' WHERE category_id IN (?)';

        $contents = $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        if (empty($contents)) {
            return [];
        }

        $sql = 'UPDATE IGNORE content_category SET category_id = ?'
            . ' WHERE category_id IN (?)';

        $this->conn->executeQuery(
            $sql,
            [ $target, $ids ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $sql = 'DELETE FROM content_category WHERE category_id IN (?)';

        $this->conn->executeQuery(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        return $contents;
    }

    /**
     * Removes all contents assigned categories basing on a category id or a
     * list of category ids.
     *
     * @param mixed $ids The category id or the list of category ids.
     *
     * @return array The list of ids and content types of the removed contents.
     */
    public function removeContents($ids)
    {
        if (empty($ids)) {
            throw new \InvalidArgumentException();
        }

        if (!is_array($ids)) {
            $ids = [ $ids ];
        }

        $sql = 'DELETE FROM contents WHERE pk_content IN ('
            . 'SELECT content_id FROM content_category '
            . 'WHERE category_id IN (?))';

        $this->conn->executeQuery(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );
    }
}
