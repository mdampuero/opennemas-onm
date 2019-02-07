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

        $sql = 'SELECT pk_fk_content_category AS "id", COUNT(1) AS "contents" '
            . 'FROM contents_categories '
            . 'WHERE pk_fk_content_category IN (?) '
            . 'GROUP BY pk_fk_content_category';

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
            . 'LEFT JOIN contents_categories '
            . 'ON pk_content = pk_fk_content '
            . 'WHERE pk_fk_content_category IN (?)';

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

        $params = [];
        $types  = [];

        $data = $this->findContents($ids);

        if (empty($data)) {
            return [];
        }

        $sql = 'REPLACE INTO contents_categories (pk_fk_content_category, pk_fk_content)'
            . ' VALUES ' . rtrim(str_repeat('(?,?),', count($data)), ',');

        foreach ($data as $value) {
            $params = array_merge($params, [ $target, $value['id'] ]);
            $types  = array_merge($types, [ \PDO::PARAM_INT, \PDO::PARAM_INT ]);
        }

        $this->conn->executeQuery($sql, $params, $types);

        $sql = 'DELETE FROM contents_categories WHERE pk_fk_content_category IN (?)';

        $this->conn->executeQuery(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        return $data;
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

        $data = $this->findContents($ids);

        if (empty($data)) {
            return [];
        }

        $toDelete = array_map(function ($a) {
            return $a['id'];
        }, $data);

        $sql = 'DELETE FROM contents WHERE pk_content IN (?)';

        $this->conn->executeQuery(
            $sql,
            [ $toDelete ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        return $data;
    }
}
