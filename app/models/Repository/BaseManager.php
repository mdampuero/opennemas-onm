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
use Onm\DatabaseConnection;

/**
 * Default BaseManager contains common functions to the rest of Entity Managers
 *
 * @package Repository
 **/
abstract class BaseManager
{
    /**
     * Initializes the menu manager
     *
     * @param CacheInterface $cache the cache instance
     **/
    public function __construct(DatabaseConnection $dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn = $dbConn;
        $this->cache = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Builds the SQL WHERE filter given an array or string with the desired filter
     *
     * @param string|array $filter the filter params
     *
     * @return string the SQL WHERE filter
     **/
    protected function getFilterSQL($filters)
    {
        if (empty($filters)) {
            $filterSQL = ' 1=1 ';
        } elseif (is_array($filters)) {
            $filterSQL = array();

            foreach ($filters as $field => $values) {
                $fieldFilters = array();

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

            // Build filters
            $filterSQL = implode(' AND ', $filterSQL);
        } else {
            $filterSQL = $filter;
        }

        return $filterSQL;
    }

    /**
     * Builds the ORDER BY SQL clause given an array or an string
     *
     * @param array|string $order the filter to build
     *
     * @return string the ORDER BY clause
     **/
    public function getOrderBySQL($order)
    {
        $orderSQL  = '`id` DESC';
        if (is_string($order)) {
            $orderSQL = $order;
        } elseif (is_array($order)) {
            $tokens = array();
            foreach ($order as $key => $order) {
                if (in_array($order, array('DESC', 'ASC'))) {
                    $tokens []= "`$key` $order";
                }
            }
            $orderSQL = implode(', ', $tokens);
        }

        return $orderSQL;
    }

    /**
     * Builds the LIMIT SQL clause
     *
     * @param int $elements number of elements
     * @param int $offset the page number to show
     *
     * @return string the LIMIT SQL clause
     **/
    public function getLimitSQL($elements = 20, $offset = 1)
    {
        $limitSQL = '';
        if ($offset == 1) {
            $limitSQL = ' LIMIT '. $elements;
        } elseif ($offset > 1) {
            $limitSQL = ' LIMIT '.($offset-1)*$elements.', '.$elements;
        }

        return $limitSQL;
    }

    /**
     * Searches one entity given a criteria and an order
     *
     * @param array|string $criteria the criteria to search for an entity
     * @param array $order the order to
     *
     * @return Object the object searched
     **/
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
     * Indicates if the EntityRepository has the cache handler enabled
     *
     * @return boolean true if it has cache
     **/
    protected function hasCache()
    {
        return $this->cache != null;
    }

    /**
     * Removes the object into database
     *
     * @param Object $object the object instance to remove
     *
     * @return boolean
     **/
    public function remove($object)
    {
        return $object->remove();
    }
}
