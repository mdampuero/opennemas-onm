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

/**
 * Default BaseManager
 *
 * @package Repository
 **/
class BaseManager
{
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
                if (strpos('SEARCH ', $value)) {

                }
                $filterSQL []= "`$field`='$value'";
            }
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
        if ($offset <= 1) {
            $limitSQL = ' LIMIT '. $elements;
        } elseif ($offset > 1) {
            $limitSQL = ' LIMIT '.($offset-1)*$elements.', '.$elements;
        }

        return $limitSQL;
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
