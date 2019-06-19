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

class ContentRepository extends BaseRepository
{
    /**
     * Removes all contents assigned categories basing on a category id or a
     * list of category ids.
     *
     * @param mixed $ids The category id or the list of category ids.
     *
     * @return array The list of ids and content types of the removed contents.
     */
    public function removeContentsInTrash()
    {
        $sql = 'DELETE FROM contents WHERE in_litter = 1';

        $this->conn->executeQuery($sql);
    }
}
