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
            $sql .= " WHERE pk_fk_content IN (" . implode(',', $id) . ")";
        } else {
            $sql .= " WHERE pk_fk_content = $id";
        }

        $rs = $this->dbConn->fetchAll($sql);

        if (!$rs) {
            return 0;
        }

        if (is_array($id)) {
            $views = array();

            foreach ($rs as $value) {
                $views[$value['pk_fk_content']] = $value['views'];
            }
            return $views;
        } else {
            return $rs[0]['views'];
        }
    }

    /**
     * Saves the amount of views for a content in database.
     *
     * @param integer $id    The content id.
     * @param integer $views The amount of views.
     */
    public function setViews($id, $views = null)
    {
        if (is_null($views)) {
            $views = $this->getViews($id) + 1;
        }

        $sql = 'REPLACE INTO `content_views` (`pk_fk_content`, `views`) VALUES (?, ?)';

        $this->dbConn->transactional(function ($em) use ($sql, $id, $views) {
            $em->executeQuery($sql, array($id, $views));
        });
    }
}
