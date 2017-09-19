<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Repository;

use Onm\Cache\CacheInterface;

/**
 * An EntityRepository serves as a repository for entities with generic as well
 * as business specific methods for retrieving entities.
 *
 * This class is designed for inheritance and users can subclass this class to
 * write their own repositories with business-specific methods to locate
 * entities.
 *
 * TODO: When new ORM is merged, keep this class only to manage the different
 *       menu positions.
 */
class MenuManager extends BaseManager
{
    /**
     * The default values for menu.
     *
     * @var array
     */
    protected $defaultMenu = [
        'description'  => 'A simple menu',
        'default_menu' => 'frontpage',
        'class'        => 'menu',
        'template'     => '<div id="%1$s" class="menu %2$s">[menu]</div>',
    ];

    /**
     * The list of menus.
     *
     * @var array
     */
    protected $menus = [];

    /**
     * Initializes the menu manager.
     *
     * @param Connection     $dbConn      The custom DBAL wrapper.
     * @param CacheInterface $cache       The cache instance.
     * @param string         $cachePrefix The cache prefix.
     */
    public function __construct($dbConn, CacheInterface $cache, $cachePrefix)
    {
        $this->dbConn      = $dbConn;
        $this->cache       = $cache;
        $this->cachePrefix = $cachePrefix;
    }

    /**
     * Counts searched menus given a criteria.
     *
     * @param array $criteria The criteria used to search.
     *
     * @return integer The amount of elements.
     */
    public function countBy($criteria)
    {
        // Building the SQL filter
        $whereSQL = $this->getFilterSQL($criteria);

        // Executing the SQL
        $sql = "SELECT COUNT(pk_menu) FROM `menues` WHERE $whereSQL";

        $rs = $this->dbConn->fetchArray($sql);

        if (!$rs) {
            return 0;
        }

        return $rs[0];
    }

    /**
     * Finds one menu from the given a menu id.
     *
     * @param integer $id Menu id.
     *
     * @return Menu The matched menu.
     */
    public function find($id)
    {
        $cacheId = "menu" . $this->cacheSeparator . $id;
        $entity  = null;

        if (true || !$this->hasCache()
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
     * @param array   $criteria        The criteria used to search.
     * @param array   $order           The order applied in the search.
     * @param integer $elementsPerPage The max number of elements.
     * @param integer $page            The offset to start with.
     *
     * @return array The matched elements.
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

        $rs = $this->dbConn->fetchAll($sql);

        $ids = [];
        foreach ($rs as $resultElement) {
            $ids[] = $resultElement['pk_menu'];
        }

        $menus = $this->findMulti($ids);

        return $menus;
    }

    /**
     * Find multiple menus from a given array of menu ids.
     *
     * @param array $data Array of preprocessed menu ids.
     *
     * @return array Array of menus.
     */
    public function findMulti(array $data)
    {
        $ids  = [];
        $keys = [];
        foreach ($data as $value) {
            $ids[]  = 'menu' . $this->cacheSeparator . $value;
            $keys[] = $value;
        }

        $menus = array_values($this->cache->fetch($ids));

        $cachedIds = [];
        foreach ($menus as $menu) {
            $ordered[$menu->pk_menu] = $menu;
            $cachedIds[]             = 'menu' . $this->cacheSeparator . $menu->pk_menu;
        }

        $missedIds = array_diff($ids, $cachedIds);

        foreach ($missedIds as $id) {
            list($contentType, $contentId) = explode($this->cacheSeparator, $id);

            $menu    = $this->find($contentId);
            $menus[] = $menu;
        }
        // Unused var $contentType
        unset($contentType);

        $ordered = [];
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
     * Adds a new menu to the list of menus.
     *
     * @param array $menu The menu definition.
     */
    public function addMenu($menu)
    {
        if (!is_array($menu)
            || !array_key_exists('name', $menu)
        ) {
            throw new \Exception(_('Unable to register the menu'));
        }

        $this->menus[$menu['name']] = array_merge($this->defaultMenu, $menu);
    }

    /**
     * Adds a list of menus to the list of menus.
     *
     * @param string $name The menu name.
     * @param string $file The menu configuration.
     */
    public function addMenus($menus)
    {
        foreach ($menus as $menu) {
            $this->addMenu($menu);
        }
    }

    /**
     * Returns the menu definition
     *
     * @param string $name The menu name.
     *
     * @return mixed The menu definition if it exists. False otherwise.
     */
    public function getMenu($name)
    {
        if (!array_key_exists($name, $this->menus)) {
            return false;
        }

        return $this->menus[$name];
    }

    /**
     * Returns the list of menu names.
     *
     * @return array The list of menus.
     */
    public function getMenus()
    {
        return array_map(function ($a) {
            return $a['name'];
        }, $this->menus);
    }

    /**
     * Deletes a menu from cache.
     *
     * @param integer $id Menu id.
     */
    public function delete($id)
    {
        $this->cache->delete('menu' . $this->cacheSeparator . $id);
    }
}
