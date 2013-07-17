<?php
/**
 * Defines the ContentCategoryManager class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Model
 */
/**
 * Class for handling content-category relations operations
 *
 * @package Model
 */
class ContentCategoryManager
{
    /**
     * List of available categories
     *
     * @var array
     **/
    public $categories = null;

    /**
     * The instance of this class, use for the Singleton pattern
     * @var ContentCategoryManager
     **/
    private static $instance = null;

    /**
     * Cache handler
     *
     * @var MethodCacheManager
     **/
    public $cache = null;

    /**
     * Initializes the object class or returns the initialized instance if
     * it was previously created
     *
     * @return ContentCategoryManager
     **/
    public function __construct()
    {
        if (is_null(self::$instance)) {
            $this->cache = new MethodCacheManager($this, array('ttl' => 300));

            // Fill categories from cache
            $this->categories = $this->findAll();

            self::$instance = $this;
        }

        return self::$instance;
    }

    /**
     * Returns an unique instance, Singleton pattern
     *
     * @return ContentCategoryManager the object instance
     **/
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new ContentCategoryManager();

            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * Fetches the available categories and stores them into a property
     *
     * @return array List of ContentCategory objects
    */
    public function findAll()
    {
        global $sc;
        $cache = $sc->get('cache');

        $cacheKey = CACHE_PREFIX.'_content_categories';
        $categories = $cache->fetch($cacheKey);

        if (!$categories) {
            $sql = 'SELECT * FROM content_categories ORDER BY posmenu ASC';
            $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rs = $GLOBALS['application']->conn->Execute($sql);

            if (!$rs) {
                \Application::logDatabaseError();

                return false;
            }

            $categories = array();
            if ($rs != false) {
                $data = $rs->getArray();

                foreach ($data as $catData) {
                    $category = new \ContentCategory();
                    $category->load($catData);
                    $categories[$category->id] = $category;
                }
            }

            $cache->save($cacheKey, $categories, 300);
        }

        $this->categories = $categories;

        return $this->categories;
    }

    /**
     * Normalize names of category and subcategory
     *
     * @param $categoryName Name of category
     * @param $subcategoryName Name of subcategory
     *
     * @return array Return categoryName and subcategoryName fixed
    */
    public function normalize($categoryName, $subcategoryName = null)
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
     * @param string $filter SQL WHERE clause
     * @param string $orderBy ORDER BY clause
     *
     * @return array List of ContentCategory objects
     */
    public function find($filter = null, $orderBy = 'ORDER BY 1')
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
     * Returns the category name given its id
     *
     * @param int $id the category id
     *
     * @return string the category name
     * @return boolean false if the category doesn't exists
     **/
    public function get_name($id)
    {
        if (is_null($this->categories)) {
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
     *
     * @return int Category position
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

    /**
     * Returns the category id from its name
     *
     * @param string $categoryName the category name
     *
     * @return int the category id
     **/
    public function get_id($categoryName)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT pk_content_category '
                 . 'FROM content_categories WHERE name = ?';
            $rs  = $GLOBALS['application']->conn->Execute($sql, array($categoryName));

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

            $rs = $GLOBALS['application']->conn->Execute($sql, array($categoryType));

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

    /**
     * Returns the title "Human readablle name" of a category given its name
     *
     * @param string $categoryName the category name
     *
     * @return string the category title
     **/
    public function get_title($categoryName)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT title FROM content_categories WHERE name = ?';

            $rs = $GLOBALS['application']->conn->Execute($sql, array($categoryName));

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

    /**
     * Returns a category object given its name
     *
     * @param string $categoryName
     *
     * @return string the category object
     **/
    public function getByName($categoryName)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT title FROM content_categories WHERE name = ?';

            $rs = $GLOBALS['application']->conn->Execute($sql, array($categoryName));

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

    /**
     * Returns an array with all the available category objects
     *
     * @return array ContentCategory object list
     **/
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

    /**
     * Returns an array with subcetegories from a single category
     * with internal_name as index
     *
     * @param int $id the id of the parent category
     *
     * @return array list of ContentCategory objects
     **/
    public function get_all_subcategories($id)
    {
        if (is_null($this->categories)) {
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

    /**
     * Sorts an array of categories by its posmenu property
     *
     * @param array $categories the list of categories to sort
     *
     * @return array the sorted list of categories
     **/
    public function order_by_posmenu($categories)
    {
        $categories = array_values($categories);

        if (count($categories) > 0) {
            usort(
                $categories,
                function (
                    $a,
                    $b
                ) {
                    if ($b->inmenu == 0) {
                        return 0;
                    }
                    if ($a->inmenu == 0) {
                        return +1;
                    }
                    if ($a->posmenu == $b->posmenu) {

                    }

                    return ($a->posmenu > $b->posmenu) ? +1 : -1;
                }
            );
        }

        return $categories;
    }

    /**
     * Sorts an array of categories by its internal_category property
     *
     * @param array $categories the list of categories to sort
     *
     * @return array the sorted list of categories
     **/
    public function groupByType($categories)
    {
        $categories = array_values($categories);

        if (count($categories) > 0) {
            usort(
                $categories,
                function (
                    $a,
                    $b
                ) {
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

                }
            );
        }

        return $categories;
    }

    /**
     * Get a tree with categories and subcategories
     *
     * TODO: To do work recursive for varios nested levels
     *
     * @return array Tree structure
     **/
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
     * TODO:  To render a select form with categories
     *
     * @return array unidimensional structure for select form
     **/
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
                if (!empty($category->childNodes)) {
                    //subcategorys
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
     * TODO: To do work recursive for varios nested levels
     *
     * @return array Tree structure
     **/
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

    /**
     * Returns first subcategory given the parente category id
     *
     * @param int $categoryId the category id
     *
     * @return string the category name
     **/
    public function getFirstSubcategory($categoryId)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT name FROM content_categories
                    WHERE inmenu=1 AND fk_content_category=?
                    AND internal_category<>0
                    ORDER BY posmenu LIMIT 0,1';

            $rs = $GLOBALS['application']->conn->Execute($sql, array($categoryId));

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

    /**
     * Returns the father of a category given its name
     *
     * @param string $category_name the category name
     *
     * @return string the parent category name
     **/
    public function get_father($category_name)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT content2.name '
                .'FROM `content_categories` as content1, '
                .'`content_categories` as content2 '
                .'WHERE content1.name=? '
                .'AND content1.fk_content_category='
                .'content2.pk_content_category';

            $rs = $GLOBALS['application']->conn->Execute($sql, array($category_name));
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

    /**
     * Checks if exists one category given its name
     *
     * @param string $category_name the name of the category
     *
     * @return boolean true if the category exists
     **/
    public function exists($category_name)
    {
        if (is_null($this->categories)) {
            $sql = 'SELECT count(*) AS total '
                 . 'FROM content_categories WHERE name = ?';
            $rs  = $GLOBALS['application']->conn->GetOne($sql, $category_name);

            return $rs || $rs > 0;
        }

        // Singleton version
        // searches within the interal categories array ($this->categories)
        foreach ($this->categories as $category) {
            if ($category->name == $category_name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if there is no contents in category given its name
     *
     * @param string $categoryName the category name
     *
     * @return boolean true if the category has no contents
     **/
    public function isEmpty($categoryName)
    {
        $pk_category = $this->get_id($categoryName);
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
     * @param int $category the category id
     *
     * @return boolean
     **/
    public function isEmptyByCategoryId($category)
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

    /**
     * Counts the contents from a category
     *
     * @param int    $category the category id
     * @param string $type the group type where to search from
     *
     * @return array the counters for a category
     **/
    public function countContentByType($category, $type)
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
     * Counts the contents from a group type
     *
     * @param string $type the group type where to search from
     * @param string $filter the WHERE SQL clause to filter contents from
     *
     * @return array the counters for all the group types
     *
     * @see ContentCategoryManager::countContentByType
     **/
    public function countContentsByGroupType($type, $filter = null)
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

    /**
     * Counts the media elements from a group type
     *
     * @param string $filter the WHERE clause to filter the contents with
     *
     * @return the counters for all the group types
     **/
    public function countMediaByTypeGroup($filter = null)
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

    /**
     * Order array of category menu and submenues.
     * Get category info if there is one selected or get first category info
     *
     * @param int $category the category id
     * @param int $internalCategory 1 if only return internal categories
     *
     * @return array principal categories, childs categorys and category info
     */
    public function getArraysMenu($category = null, $internalCategory = 1)
    {
        //fullcat contains array with all cats order by posmenu
        //parentCategories is an array with all menu cats in frontpage
        //subcat is an array with all subcat form the parentCategories array
        //$categoryData is the info of the category selected

        //$fullcat = $this->order_by_posmenu($this->categories);
        $fullcat = $this->groupByType($this->categories);

        $parentCategories = array();
        $categoryData = array();
        foreach ($fullcat as $prima) {

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
     * @param int $categoryId the category id
     *
     * @return array childs categorys
     */
    public function getSubcategories($categoryId)
    {
        if (is_null($this->categories)) {
            $this->categories = $this->cache->populateCategories();
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

        return null;
    }
}
