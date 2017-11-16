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

use Onm\Cache\CacheInterface;

/**
 * An EntityRepository serves as a repository for entities with generic as well
 * as business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate
 * entities.
 *
 * @package Repository
 */
class FrontpageManager extends EntityManager
{
    /**
     * Fetches all the contents (articles, widgets, etc) for one specific
     * category with its placeholder and position.
     *
     * This is used for HomePages, fetches all the contents assigned for it
     * and allows to render an entire homepage.
     *
     * @param int $categoryId The category id.
     *
     * @return array Array of contents.
     */
    public function getContentIdsForHomepageOfCategory($categoryId = null)
    {
        // Initialization of variables
        $contents = [];

        $whereSQL = "";
        if (!is_null($categoryId)) {
            $whereSQL = " WHERE `fk_category` = " . $categoryId;
        }

        $sql = "SELECT `pk_fk_content` FROM content_positions"
              . "$whereSQL"
              . " ORDER BY `position` ASC";

        $rs = $this->dbConn->fetchAll($sql);

        foreach ($rs as $resultElement) {
            $contents[] = $resultElement['pk_fk_content'];
        }

        return $contents;
    }

    /**
     * Searches for content given a criteria
     *
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0, &$count = null)
    {
        // Building the SQL filter
        $filterSQL = $this->getFilterSQL($criteria);

        $orderBySQL = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT " . (($count) ? 'SQL_CALC_FOUND_ROWS  ' : '') . " content_type_name, pk_content"
            . " FROM `contents`, `content_positions`"
            . " WHERE `pk_fk_content` = `pk_content` AND $filterSQL"
            . " ORDER BY $orderBySQL $limitSQL";

        $rs = $this->dbConn->fetchAll($sql);

        if ($count) {
            $count = $this->getSqlCount();
        }

        $contentIdentifiers = [];
        foreach ($rs as $resultElement) {
            $contentIdentifiers[] = [
                $resultElement['content_type_name'],
                $resultElement['pk_content']
            ];
        }

        $contents = $this->findMulti($contentIdentifiers);

        return $contents;
    }

    /**
     * Counts contents given a criteria.
     *
     * @param array $criteria The criteria used to search the contents.
     *
     * @return integer The number of found contents.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $filterSQL = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_content) FROM `contents`, `content_positions`"
            . " WHERE `pk_fk_content` = `pk_content` AND $filterSQL";
        $rs  = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }
}
