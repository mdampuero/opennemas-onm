<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class for handling content-category relations operations
 *
 * @package Onm
 * @subpackage Model
 */
class ContentCategoryManager
{
    /**
     * @var array with categories
     **/
    public $categories = null;

    /**
     * @var ContentCategoryManager instance, singleton pattern
     **/
    private static $_instance = null;

    /**
     * @var MethodCacheManager cache, object to cache requests
     **/
    public $cache = null;

    public function __construct()
    {
        if (is_null(self::$_instance)) {
            // Posibilidad de cachear resultados de métodos
            $this->cache = new MethodCacheManager($this, array('ttl' => 300));

            // Rellenar categorías dende caché
            $this->categories = $this->cache->populate_categories();

            self::$_instance = $this;

            return self::$_instance;
        } else {
            return self::$_instance;
        }
    }

    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ContentCategoryManager();

            return self::$_instance;
        } else {
            return self::$_instance;
        }
    }

    /**
     *  reload internal array $this->categories and
     *  delete APC cache
     *  call when change or create categories
     *
     * @return array Array with Content_category objects
     **/
    public function reloadCategories()
    {
        $this->categories = null;
        $method ='populate_categories';
        $args   = array();
        $key    = 'ContentCategoryManager'.$method.md5(serialize($args));
        if (defined('APC_PREFIX')) {
            $key = APC_PREFIX . $key;
        }

        $result = apc_delete($key);
        $result = call_user_func_array(array('ContentCategoryManager', $method),
            $args);
        apc_store($key, serialize($result), 300);

        return $result ;

    }

    /**
     * populate_categories, load internal array $this->categories for a
     * singleton instance
     *
     * @return array Array with Content_category objects
    */
    public function populate_categories()
    {
        $sql = 'SELECT * FROM content_categories ORDER BY posmenu ASC';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        // clear content
        $this->categories = array();
        if ($rs!=false) {
            while ($obj = $rs->FetchNextObject($toupper = false)) {
                $this->categories[$obj->pk_content_category] = $obj;
            }
        }

        return $this->categories;
    }

    /**
     * Normalize names of category and subcategory
     *
     * @param $categoryName Name of category
     * @param $subcategoryName Name of subcategory
     * @return array Return categoryName and subcategoryName fixed
    */
    public function normalize($categoryName, $subcategoryName=null)
    {
        if (!empty($subcategoryName)) {
            // It's a father category
            return array($categoryName, $subcategoryName);
        }

        $father = $this->get_father($categoryName);
        if (!empty($father)) {
            return array($father, $categoryName);
        }

        // If don't match return same values
        return array($categoryName, $subcategoryName);
    }

 /**
     * find objects of category and subcategory
     *
     * @param $filter - filter of sql
     * @param $order_by
     * @return array Return category objects
    */
    public function find($filter = NULL, $orderBy = 'ORDER BY 1')
    {
        $items = array();
        $where = '1=1';

        if (!is_null($filter)) {
            $where = $filter;
        }

        $sql = 'SELECT * FROM content_categories ' .
                'WHERE internal_category<>0 AND '.$where.' '.$orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $obj = new ContentCategory();
                $obj->load($rs->fields);

                $items[] = $obj;
                $rs->MoveNext();
            }
        }

        return $items;
    }

    /**
     * find category and subcategory of type content.
     * @param $fkContentType type of elements category.
     * @param $filter - filter of sql
     * @param $order_by
     * @return array Return category objects
    */
    public function find_by_type(
        $fkContentType,
        $filter    = NULL,
        $orderBy ='ORDER BY 1'
    ) {
        $_where = 'fk_content_type='. $fkContentType .' ';

        return $this->find($_where);
    }

    //Devuelve el nombre de una categoria para los upload y posible las urls
    public function get_name($id)
    {
        if (is_null($this->categories) ) {
            $sql = 'SELECT name FROM content_categories '
                 . 'WHERE pk_content_category = ?';
            $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

            if (!$rs) {
                \Application::logDatabaseError();

                return false;
            }

            return $rs->fields['name'];
        }
        if (isset($this->categories[$id]->name)) {
            return $this->categories[$id]->name;
        } else {
            return false;
        }
    }


    /**
     * Returns the position in menu
     *
     * @param  int $id Category ID
     * @return int Return posmenu
     */
    public function get_pos($id)
    {
        if (is_null($this->categories)) {
            if (is_numeric($id)) {
                $sql = 'SELECT posmenu FROM content_categories'
                     . ' WHERE pk_content_category = ?';
                $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

                if (!$rs) {
                    \Application::logDatabaseError();

                    return;
                }

                return $rs->fields['posmenu'];
            }

            return 0;
        }

        if (!isset($this->categories[$id])) {
            return 0;
        }

        // Singleton version
        return $this->categories[$id]->posmenu;
    }

    //Returns cetegory id
    public function get_id($categoryName)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT pk_content_category '
                 . 'FROM content_categories WHERE name = ?';
            $rs  = $GLOBALS['application']->conn->Execute($sql,
                array($categoryName));

            if (!$rs) {
                \Application::logDatabaseError();

                return false;
            }

            return $rs->fields['pk_content_category'];
        }

        // Singleton version
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return $category->pk_content_category;
            }
        }

        return 0;
    }

    /**
     * Return first category data in the menu of type
     *
     * @param int $categoryType content_type.
     *
     * @return array with category data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function getFirstCategory($categoryType)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT * FROM content_categories WHERE '
                   .' inmenu=1 AND internal_category=?'
                   .' ORDER BY posmenu LIMIT 1';

            $rs = $GLOBALS['application']->conn->Execute($sql,
                array($categoryType));

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            return $rs->fields;
        }

        // Singleton version
        $categories = $this->order_by_posmenu($this->categories);

        foreach ($categories as $category) {
            if (($category->internal_category == $categoryType)
                && ($category->inmenu==1)
            ) {

                return $category->name;
            }
        }
    }

    //Returns the title of category
    public function get_title($categoryName)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT title FROM content_categories WHERE name = ?';

            $rs = $GLOBALS['application']->conn->Execute($sql,
                array($categoryName));

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            return $rs->fields['title'];
        }

        // Singleton version
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return $category->title;
            }
        }

        return '';
    }

    public function getByName($categoryName)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT title FROM content_categories WHERE name = ?';

            $rs = $GLOBALS['application']->conn->Execute($sql,
                array($categoryName));

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            return $rs->fields;
        }

        // Singleton version
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return $category;
            }
        }

        return null;
    }

    //Returns an all cetegories array
    public function get_all_categories()
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT name FROM content_categories';

            $rs = $GLOBALS['application']->conn->Execute($sql);

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            $items = array ();
            while (!$rs->EOF) {
                $str = $rs->fields['name'];
                $items[$str]=0;
                $rs->MoveNext();
            }

            return $items;
        }

        // Singleton version
        $items = array();
        foreach ($this->categories as $category) {
            $items[$category->name] = 0;
        }

        return $items;
    }

    // Returns an all cetegories array
    public function get_all_categoriesID()
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT pk_content_category '
                 . 'FROM content_categories WHERE internal_category=1';
            $rs = $GLOBALS['application']->conn->Execute($sql);

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }
            $items = array ();
            while (!$rs->EOF) {
                $str = $rs->fields['pk_content_category'];
                $items[$str] = $str;
                $rs->MoveNext();
            }

            return $items;
        }

        // Singleton version
        $items = array();
        foreach ($this->categories as $category) {
            if ($category->internal_category == 1) {
                $items[$category->pk_content_category] =
                    $category->pk_content_category;
            }
        }

        return $items;
    }

    //Returns an array with subcetegories from a single category
    //with internal_name as index
    public function get_all_subcategories($id)
    {
        if ( is_null($this->categories) ) {
            $sql = 'SELECT name,title,internal_category '
                 . 'FROM content_categories WHERE internal_category<>0 '
                 .' AND inmenu=1 AND fk_content_category = ? ORDER BY posmenu';

            $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            $items = array ();
            while (!$rs->EOF) {
                $items[$rs->fields['name']]['title'] = $rs->fields['title'];
                $items[$rs->fields['name']]['internal_category'] =
                    $rs->fields['title'];
                $rs->MoveNext();
            }
        }

        // Singleton version
        $categories = $this->order_by_posmenu($this->categories);

        $items = array ();
        foreach ($categories as $category) {
            if (($category->internal_category)
                && ($category->inmenu == 1)
                && ($category->fk_content_category == $id)
            ) {
                $items[$category->name]['title'] = $category->title;
                $items[$category->name]['internal_category'] =
                    $category->internal_category;
            }
        }

        return $items;
    }

    public function order_by_posmenu($categories)
    {
        $categories = array_values($categories);

        usort($categories, function($a, $b) {
            if ($b->inmenu == 0) {
                return 0;
            }
            if ($a->inmenu == 0) {
                return +1;
            }
            if ($a->posmenu == $b->posmenu) {

            }

            return ($a->posmenu > $b->posmenu) ? +1 : -1;
        });

        return $categories;
    }

    public function group_by_type($categories)
    {
        $categories = array_values($categories);

        usort($categories, function ($a, $b) {
            //Las que no están en el menú colocarlas al final
            if ($b->internal_category == 0) {
                 return 0;
            }
            if ($a->internal_category == 0) {
                 return +1;
            }
            if ($a->internal_category == $b->internal_category) {
                return ($a->posmenu > $b->posmenu) ? +1 : -1;
            }

            return ($a->internal_category < $b->internal_category) ? 1 : +1;

        });

        return $categories;
    }

    /**
     * Get a tree with categories and subcategories
     *
     * @todo To do work recursive for varios nested levels
     * @return array Tree structure
    */
    public function getCategoriesTree()
    {
        $tree = array();

        $categories = $this->order_by_posmenu($this->categories);

        // First loop categories
        foreach ($categories as $category) {
            if ($category->fk_content_category == 0
                && $category->internal_category != 0
            ) {
                $tree[$category->pk_content_category] = $category;
                $tree[$category->pk_content_category]->childNodes = array();
            }
        }

        // Loop on subcategories
        foreach ($categories as $category) {
            if ($category->fk_content_category != 0
                && $category->internal_category != 0
                && isset($tree[$category->fk_content_category])
            ) {

                $tree[$category->fk_content_category]
                    ->childNodes[$category->pk_content_category] = $category;
            }
        }

        return $tree;
    }

     /**
     * Get a tree   categories and subcategories and render for select
     *
     * @todo  To render a select form with categories
     * @return array unidimensional structure for select form
    */
    public function renderCategoriesTree()
    {
        $categories = $this->getCategoriesTreeMenu();
        $i=0;
        $tree =array();
        foreach ($categories as $category) {
            if ($category->fk_content_category == 0
                && $category->internal_category != 0
                && ($category->pk_content_category != 4)
            ) {
                $tree[$i] = new stdClass();
                $tree[$i]->pk_content_category = $category->pk_content_category;
                $tree[$i]->title = ' '. $category->title;
                $i++;
                if (!empty($category->childNodes)) { //subcategorys
                    foreach ($category->childNodes as $subcat) {
                        $tree[$i] = new stdClass();
                        $tree[$i]->pk_content_category =
                            $subcat->pk_content_category;
                        $tree[$i]->title = '      ⇒ '.$subcat->title;

                        $i++;
                    }
                }
            }
        }

        return $tree;
    }

    /**
     * Get a tree with categories and subcategories for menu
     *
     * @todo To do work recursive for varios nested levels
     * @return array Tree structure
    */
    public function getCategoriesTreeMenu()
    {
        $tree = array();

        $categories = $this->order_by_posmenu($this->categories);

        // First loop categories
        foreach ($categories as $category) {
            $category->params = unserialize($category->params);
            if ($category->fk_content_category == 0
                && $category->internal_category == 1
                && $category->inmenu == 1
            ) {
                $tree[$category->pk_content_category] = $category;
                $tree[$category->pk_content_category]->childNodes = array();
            }
        }

        // Loop on subcategories
        foreach ($categories as $category) {
            if ($category->fk_content_category != 0
                && $category->internal_category != 0
                && isset($tree[$category->fk_content_category])
            ) {

                $tree[$category->fk_content_category]
                    ->childNodes[$category->pk_content_category] = $category;
            }
        }

        return $tree;
    }

    //Returns first subcategory from a father category_id
    public function get_first_subcategory($categoryId)
    {
        if ( is_null($this->categories) ) {
            $sql = 'SELECT name FROM content_categories
                    WHERE inmenu=1 AND fk_content_category=?
                    AND internal_category<>0
                    ORDER BY posmenu LIMIT 0,1';

            $rs = $GLOBALS['application']->conn->Execute($sql,
                array($categoryId));

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            return $rs->fields['name'];
        }

        // Singleton version
        $categories = $this->order_by_posmenu($this->categories);

        foreach ($categories as $category) {
            if ($category->fk_content_category == $categoryId
                && $category->inmenu==1
                && $category->internal_category!=0
            ) {
                return $category->name;
            }
        }
    }

    public function get_father($category_name)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT content2.name '
                .'FROM `content_categories` as content1, '
                .'`content_categories` as content2 '
                .'WHERE content1.name=? '
                .'AND content1.fk_content_category='
                .'content2.pk_content_category';

            $rs = $GLOBALS['application']->conn->Execute($sql,
                    array($category_name));
            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            return $rs->fields['name'];
        }


        // Singleton version
        $fk_content_category = '';
        // Search fk_content_category
        foreach ($this->categories as $category) {
            if ($category->name == $category_name) {
                $fk_content_category = $category->fk_content_category;
                break;
            }
        }

        foreach ($this->categories as $category) {
            if ($category->pk_content_category == $fk_content_category) {
                return $category->name;
            }
        }

        // FIXME: if flow of code could arrive here then throw a exception
        return '';
    }

    //Returns false if the category does not exist
    public function exists($category_name)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT count(*) AS total '
                 . 'FROM content_categories WHERE name = ?';
            $rs  = $GLOBALS['application']->conn->GetOne($sql, $category_name);

            return $rs || $rs > 0;
        }

        // Singleton version
        // search into categories internal array ($this->categories)
        foreach ($this->categories as $category) {
            if ($category->name == $category_name) {
                return true;
            }
        }

        return false;
    }

    //Returns true if there is no contents in that category name
    public function isEmpty($category)
    {
        $pk_category = $this->get_id($category);
        $sql1 = 'SELECT count( * )
                 FROM `content_positions`
                 WHERE `fk_category` ='.$pk_category;
        $rs1 = $GLOBALS['application']->conn->Execute($sql1);

        if (!$rs1) {
            \Application::logDatabaseError();

            return;
        }


        $sql = 'SELECT count(pk_content) AS number '
            . 'FROM `contents`, `contents_categories`
            WHERE `contents`.`fk_content_type`=1
            AND `contents`.`in_litter`=0
            AND `contents_categories`.`pk_fk_content_category`=?
            AND `contents`.`pk_content`=`contents_categories`.`pk_fk_content`';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($pk_category));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        return $rs->fields['number'] == 0 && $rs1->fields[0] == 0;
    }

    /**
     * Returns true if there is no contents in that category id
     *
     * @return boolean
     **/
    public function is_Empty($category)
    {
        $sql1 = 'SELECT count(pk_content) AS number
            FROM `contents`, `contents_categories`
            WHERE `fk_content_type`=1
            AND `in_litter`=0
            AND contents_categories.pk_fk_content_category=?
            AND contents.pk_content=pk_fk_content';
        $rs1 = $GLOBALS['application']->conn->Execute($sql1, array($category));

        $sql2 = 'SELECT count(pk_content_category) AS number
            FROM `content_categories`
            WHERE content_categories.fk_content_category = ?';

        $rs2 = $GLOBALS['application']->conn->Execute($sql2, array($category));

        $number = $rs1->fields['number'] + $rs2->fields['number'];

        return $number == 0;
    }

    public function count_content_by_type($category, $type)
    {
        $sql = 'SELECT count(pk_content) AS number
             FROM `contents`,`contents_categories`
             WHERE contents.pk_content=pk_fk_content
             AND pk_fk_content_category=?
             AND `fk_content_type`=?';
        $values = array($category, $type);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        if ($rs->fields['number']) {
            return $rs->fields['number'];
        } else {
            return 0;
        }
    }

    /**
     *
     * @see ContentCategoryManager::count_content_by_type
     **/
    public function count_content_by_type_group($type, $filter=NULL)
    {
        $_where = '1=1';
        if (!is_null($filter)) {
            $_where = $filter;
        }
        $sql = 'SELECT count(contents.pk_content) AS number,
            `contents_categories`.`pk_fk_content_category` AS cat
            FROM `contents`,`contents_categories`
            WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content`
            AND `in_litter`=0 AND `contents`.`fk_content_type`=?
            AND '.$_where.
            ' GROUP BY `contents_categories`.`pk_fk_content_category`';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($type));

        $groups = array();

        if ($rs!==false) {
            while (!$rs->EOF) {
                $groups[$rs->fields['cat']] = $rs->fields['number'];
                $rs->MoveNext();
            }
        }

        return $groups;
    }

    public function countMediaByTypeGroup($filter=NULL)
    {
        $_where = '1=1';
        if (!is_null($filter)) {
            $_where = $filter;
        }
        $sql = 'SELECT count(photos.pk_photo) AS number,
            `contents_categories`.`pk_fk_content_category` AS cat
            FROM `contents_categories`,`photos`,`contents`
            WHERE `photos`.`pk_photo`=`contents`.`pk_content`
            AND `photos`.`pk_photo`=`contents_categories`.`pk_fk_content`
            AND contents.`in_litter`=0
            AND '.$_where.
            ' GROUP BY `contents_categories`.`pk_fk_content_category`';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $groups = array();

        if ($rs!==false) {
            while (!$rs->EOF) {
                $groups[$rs->fields['cat']] = $rs->fields['number'];
                $rs->MoveNext();
            }
        }

        return $groups;
    }

    public function data_media_by_type_group($filter=NULL)
    {
        $_where = '1=1';
        if (!is_null($filter)) {
            $_where = $filter;
        }
        $sql = 'SELECT count(photos.pk_photo) AS number,
            `contents_categories`.`pk_fk_content_category` AS cat,
            sum(`photos`.`size`) as size
            FROM `contents_categories`,`photos`,`contents`
            WHERE `photos`.`pk_photo`=`contents`.`pk_content`
            AND `photos`.`pk_photo`=`contents_categories`.`pk_fk_content`
            AND contents.`in_litter`=0
            AND '.$_where.
            ' GROUP BY `contents_categories`.`pk_fk_content_category`';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        $groups = array();

        if ($rs!==false) {
            while (!$rs->EOF) {
                $groups[ $rs->fields['cat'] ] = new stdClass;
                $groups[ $rs->fields['cat'] ]->total = $rs->fields['number'];
                $groups[ $rs->fields['cat'] ]->size = $rs->fields['size'];
                $rs->MoveNext();
            }
        }

        return $groups;
    }
    /**
     * Order array of category menu and submenues.
     * Get category info if there is one selected or get first category info
     *
     * @param int $internal_category
     *
     * @return array principal categories, childs categorys and category info
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function getArraysMenu($category = NULL, $internalCategory = 1)
    {

        //fullcat contains array with all cats order by posmenu
        //parentCategories is an array with all menu cats in frontpage
        //subcat is an array with all subcat form the parentCategories array
        //$categoryData is the info of the category selected


       //$fullcat = $this->order_by_posmenu($this->categories);
        $fullcat = $this->group_by_type($this->categories);

        $parentCategories = array();
        $categoryData = array();
        foreach ( $fullcat as $prima) {

            if (!empty($category)
                && ($prima->pk_content_category == $category)
                && ($category !='home') && ( $category !='todos')
            ) {
                $categoryData[] = $prima;
            }
            if (($prima->internal_category == 1
                || $prima->internal_category == $internalCategory)
                && ($prima->fk_content_category == 0)
            ) {

                $parentCategories[] = $prima;
            }
        }
        $subcat = array();
        foreach ($parentCategories as $k => $v) {
            $subcat[$k] = array();

            foreach ($fullcat as $child) {
                if ($v->pk_content_category == $child->fk_content_category) {
                    $subcat[$k][] = $child;
                }
            }
        }


        if (empty($category) && !empty($parentCategories)) {
             $categoryData[] = $parentCategories[0];
        }

        return array($parentCategories, $subcat, $categoryData);
    }


   /**
     *
     * Get array with subcategories info from a category id
     *
     * @param int $category_id
     *
     * @return array childs categorys
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    public function getSubcategories($categoryId)
    {
        if (is_null($this->categories)) {
            $this->categories = $this->cache->populate_categories();
        }

        $items = array();
        foreach ($this->categories as $category) {
            if ($category->fk_content_category == $categoryId) {
                $items[]=$category;
            }
        }

        return $items;
    }

    /**
     * Returns the category name of a Content throw ID
     *
     * @param  int $id Content ID
     * @return int Return category name
     */
    public function get_category_name_by_content_id($id)
    {
        if (is_numeric($id)) {
            $sql = 'SELECT catName FROM contents_categories '
                 . 'WHERE pk_fk_content=?';
            $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

            if (!$rs) {
                \Application::logDatabaseError();

                return;
            }

            return $rs->fields['catName'];
        }

        return NULL;
    }


}
