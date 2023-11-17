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

class TagRepository extends BaseRepository
{
    /**
     * Returns an array of privileges grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of privileges.
     */
    public function getIdsByContentType($contentTypes)
    {
        $sql = 'SELECT DISTINCT(tag_id) FROM contents_tags' .
            ' INNER JOIN contents' .
            ' ON contents.pk_content = contents_tags.content_id' .
            ' AND contents.content_type_name IN (?)';

        $rs = $this->conn->fetchAll(
            $sql,
            [ $contentTypes ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        return array_map(function ($a) {
            return $a['tag_id'];
        }, $rs);
    }

    /**
     * Returns a list where key is the tag id and value is the number of
     * contents assigned to the tag.
     *
     * @param mixed $ids A tag id or a list of tag ids.
     *
     * @return array The list where keys are the tag ids and values are the
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

        $sql = 'SELECT tag_id AS "id", COUNT(1) AS "contents" '
            . 'FROM contents_tags '
            . 'WHERE tag_id IN (?) '
            . 'GROUP BY tag_id';

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
     * Moves all contents assigned to tags basing on a tag id
     *
     * @param integer $id     The tag id
     * @param integer $target The tag id of the target tag.
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
            . ' FROM contents_tags'
            . ' INNER JOIN contents'
            . ' ON contents_tags.content_id = contents.pk_content'
            . ' WHERE tag_id IN (?)';

        $contents = $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        if (empty($contents)) {
            return [];
        }

        $sql = 'UPDATE IGNORE contents_tags SET tag_id = ?'
            . ' WHERE tag_id IN (?)';

        $this->conn->executeQuery(
            $sql,
            [ $target, $ids ],
            [ \PDO::PARAM_INT, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        $sql = 'DELETE FROM contents_tags WHERE tag_id IN (?)';

        $this->conn->executeQuery(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_STR_ARRAY ]
        );

        return $contents;
    }

    /**
     * Returns the number of contents associated to every tag in a list of tags.
     *
     * @param array $ids The list of tag ids.
     *
     * @return array A list where the key is a tag id and the value is the
     *               number of contents associated to the tag.
     */
    public function getNumberOfContents($ids)
    {
        $sql = 'SELECT tag_id, count(1) AS total FROM `contents_tags` WHERE '
            . 'tag_id IN (?) GROUP BY tag_id';

        $rs = $this->conn->fetchAll(
            $sql,
            [ $ids ],
            [ \Doctrine\DBAL\Connection::PARAM_INT_ARRAY ]
        );

        $stats = [];
        foreach ($rs as $value) {
            $stats[$value['tag_id']] = $value['total'];
        }

        return $stats;
    }
}
