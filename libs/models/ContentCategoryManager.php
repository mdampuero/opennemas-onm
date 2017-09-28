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
     */
    public $categories = null;

    /**
     * The instance of this class, use for the Singleton pattern
     * @var ContentCategoryManager
     */
    private static $instance = null;

    /**
     * Cache handler
     *
     * @var MethodCacheManager
     */
    public $cache = null;

    /**
     * Initializes the object class or returns the initialized instance if
     * it was previously created
     *
     * @return ContentCategoryManager
     */
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
     */
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
        if (count($this->categories) > 0) {
            return $this->categories;
        }

        $cache = getService('cache');
        $cacheKey = 'content_categories';
        $categories = $cache->fetch($cacheKey);

        if ($categories) {
            $this->categories = $categories;

            return $this->categories;
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT * FROM content_categories ORDER BY posmenu ASC'
            );

            if (!$rs) {
                return [];
            }

            foreach ($rs as $row) {
                $category = new \ContentCategory();
                $category->load($row);
                $categories[$category->id] = $category;
            }

            $cache->save($cacheKey, $categories, 300);

            $this->categories = $categories;

            return $this->categories;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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

        $father = $this->getFather($categoryName);
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
    public function find($filter = null, $orderBy = '')
    {
        $items = [];

        $where = '';
        if (!is_null($filter)) {
            $where = ' AND '.$filter;
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT * FROM content_categories ' .
                'WHERE internal_category<>0 '.$where.' '.$orderBy
            );
            foreach ($rs as $row) {
                $obj = new ContentCategory();
                $obj->load($row);

                $items[] = $obj;
            }

            return $items;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Returns the category name given its id
     *
     * @param int $id the category id
     *
     * @return string the category name
     * @return boolean false if the category doesn't exists
     */
    public function getName($id)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAssoc(
                    'SELECT name FROM content_categories '
                    . 'WHERE pk_content_category = ?',
                    [ (int) $id ]
                );

                if (!$rs) {
                    return false;
                }

                return $rs['name'];
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        if (isset($this->categories[$id]->name)) {
            return $this->categories[$id]->name;
        } else {
            return false;
        }
    }

    /**
     * Returns the category id from its name
     *
     * @param string $categoryName the category name
     *
     * @return int the category id, 0 if not found
     */
    public function get_id($categoryName)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAssoc(
                    'SELECT pk_content_category FROM content_categories WHERE name = ?',
                    [ $categoryName ]
                );

                if (!$rs) {
                    return false;
                }

                return $rs['pk_content_category'];
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
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
     * Returns the title "Human readablle name" of a category given its name
     *
     * @param string $categoryName the category name
     *
     * @return string the category title
     */
    public function getTitle($categoryName)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAssoc(
                    'SELECT title FROM content_categories WHERE name = ?',
                    [ $categoryName ]
                );

                if (!$rs) {
                    return false;
                }

                return $rs['title'];
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
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
     */
    public function getByName($categoryName)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAssoc(
                    'SELECT * FROM content_categories WHERE name = ?',
                    [ $categoryName ]
                );

                if (!$rs) {
                    return false;
                }

                $category = new \ContentCategory();
                $category->load($rs);

                return $category;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
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
     * Returns an array with subcetegories from a single category
     * with internal_name as index
     *
     * @param int $id the id of the parent category
     *
     * @return array list of ContentCategory objects
     */
    public function getAllSubcategories($id)
    {
        if (true|| is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAll(
                    'SELECT name,title,internal_category '
                    .'FROM content_categories WHERE internal_category<>0 '
                    .'AND inmenu=1 AND fk_content_category = ? ORDER BY posmenu',
                    [ $id ]
                );

                if (!$rs) {
                    return null;
                }

                $items = [];
                foreach ($rs as $row) {
                    $items[$row['name']]['title']             = $row['title'];
                    $items[$row['name']]['internal_category'] = $row['internal_category'];
                }

                return $items;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        // Singleton version
        $categories = $this->orderByPosmenu($this->categories);

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
     */
    public function orderByPosmenu($categories)
    {
        $categories = array_values($categories);

        if (count($categories) == 0) {
            return $categories;
        }

        usort($categories, function ($a, $b) {
            if ($b->inmenu == 0) {
                return 0;
            }

            if ($a->inmenu == 0) {
                return +1;
            }

            return ($a->posmenu > $b->posmenu) ? +1 : -1;
        });

        return $categories;
    }

    /**
     * Sorts an array of categories by its internal_category property
     *
     * @param array $categories the list of categories to sort
     *
     * @return array the sorted list of categories
     */
    public function groupByType($categories)
    {
        $categories = array_values($categories);

        if (count($categories) > 0) {
            return $categories;
        }

        usort($categories, function ($a, $b) {
            // Those that are not in the menu put them at the end of the list
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
     * Get a tree with categories and subcategories for menu
     *
     * TODO: To do work recursive for varios nested levels
     *
     * @return array Tree structure
     */
    public function getCategoriesTreeMenu()
    {
        $categories = $this->orderByPosmenu($this->categories);

        // Loop categories, and build the tree down
        $tree = [];
        foreach ($categories as $category) {
            if ($category->fk_content_category == 0
                && $category->internal_category == 1
                && $category->inmenu == 1
            ) {
                $tree[$category->pk_content_category] = $category;
                $tree[$category->pk_content_category]->childNodes = array();
            }
        }

        // Loop on subcategories, add them to the tree
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
     */
    public function getFirstSubcategory($categoryId)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAssoc(
                    'SELECT name FROM content_categories '
                    .'WHERE inmenu=1 AND fk_content_category=? AND internal_category<>0 '
                    .'ORDER BY posmenu LIMIT 1',
                    [ $categoryId ]
                );

                if (!$rs) {
                    return null;
                }

                return $rs['name'];
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        // Singleton version
        $categories = $this->orderByPosmenu($this->categories);

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
     */
    public function getFather($categoryName)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAll(
                    'SELECT content2.name '
                    .'FROM `content_categories` as content1, `content_categories` as content2 '
                    .'WHERE content1.name=? AND content1.fk_content_category=content2.pk_content_category',
                    [ $categoryName ]
                );

                if (!$rs) {
                    return null;
                }

                return $rs[0]['name'];
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        // Singleton version
        $fk_content_category = '';
        // Search fk_content_category
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
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
     */
    public function exists($categoryName)
    {
        if (is_null($this->categories)) {
            try {
                $rs = getService('dbal_connection')->fetchAssoc(
                    'SELECT count(*) AS total FROM content_categories WHERE name=?',
                    [ $categoryName ]
                );

                return intval($rs['total']) > 0;
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
            }
        }

        // Singleton version
        // searches within the interal categories array ($this->categories)
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
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
     */
    public function isEmpty($categoryName)
    {
        $pkCategory = $this->get_id($categoryName);

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT count(*) as content_count FROM `content_positions` WHERE `fk_category`=?',
                [ $pkCategory ]
            );

            $rs2 = getService('dbal_connection')->fetchAssoc(
                'SELECT count(pk_content) as content_count FROM `contents`, `contents_categories` '
                .'WHERE`contents_categories`.`pk_fk_content_category`=? '
                .'AND `contents`.`pk_content`=`contents_categories`.`pk_fk_content`',
                [ $pkCategory ]
            );

            return $rs['content_count'] == 0 && $rs2['content_count'] == 0;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns true if there is no contents in that category id
     *
     * @param int $category the category id
     *
     * @return boolean
     */
    public static function isEmptyByCategoryId($category)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT count(pk_content) AS number FROM `contents`, `contents_categories` '
                .'WHERE `fk_content_type`=1 '
                .'AND `in_litter`=0 '
                .'AND contents_categories.pk_fk_content_category=? '
                .'AND contents.pk_content=pk_fk_content',
                [ $category ]
            );

            return $rs['number'] == 0;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Counts the contents from a category
     *
     * @param int    $category the category id
     * @param string $type the group type where to search from
     *
     * @return array the counters for a category
     */
    public function countContentByType($category, $type)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT count(pk_content) AS number FROM `contents`,`contents_categories` '
                 .'WHERE contents.pk_content=pk_fk_content '
                 .'AND pk_fk_content_category=? AND `fk_content_type`=?',
                [ $category, $type ]
            );

            if (array_key_exists('number', $rs) && $rs['number']) {
                return $rs['number'];
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
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
     */
    public static function countContentsByGroupType($type, $filter = null)
    {
        $where= '';
        if (!is_null($filter)) {
            $where = ' AND '.$filter;
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT count(contents.pk_content) AS number,'
                .'`contents_categories`.`pk_fk_content_category` AS cat '
                .'FROM `contents`,`contents_categories` '
                .'WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content` '
                .'AND `in_litter`=0 AND `contents`.`fk_content_type`=? '
                .$where.' GROUP BY `contents_categories`.`pk_fk_content_category`',
                [ $type ]
            );

            $groups = [];
            foreach ($rs as $row) {
                $groups[$row['cat']] = $row['number'];
            }

            return $groups;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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
    public function getArraysMenu($category = null, $internalCategory = [1])
    {
        //fullcat contains array with all cats order by posmenu
        //parentCategories is an array with all menu cats in frontpage
        //subcat is an array with all subcat form the parentCategories array
        //$categoryData is the info of the category selected

        //$fullcat = $this->orderByPosmenu($this->categories);
        $fullcat = $this->groupByType($this->categories);

        $fullcat = getService('data.manager.filter')->set($fullcat)->filter('localize', [
            'keys' => \ContentCategory::getMultiLanguageFields(),
            'locale' => getService('core.locale')->setContext('frontend')->getLocale()
        ])->get();

        if (!is_array($internalCategory)) {
            $internalCategory = [$internalCategory];
        }

        $parentCategories = [];
        $categoryData     = [];
        foreach ($fullcat as $prima) {
            if (!empty($category)
                && $prima->pk_content_category == $category
                && $category != 'home'
                && $category != 'todos'
            ) {
                $categoryData[] = $prima;
            }

            if (($prima->internal_category == 1
                || in_array($prima->internal_category, $internalCategory))
                && ($prima->fk_content_category == 0)
            ) {
                $parentCategories[] = $prima;
            }
        }

        $subcat = [];
        foreach ($parentCategories as $k => $v) {
            $subcat[$k] = [];

            foreach ($fullcat as $child) {
                if ($v->pk_content_category == $child->fk_content_category) {
                    $subcat[$k][] = $child;
                }
            }
        }

        if (empty($category) && !empty($parentCategories)) {
             $categoryData[] = $parentCategories[0];
        }

        return [$parentCategories, $subcat, $categoryData];
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

        $items = [];
        foreach ($this->categories as $category) {
            if ($category->fk_content_category == $categoryId) {
                $items[] = $category;
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
    public function getCategoryNameByContentId($id)
    {
        if (!is_numeric($id)) {
            return null;
        }
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT catName FROM contents_categories WHERE pk_fk_content=?',
                [ $id ]
            );

            if (!$rs) {
                return null;
            }

            return $rs['catName'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Resets ContentCategoryManager.
     */
    public function reset()
    {
        $this->categories = [];
        getService('cache')->delete('content_categories');
    }
}
