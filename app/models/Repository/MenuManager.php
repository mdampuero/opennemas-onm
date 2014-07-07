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
class MenuManager extends BaseManager
{
    /**
     * Initializes the entity manager.
     *
     * @param CacheInterface $cache the cache instance.
     */
    public function __construct(DbalWrapper $dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Counts searched menus given a criteria.
     *
     * @param  array|string $criteria The criteria used to search.
     * @return integer                The amount of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL  = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_menu) FROM `menues` WHERE $whereSQL";

        $this->dbConn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one menu from the given a menu id.
     *
     * @param  integer $id Menu id.
     * @return Menu
     */
    public function find($id)
    {
        $cacheId = "menu" . $this->cacheSeparator . $id;
        $entity  = null;

        if (!$this->hasCache()
            || ($entity = $this->cache->fetch($cacheId)) === false
            || !is_object($entity)
        ) {
            $entity = new \Menu($id);

            if ($this->hasCache()) {
                $this->cache->save($cacheId, $entity);
            }
        }

        return $entity;
    }

    /**
     * Searches for menus given a criteria
     *
     * @param  array|string $criteria        The criteria used to search.
     * @param  array        $order           The order applied in the search.
     * @param  integer      $elementsPerPage The max number of elements.
     * @param  integer      $page            The offset to start with.
     * @return array                         The matched elements.
     */
    public function findBy($criteria, $order, $elementsPerPage = null, $page = null)
    {
        // Building the SQL filter
        $whereSQL = $this->getFilterSQL($criteria);

        $orderSQL = '`pk_menu` DESC';
        if (!empty($order)) {
            $orderSQL = $this->getOrderBySQL($order);
        }
        $limitSQL = $this->getLimitSQL($elementsPerPage, $page);

        // Executing the SQL
        $sql = "SELECT pk_menu FROM `menues` WHERE $whereSQL ORDER BY $orderSQL $limitSQL";

        $this->dbConn->setFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->dbConn->fetchAll($sql);

        $ids = array();
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['pk_menu'];
        }

        $menus = $this->findMulti($ids);

        return $menus;
    }

    /**
     * Find multiple menus from a given array of menu ids.
     *
     * @param  array $data Array of preprocessed menu ids.
     * @return array       Array of menus.
     */
    public function findMulti(array $data)
    {
        $ids = array();
        $keys = array();
        foreach ($data as $value) {
            $ids[] = 'menu' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $menus = array_values($this->cache->fetch($ids));

        $cachedIds = array();
        foreach ($menus as $menu) {
            $ordered[$menu->pk_menu] = $menu;
            $cachedIds[] = 'menu' . $this->cacheSeparator . $menu->pk_menu;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $id) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $id);
            $menu = $this->find($contentId);
            $menus[] = $menu;
        }

        $ordered = array();
        foreach ($keys as $id) {
            $i = 0;
            while ($i < count($menus) && $menus[$i]->pk_menu != $id) {
                $i++;
            }

            if ($i < count($menus)) {
                $ordered[] = $menus[$i];
            }
        }

        return $ordered;
    }

    /**
     * Deletes a menu and its items.
     *
     * @param integer $id Menu id.
     */
    public function delete($id)
    {
        // $this->dbConn->transactional(function ($em) use ($id) {
        //     $em->executeQuery('DELETE FROM `menues` WHERE `pk_menu`= ' . $id);
        //     $em->executeQuery('DELETE FROM `menu_items` WHERE `pk_menu`= ' . $id);
        // });

        $this->cache->delete('menu' . $this->cacheSeparator . $id);
    }
}
