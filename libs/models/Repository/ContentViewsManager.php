<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Repository;

/**
 * This repository handles the content views operations for contents.
 */
class ContentViewsManager extends EntityManager
{
    /**
     * Gets the amount of views for a given content id.
     *
     * @param integer $id The content id(s).
     *
     * @return mixed The amount of views.
     */
    public function getViews($id)
    {
        $sql = "SELECT * FROM `content_views`";
        if (is_array($id)) {
            if (count($id) <= 0) {
                return [];
            }

            $sql .= " WHERE pk_fk_content IN (" . implode(',', $id) . ")";
        } else {
            $sql .= " WHERE pk_fk_content = " . intval($id);
        }

        $rs = $this->dbConn->fetchAll($sql);

        if (!$rs) {
            return (is_array($id) ? [] : 0);
        }

        if (is_array($id)) {
            $views = [];

            foreach ($rs as $value) {
                $views[$value['pk_fk_content']] = $value['views'];
            }

            return $views;
        } else {
            return $rs[0]['views'];
        }
    }

    /**
     * Saves the amount of views for a content in database
     *
     * @param integer $id    The content id.
     * @param integer $views The amount of views.
     */
    public function setViews($id, $views = null)
    {
        $sql = 'INSERT INTO `content_views` (`pk_fk_content`, `views`) VALUES (?, ?)';
        if (is_null($views)) {
            $sql   .= ' ON DUPLICATE KEY UPDATE views = views + 1';
            $params = [$id, 1];
        } else {
            $sql   .= ' ON DUPLICATE KEY UPDATE views = ?';
            $params = [$id, $views, $views];
        }

        $this->dbConn->executeUpdate($sql, $params);
    }
}
