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

class FrontpageRepository extends BaseRepository
{
    /**
     * Finds the list of instances created in the current month.
     *
     * @return array Array of instances.
     */
    public function getDistinctFrontpages()
    {
        // Executing the SQL
        $sql = "SELECT id FROM `instances` "
            . 'WHERE created > DATE_SUB(NOW(), INTERVAL 1 MONTH)';

        $this->conn->selectDatabase('onm-instances');
        $rs = $this->conn->fetchAll($sql);

        return $this->getEntities($rs);
    }
}
