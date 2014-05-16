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
class OpinionManager extends EntityManager
{
    /**
     * Searches for content given a criteria.
     *
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The offset to start with.
     * @return array                    The matched elements.
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
     * Searches for content given a criteria.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return integer                The number of matched elements.
     */
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
     * Builds the SQL WHERE filter given an array or string with the desired
     * filter.
     *
     * @param  string|array $criteria The filter params.
     * @return string                 The SQL WHERE filter.
     */
    protected function getFilterSQL($criteria)
    {
        if (empty($criteria)) {
            $filterSQL = ' 1=1 ';
        } elseif (!is_array($criteria)) {
            $filterSQL = $criteria;
        } elseif (is_array($criteria)) {
            $filterSQL = array();

            $fieldUnion = ' AND ';
            if (array_key_exists('union', $criteria)) {
                $fieldUnion = ' ' . trim($criteria['union']) . ' ';
                unset($criteria['union']);
            }

            foreach ($criteria as $field => $filters) {
                $valueUnion = ' AND ';
                if (array_key_exists('union', $filters)) {
                    $valueUnion = ' ' . trim($filters['union']) . ' ';
                    unset($filters['union']);
                }

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
                        if ($filters[0]['value']) {
                            $filterSQL[] = 'opinions.fk_author IN ('
                                . implode(', ', array_keys($authorsBlog)).") ";
                        } else {
                            $filterSQL[] = 'opinions.fk_author NOT IN ('
                                . implode(', ', array_keys($authorsBlog)).") ";
                        }
                    } else {
                        if ($filters[0]['value']) {
                            $filterSQL[] = 'opinions.fk_author=-1';
                        }
                    }
                } elseif ($field == 'author') {
                    if ($filters[0]['value'] > 0) {
                        $filterSQL[] = 'opinions.fk_author=' . $filters[0]['value'];
                    } elseif ($filters[0]['value'] == -2) {
                        $filterSQL[] = 'opinions.type_opinion=2';
                    } elseif ($filters[0]['value'] == -3) {
                        $filterSQL[] = 'opinions.type_opinion=1';
                    }
                } else {
                    $valueUnion = ' AND ';
                    if (array_key_exists('union', $filters)) {
                        $valueUnion = ' ' . trim($filters['union']) . ' ';
                        unset($filters['union']);
                    }

                    $fieldFilters = array();
                    foreach ($filters as $filter) {
                        $operator = "=";
                        if (array_key_exists('operator', $filter)) {
                            $operator = trim($filter['operator']);
                        }

                        $value = '';
                        if (array_key_exists('value', $filter)) {
                            $value = $filter['value'];
                        }

                        if (is_array($value) && !empty($value)) {
                            if (strtoupper($operator) == 'IN'
                                || strtoupper($operator) == 'NOT IN'
                            ) {
                                $fieldFilters[] = "`$field` $operator (" .
                                    implode(', ', $value) . ")";
                            } else {
                                $value = $this->parseValues($value, $operator);
                                $fieldFilters[] = "`$field` $operator " .
                                    implode(' ', $value);
                            }
                        } else {
                            $fieldFilters[] = "`$field` $operator '$value'";
                        }
                    }

                    // Add filters for the current $field
                    $filterSQL[] = '(' . implode($valueUnion, $fieldFilters) . ')';
                }
            }

            // Build filters
            $filterSQL = implode($fieldUnion, $filterSQL);
        }

        return $filterSQL;
    }
}
