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
        $filterSQL = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_content) FROM `contents`, `advertisements`"
            . " WHERE $filterSQL AND pk_content=pk_advertisement";

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
     * @param  integer      $count           Whether if fetch the total number of elements
     *
     * @return array                         The matched elements.
     */
    public function findBy($criteria, $order = null, $elementsPerPage = null, $page = null, $offset = 0, &$count = null)
    {
        // Building the SQL filter
        $filterSQL  = $this->getFilterSQL($criteria);
        $orderBySQL = '`pk_content` DESC';

        if (!empty($order)) {
            $orderBySQL = $this->getOrderBySQL($order);
        }

        $limitSQL = $this->getLimitSQL($elementsPerPage, $page, $offset);

        // Executing the SQL
        $sql = "SELECT " . (($count) ? "SQL_CALC_FOUND_ROWS  " : "") .
            " content_type_name, pk_content FROM `contents`, `advertisements`
            WHERE $filterSQL AND pk_content=pk_advertisement
            ORDER BY $orderBySQL $limitSQL";

        $rs = $this->dbConn->fetchAll($sql);

        if ($count) {
            $count = $this->getSqlCount();
        }

        $contentIdentifiers = [];
        foreach ($rs as $resultElement) {
            $contentIdentifiers[] = [ $resultElement['content_type_name'], $resultElement['pk_content'] ];
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
            $filterSQL = $criteria;
        } else {
            $filterSQL = [];

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

                $fieldFilters = [];
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
                                . ",)|(," . $value . "$)|(^" . $value . "$)|(^"
                                . $value . ",[\d]*)'";
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

        $filterSQL = preg_replace('@position="(\d*)"@', ' EXISTS ('
            . 'SELECT 1 FROM advertisements_positions WHERE position_id IN ($1) and advertisement_id = pk_advertisement'
            . ')', $filterSQL);

        return $filterSQL;
    }

    /**
     * Retrieves the list of ads for a list of types and a category
     *
     * @param array $types the list of position ids
     * @param int   $category the id of the category
     *
     * @return array the list of arrays
     */
    public function findByPositionsAndCategory($types = [], $category = 0)
    {
        if (!is_array($types) || count($types) <= 0) {
            return [];
        }

        $category = (empty($category) || ($category == 'home')) ? 0 : $category;

        if (!getService('core.security')->hasExtension('ADS_MANAGER')) {
            return $this->findDefaultAdvertisements($types, $category);
        }

        $generics = true;
        $config   = getService('setting_repository')->get('ads_settings');

        if (isset($config['no_generics'])
            && ($config['no_generics'] == '1')
        ) {
            $generics = false;
        }

        return $this->findAdvertisements($types, $category, $generics);
    }

    /**
     * Returns the list of advertisements basing on the list of positions to
     * render, the current category and the generic advertisements flag.
     *
     * @param array   $types    The list of positions to render.
     * @param integer $category The current category id.
     * @param boolean $generics Whether generig advertisements are enabled.
     *
     * @return array The list of advertisements.
     */
    protected function findAdvertisements($types, $category, $generics)
    {
        $types = implode(', ', $types);

        $sql = 'SELECT pk_advertisement as id FROM advertisements '
            . 'WHERE EXISTS('
            . ' SELECT 1 FROM advertisements_positions WHERE position_id IN (%s) AND pk_advertisement=advertisement_id'
            . ') AND (fk_content_categories IS null or %s) '
            . 'ORDER BY id';

        $categories = '';

        // Return advertisements in frontpage if generic advertisements allowed
        if ($category !== 0 && $generics) {
            $categories = 'fk_content_categories REGEXP "^0($|,)|,\s*0\s*,|(^|,)\s*0$" OR ';
        }

        $categories .= sprintf(
            'fk_content_categories REGEXP "^%s($|,)|,\s*%s\s*,|(^|,)\s*%s$"',
            $category,
            $category,
            $category
        );

        $sql = sprintf($sql, $types, $categories);

        try {
            $result = $this->dbConn->fetchAll($sql);
            $result = array_map(function ($element) {
                return [ 'Advertisement', $element['id'] ];
            }, $result);
        } catch (\Exception $e) {
            return [];
        }

        $advertisements = $this->findMulti($result);

        return array_filter($advertisements, function ($a) {
            if (!is_object($a)
                || (!$a->isInTime()
                && $a->type_medida == 'DATE')
                || $a->content_status != 1
                || $a->in_litter != 0
            ) {
                return false;
            }

            return true;
        });
    }

    /**
     * Returns the list of default advertisements.
     *
     * @param array   $types    the list of types to fetch
     * @param integer $category the category id
     *
     * @return array the list of ads
     */
    protected function findDefaultAdvertisements($types, $category)
    {
        $ads = include APP_PATH . 'config/ads/onm_default_ads.php';

        $ads = array_filter($ads, function ($a) use ($types, $category) {
            $isOfType = false;
            foreach ($types as $type) {
                $isOfType |= in_array($type, $a->positions);
            }

            return $isOfType
                && (is_null($a->fk_content_categories)
                    || in_array($category, $a->fk_content_categories)
                    || in_array(0, $a->fk_content_categories));
        });

        return $ads;
    }
}
