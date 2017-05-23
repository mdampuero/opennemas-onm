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

    /**
     * Retrieves the list of ads for a list of types and a category
     *
     * @param array $types the list of position ids
     * @param int   $category the id of the category
     *
     * @return array the list of arrays
     **/
    public static function findByPositionsAndCategory($types = [], $category = 0)
    {
        $banners = [];

        // If advertisement types aren't passed return earlier
        if (!is_array($types) || count($types) <= 0) {
            return $banners;
        }

        // Check category
        $category = (empty($category) || ($category=='home')) ? 0 : $category;

        if (!getService('core.security')->hasExtension('ADS_MANAGER')) {
            // Fetch ads from static file
            $advertisements = include APP_PATH.'config/ads/onm_default_ads.php';

            foreach ($advertisements as $ad) {
                if (in_array($ad->type_advertisement, $types) &&
                    (
                        in_array($category, $ad->fk_content_categories) ||
                        in_array(0, $ad->fk_content_categories) ||
                        $ad->fk_content_categories == null
                    )
                ) {
                    $banners []= $ad;
                }
            }
        } else {
            // Get string of types separated by commas
            $types = '(' . implode('|', $types) . '){1}';
            $types = sprintf('"^%s($|,)|,\s*%s\s*,|(^|,)\s*%s$"', $types, $types, $types);

            // Generate sql with or without category
            $catsSQL = '';
            if ($category !== 0) {
                $config = getService('setting_repository')->get('ads_settings');
                if (isset($config['no_generics'])
                    && ($config['no_generics'] == '1')
                ) {
                    $generics = '';
                } else {
                    $generics = ' OR fk_content_categories=0';
                }
                $catsSQL = 'AND (advertisements.fk_content_categories LIKE \'%'.$category.'%\' '.$generics.') ';
            } else {
                $catsSQL = 'AND advertisements.fk_content_categories=0';
            }

            $catsSQL .= ' OR advertisements.fk_content_categories IS EMPTY';

            try {
                $sql = "SELECT pk_advertisement as id FROM advertisements "
                    . "WHERE advertisements.type_advertisement REGEXP $types "
                    . " $catsSQL ORDER BY id";
                $conn = getService('dbal_connection');
                $result = $conn->fetchAll($sql);
            } catch (\Exception $e) {
                return $banners;
            }

            if (count($result) <= 0) {
                return $banners;
            }

            $result = array_map(function ($element) {
                return array('Advertisement', $element['id']);
            }, $result);

            $adManager = getService('advertisement_repository');
            $advertisements = $adManager->findMulti($result);

            foreach ($advertisements as $advertisement) {
                // Dont use this ad if is not in time
                if (!is_object($advertisement)
                    || $advertisement->content_status != 1
                    || $advertisement->in_litter != 0
                ) {
                    continue;
                }

                if (is_string($advertisement->params)) {
                    $advertisement->params = unserialize($advertisement->params);
                    if (!is_array($advertisement->params)) {
                        $advertisement->params = [];
                    }
                }

                // If the ad doesn't belong to the given category or home, skip it
                if (!in_array($category, $advertisement->fk_content_categories)
                    && !in_array(0, $advertisement->fk_content_categories)
                    && !empty($advertisement->fk_content_categories)
                ) {
                    continue;
                }

                $banners []= $advertisement;
            }
        }

        return $banners;
    }
}
