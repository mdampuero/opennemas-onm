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
use Onm\DatabaseConnection;

/**
 * Default BaseManager contains common functions to the rest of Entity Managers
 *
 * @package Repository
 */
abstract class BaseManager
{
    /**
     * The separator to use in cache ids.
     *
     * @var string
     */
    protected $cacheSeparator = '-';

    /**
     * Initializes the menu manager
     *
     * @param DatabaseConnection $dbConn      The database connection.
     * @param CacheInterface     $cache       The cache instance.
     * @param string             $cachePrefix The cache prefix.
     */
    public function __construct(DatabaseConnection $dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn = $dbConn;
        $this->cache = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Searches one entity given a criteria and an order.
     *
     * @param  array|string $criteria The criteria to search for an entity.
     * @param  array        $order    The order used in clause.
     * @return Object                 The object searched.
     */
    public function findOneBy($criteria, $order)
    {
        $elements = $this->findBy($criteria, $order, 1);
        $element  = null;
        if (!empty($elements)) {
            $element = $elements[0];
        }
        return $element;
    }

    /**
     * Removes the object into database.
     *
     * @param  Object  $object The object to remove.
     * @return boolean
     */
    public function remove($object)
    {
        return $object->remove();
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
        } else {
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

            // Build filters
            $filterSQL = implode($fieldUnion, $filterSQL);
        }

        return $filterSQL;
    }

    /**
     * Builds the LIMIT SQL clause.
     *
     * @param  integer $elements Number of elements.
     * @param  integer $page     The page number to show.
     * @param  integer $offset   The offset to start with.
     * @return string            The LIMIT clause.
     */
    protected function getLimitSQL($elements = 20, $page = 1, $offset = 0)
    {
        $limitSQL = '';
        if ($page == 1) {
            $limitSQL = ' LIMIT '. ($offset + $elements);
        } elseif ($page > 1) {
            $limitSQL = ' LIMIT ' . ($offset + ($page - 1) * $elements) . ', ' . $elements;
        }

        return $limitSQL;
    }

    /**
     * Builds the ORDER BY SQL clause given an array or an string.
     *
     * @param  array|string $order The filter to build.
     * @return string              The ORDER BY clause.
     */
    protected function getOrderBySQL($order)
    {
        $orderSQL  = '`id` DESC';
        if (is_string($order)) {
            $orderSQL = $order;
        } elseif (is_array($order)) {
            $tokens = array();
            foreach ($order as $key => $value) {
                if (in_array(strtoupper($value), array('DESC', 'ASC'))) {
                    $tokens[] = "$key $value";
                }
            }
            $orderSQL = implode(', ', $tokens);
        }

        return $orderSQL;
    }

    /**
     * Indicates if the EntityRepository has the cache handler enabled.
     *
     * @return boolean true if it has cache
     */
    protected function hasCache()
    {
        return $this->cache != null;
    }

    /**
     * Parses array values in filters.
     *
     * @param  array  $values   Values to parse.
     * @param  string $operator Operator applied in WHERE clause.
     * @return array            Parsed values.
     */
    protected function parseValues($values, $operator)
    {
        $parsed = array();

        foreach ($values as $value) {
            if (strtoupper($operator) == 'LIKE') {
                $parsed[] = '\'%' . $value . '%\'';
            } else {
                $parsed[] = $item;
            }
        }

        return $parsed;
    }
}
