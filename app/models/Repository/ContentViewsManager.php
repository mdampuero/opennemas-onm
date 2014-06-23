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
 *
 */
class ContentViewsManager extends EntityManager
{
    /**
     * Gets the amount of views for a given content id.
     *
     * @param  integer $id The content id.
     * @return integer     The amount of views.
     */
    public function getViews($id)
    {
        $sql = "SELECT views FROM `content_views`"
            ." WHERE pk_fk_content = $id";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0]['views'];
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

        $sql = "UPDATE `content_views` SET `views`=$views "
            ."WHERE  `pk_fk_content`=$id";

        $this->dbConn->transactional(function ($em) use ($sql) {
            $em->executeQuery($sql);
        });
    }
}
