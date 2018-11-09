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

class TagRepository extends BaseRepository
{
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
}
