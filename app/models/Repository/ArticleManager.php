<?php

namespace Repository;

use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

class ArticleManager extends EntityManager
{
    /**
     * Searches for opinions given a criteria.
     *
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The current page.
     * @param integer $offset          The offset to start with.
     *
     * @return array The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0, $group = '')
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page, $offset);

        $group_by ='';
        if (!empty($group)) {
            $group_by = "GROUP BY {$group} ";
        }
        // Executing the SQL
        $sql = "SELECT content_type_name, pk_content FROM `contents`, `articles`
            WHERE $filterSQL AND pk_content=pk_article $group_by
            ORDER BY $orderBySQL $limitSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $contentIdentifiers = array();
        foreach ($rs as $resultElement) {
            $contentIdentifiers[]= array($resultElement['content_type_name'], $resultElement['pk_content']);
        }

        $contents = $this->findMulti($contentIdentifiers);

        return $contents;
    }

    /**
     * Counts opinions given a criteria.
     *
     * @param array   $criteria The criteria used to search.
     *
     * @return integer The number of matched elements.
     */
    public function countBy($criteria, $group = '')
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);
        $group_by ='';
        if (!empty($group)) {
            $group_by = "GROUP BY {$group} ";
        }
        // Executing the SQL
        $sql = "SELECT pk_content FROM `contents`, `article`"
            ." WHERE $filterSQL AND pk_content=pk_article $group_by";
        $rs = $this->dbConn->fetchAll($sql);

        return count($rs);

    }
}
