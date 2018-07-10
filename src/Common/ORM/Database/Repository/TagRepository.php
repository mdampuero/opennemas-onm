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
     * Returns an array of privileges grouped by entity id.
     *
     * @param array $ids The entity ids.
     *
     * @return array The array of privileges.
     */
    public function getTagsAssociatedCertainContentsTypes($contentTypesIds)
    {
        $filters = [];

        if (!is_array($contentTypesIds) || count($contentTypesIds) < 1) {
            return [];
        }

        $contentTypes = rtrim(str_repeat('?,', count($contentTypesIds)), ',');

        $sql = 'SELECT id, name, language_id, slug FROM `tags` WHERE EXISTS(' .
            ' SELECT 1 FROM contents_tags' .
            ' INNER JOIN contents ON' .
            ' contents.pk_content = contents_tags.content_id AND' .
            ' contents.fk_content_type IN (' . $contentTypes . ')' .
            ' WHERE tags.id = contents_tags.tag_id)';

        $rs = $this->conn->fetchAll($sql, $contentTypesIds);

        return $this->getEntities($rs);
    }
}
