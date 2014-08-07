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
use Onm\Database\DbalWrapper;

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
class AdvertisementManager extends EntityManager
{
    /**
     * Counts advertisements given a criteria.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return integer                The number of contents.
     */
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
     * Searches for advertisements given a criteria.
     *
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The current page.
     * @param  integer      $offset          The offset to start with.
     * @return array                         The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);

        $orderBySQL  = '`pk_content` DESC';
        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT content_type_name, pk_content FROM `contents`, `advertisements`
            WHERE $filterSQL AND pk_content=pk_advertisement
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
     * Builds the SQL WHERE filter given an array or string with the desired
     * filter.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return string                 The SQL WHERE filter.
     */
    protected function getFilterSQL($criteria)
    {
        if (empty($criteria)) {
            $filterSQL = ' 1=1 ';
        } elseif (!is_array($criteria)) {
            $filterSQL = $filters;
        } else {
            $filterSQL = array();

            $fieldUnion = ' AND ';
            if (array_key_exists('union', $criteria)) {
                $fieldUnion = $criteria['union'];
                unset($criteria['union']);
            }

            foreach ($criteria as $field => $filters) {
                $valueUnion = ' AND ';
                if (array_key_exists('union', $filters)) {
                    $valueUnion = $filters['union'];
                    unset($filters['union']);
                }

                $fieldFilters = array();
                foreach ($filters as $filter) {
                    $operator = "=";
                    if (array_key_exists('operator', $filter)) {
                        $operator = ' ' . trim($filter['operator']) . ' ';
                    }

                    $value = '';
                    if (array_key_exists('value', $filter)) {
                        $value = $filter['value'];
                    }

                    if (is_array($value) && !empty($value)) {
                        if (strtoupper($operator) == ' IN '
                            || strtoupper($operator) == ' NOT IN '
                        ) {
                            $fieldFilters[] = "`$field` $operator (" .
                                implode(', ', $filter['value']) . ")";
                        } else {
                            $fieldFilters[] = "`$field` $operator " .
                                implode(' ', $filter['value']);
                        }
                    } else {
                        if ($field == 'fk_content_categories') {
                            $fieldFilters[] = "`$field` REGEXP '(," . $value
                                .",)|(,".$value."$)|(^".$value."$)|(^".$value.",[\d]*)'";
                        } else {
                            $fieldFilters[] = "`$field` $operator '$value'";
                        }
                    }
                }

                // Add filters for the current $field
                $filterSQL[] = '(' . implode($valueUnion, $fieldFilters) . ')';
            }

            // Build filters
            $filterSQL = implode($fieldUnion, $filterSQL);
        }

        return $filterSQL;
    }
}
