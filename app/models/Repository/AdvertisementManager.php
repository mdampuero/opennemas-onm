<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Repository;

use Onm\Cache\CacheInterface;
use Onm\Database\DbalWrapper;

/**
 * An EntityRepository serves as a repository for entities with generic as well as
 * business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate entities.
 *
 * @package Repository
 **/
class AdvertisementManager extends EntityManager
{
     /**
     * Searches for content given a criteria
     *
     * @param  array $criteria        the criteria used to search the comments.
     * @param  array $order           the order applied in the search.
     * @param  int   $elementsPerPage the max number of elements to return.
     * @param  int   $page            the offset to start with.
     * @return array                  the matched elements.
     */
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL   = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT content_type_name, pk_content FROM `contents`, `advertisements`
            WHERE $filterSQL AND pk_content=pk_advertisement
            ORDER BY $orderBySQL $limitSQL";

        // var_dump($sql);die();

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $contentIdentifiers = array();
        foreach ($rs as $resultElement) {
            $contentIdentifiers[]= array($resultElement['content_type_name'], $resultElement['pk_content']);
        }

        $contents = $this->findMulti($contentIdentifiers);

        return $contents;
    }

    public function countBy($criteria)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_content) FROM `contents`, `advertisements`"
            ." WHERE $filterSQL AND pk_content=pk_advertisement";
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Builds the SQL WHERE filter given an array or string with the desired filter
     *
     * @param string|array $filter the filter params
     *
     * @return string the SQL WHERE filter
     **/
    protected function getFilterSQL($filter)
    {
        if (empty($filter)) {
            $filterSQL = ' 1=1 ';
        } elseif (is_array($filter)) {
            $filterSQL = array();
            foreach ($filter as $field => $value) {
                if ($field == 'fk_content_categories') {
                    $filterSQL[] = "`$field` REGEXP '(," . $value . ",)|(,"
                        . $value . "$)|(^" . $value . "[,\d]*$)'";
                } else {
                    // TODO : detect LIKE sqls
                    if ($value[0] == '%' && $value[strlen($value) -1] == '%') {
                        $filterSQL []= "`$field` LIKE '$value'";
                    } else {
                        $filterSQL []= "`$field`='$value'";
                    }
                }
            }
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }
}
