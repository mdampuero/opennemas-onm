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
     * The lost value to save in cache when setting is not in database.
     *
     * @var string
     */
    protected $lostValue = '-lost-';

    /**
     * Initializes the menu manager
     *
     * @param Onm\Database\DbalWrapper $dbConn      The database connection.
     * @param CacheInterface           $cache       The cache instance.
     * @param string                   $cachePrefix The cache prefix.
     */
    public function __construct($dbConn, $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Redirects all the calls to the DbalConnection instance
     *
     * @param  string $method the method to call
     * @param  array  $params the list of parameters to pass to the method
     * @return mixed          the result of the method call
     */
    public function __call($method, $params)
    {
        $rs = call_user_func_array(array($this->dbConn, $method), $params);

        return $rs;
    }

    /**
     * Searches one entity given a criteria and an order.
     *
     * @param  array   $criteria        The criteria used to search.
     * @param  array   $order           The order applied in the search.
     * @param  integer $elementsPerPage The max number of elements.
     * @param  integer $page            The current page.
     * @param  integer $offset          The offset to start with.
     * @return Object                   The object searched.
     */
    public function findOneBy($criteria, $order = null, $elementsPerPage = 1, $page = 1, $offset = 0)
    {
        $elements = $this->findBy($criteria, $order, $elementsPerPage, $page, $offset);
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
                    $sql = $this->parseFilter($field, $filter);

                    if (!empty($sql)) {
                        $fieldFilters[] = $sql;
                    }
                }

                if (!empty($fieldFilters)) {
                    // Add filters for the current $field
                    $filterSQL[] = '(' . implode($valueUnion, $fieldFilters) . ')';
                }
            }

            // Build filters
            $filterSQL = implode($fieldUnion, $filterSQL);
        }

        return $filterSQL;
    }

    /**
     * Returns the join conditions for the given criteria.
     *
     * @param  array  $criteria The criteria.
     * @return string           The join conditions.
     */
    protected function getJoinSQL($criteria)
    {
        $sql = '';
        foreach ($criteria as $join) {
            $type = 'LEFT';

            $table = $join['table'];
            unset($join['table']);

            // Add left/right join clause
            if (array_key_exists('type', $join)) {
                $type = strtoupper($join['type']);
                unset($join['type']);
            }

            $sql = "$type JOIN $table ON " . $this->getFilterSQL($join);
        }

        return $sql;
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
            $limitSQL = ' LIMIT ' . ($offset + ($page - 1) * $elements)
                . ', ' . $elements;
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
     * Returns the conditions given a filter.
     *
     * Example: array('value' => 'x', 'operator' => '<', field => true).
     *
     *     value:               The value to compare to.
     *     operator (Optional): Operator used in condition.
     *     field (Optional):    Whether value is a database field or a literal
     *                          value.
     *
     * @param  string $field  The field name.
     * @param  array  $filter The filter to apply.
     * @return string         The SQL string.
     */
    protected function parseFilter($field, $filter)
    {
        $isField  = false;
        $operator = "=";
        $sql      = null;
        $value    = '';

        if (array_key_exists('operator', $filter)) {
            $operator = trim($filter['operator']);
        }

        if (array_key_exists('value', $filter)) {
            $value = $filter['value'];
        }

        if (array_key_exists('field', $filter)) {
            $isField = $filter['field'];
        }

        if (is_array($value) && !empty($value)) {
            if (strtoupper($operator) == 'IN'
                || strtoupper($operator) == 'NOT IN'
            ) {
                // Value (not) in array
                $sql = "$field $operator ('" . implode('\', \'', $value) . "')";
            } else {
                // Array of values
                $value = $this->parseValues($value, $operator);
                $sql   = "$field $operator " . implode(' ', $value);
            }
        } elseif (!is_array($value) && !is_null($value)) {
            if ($isField) {
                // Database column
                $sql = "$field $operator $value";
            } else {
                // Literal value
                $sql = "$field $operator '$value'";
            }
        } elseif (is_null($value)) {
            // NULL value
            $sql = "$field $operator NULL";
        }

        return $sql;
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
                $parsed[] = $value;
            }
        }

        return $parsed;
    }
}
