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
class OpinionManager extends EntityManager
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
        $sql = "SELECT content_type_name, pk_content FROM `contents`, `opinions`
            WHERE $filterSQL AND pk_content=pk_opinion
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
        $sql = "SELECT COUNT(pk_content) FROM `contents`, `opinions`"
            ." WHERE $filterSQL AND pk_content=pk_opinion";
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
     */
    protected function getFilterSQL($filters)
    {
        if (empty($filters)) {
            $filterSQL = ' 1=1 ';
        } elseif (is_array($filters)) {
            $filterSQL = array();

            foreach ($filters as $field => $values) {
                $fieldFilters = array();

                if ($field == 'blog') {
                    $allAuthors = \User::getAllUsersAuthors();

                    $authorsBlog = array();
                    foreach ($allAuthors as $authorData) {
                        if ($authorData->is_blog == 1) {
                            $authorsBlog[$authorData->id] = $authorData;
                        }
                    }

                    if (!empty($authorsBlog)) {
                        if ($values[0]['value']) {
                            $filterSQL [] = ' opinions.fk_author IN ('
                                . implode(', ', array_keys($authorsBlog)).") ";
                        } else {
                            $filterSQL [] = ' opinions.fk_author NOT IN ('
                                . implode(', ', array_keys($authorsBlog)).") ";
                        }
                    } else {
                        if ($values[0]['value']) {
                            $filterSQL [] = ' opinions.fk_author=-1';
                        }
                    }
                } elseif ($field == 'author') {
                    if ($values[0]['value'] > 0) {
                        $filterSQL []= 'opinions.fk_author=' . $values[0]['value'];
                    } elseif ($values[0]['value'] == -2) {
                        $filterSQL []= 'opinions.type_opinion=2';
                    } elseif ($values[0]['value'] == -3) {
                        $filterSQL []= 'opinions.type_opinion=1';
                    }
                } else {
                    foreach ($values as $filter) {
                        $operator = "=";
                        $value    = "";
                        if ($filter['value'][0] == '%'
                            && $filter['value'][strlen($filter['value']) - 1] == '%'
                        ) {
                            $operator = "LIKE";
                        }

                        // Check operator
                        if (array_key_exists('operator', $filter)) {
                            $operator = $filter['operator'];
                        }

                        // Check value
                        if (array_key_exists('value', $filter)) {
                            $value = $filter['value'];
                        }

                        $fieldFilters[] = "`$field` $operator '$value'";
                    }

                    // Add filters for the current $field
                    $filterSQL[] = implode(' OR ', $fieldFilters);
                }
            }

            // Build filters
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }
}
