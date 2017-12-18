<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ContentCategoryManager
{
    /**
     * List of available categories.
     *
     * @var array
     */
    public $categories = null;

    /**
     * The instance of this class, use for the Singleton pattern.
     *
     * @var ContentCategoryManager
     */
    private static $instance = null;

    /**
     * Initializes the object class or returns the initialized instance if
     * it was previously created.
     */
    public function __construct()
    {
        if (is_null(self::$instance)) {
            // Fill categories from cache
            $this->categories = $this->findAll();

            self::$instance = $this;
        }

        return self::$instance;
    }

    /**
     * Counts the contents from a group type.
     *
     * @param string $type The group type where to search from.
     *
     * @return array The counters for all the group types.
     */
    public static function countContentsByGroupType($type)
    {
        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT count(contents.pk_content) AS number,'
                . '`contents_categories`.`pk_fk_content_category` AS cat '
                . 'FROM `contents`,`contents_categories` '
                . 'WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content` '
                . 'AND `in_litter`=0 AND `contents`.`fk_content_type`=? '
                . ' GROUP BY `contents_categories`.`pk_fk_content_category`',
                [ $type ]
            );

            $groups = [];
            foreach ($rs as $row) {
                $groups[$row['cat']] = $row['number'];
            }

            return $groups;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Counts the contents from a category.
     *
     * @param integer $category The category id.
     * @param string  $type The group type where to search from.
     *
     * @return array The counters for a category.
     */
    public function countContentByType($category, $type)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT count(pk_content) AS number FROM `contents`,`contents_categories` '
                . 'WHERE contents.pk_content=pk_fk_content '
                . 'AND pk_fk_content_category=? AND `fk_content_type`=?',
                [ $category, $type ]
            );

            if (array_key_exists('number', $rs) && $rs['number']) {
                return $rs['number'];
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
            return false;
        }
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
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find objects of category and subcategory.
     *
     * @param string $filter  SQL WHERE clause.
     * @param string $orderBy ORDER BY clause.
     *
     * @return array List of ContentCategory objects.
     */
    public function find($filter = null, $orderBy = '')
    {
        $items = [];

        $where = '';
        if (!is_null($filter)) {
            $where = ' AND ' . $filter;
        }

        try {
            $rs = getService('dbal_connection')->fetchAll(
                'SELECT * FROM content_categories ' .
                'WHERE internal_category<>0 ' . $where . ' ' . $orderBy
            );
            foreach ($rs as $row) {
                $obj = new ContentCategory();
                $obj->load($row);

                $items[] = $obj;
            }

            return $items;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
            return [];
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

        $cache      = getService('cache');
        $cacheKey   = 'content_categories';
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
            getService('error.log')->error($e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
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
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return $category->pk_content_category;
            }
        }

        return 0;
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
        }

        return self::$instance;
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

        $fullcat = $this->groupByType($this->categories);

        $fullcat = getService('data.manager.filter')->set($fullcat)->filter('localize', [
            'keys' => \ContentCategory::getL10nKeys(),
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

        return [ $parentCategories, $subcat, $categoryData ];
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

                $tree[$category->pk_content_category]->childNodes = [];
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
     * Returns the father of a category given its name
     *
     * @param string $category_name the category name
     *
     * @return string the parent category name
     */
    public function getFather($categoryName)
    {
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
     * Returns the category name given its id.
     *
     * @param integer $id The category id.
     *
     * @return mixed The category name if it exists. False otherwise.
     */
    public function getName($id)
    {
        if (array_key_exists($id, $this->categories)
            && !empty($this->categories[$id])
            && isset($this->categories[$id]->name)
        ) {
            return $this->categories[$id]->name;
        }

        return false;
    }

    /**
     * Returns a list of subcategories given the id of the parent category.
     *
     * @param integer $parent The parent id.
     *
     * @return array The list of subcategories.
     */
    public function getSubcategories($parent)
    {
        return array_filter($this->categories, function ($a) use ($parent) {
            return $a->fk_content_category == $parent;
        });
    }

    /**
     * Returns the title "Human readable name" of a category given its name.
     *
     * @param string $categoryName The category name.
     *
     * @return string The category title.
     */
    public function getTitle($categoryName)
    {
        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return $category->title;
            }
        }

        return '';
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
                . 'WHERE`contents_categories`.`pk_fk_content_category`=? '
                . 'AND `contents`.`pk_content`=`contents_categories`.`pk_fk_content`',
                [ $pkCategory ]
            );

            return $rs['content_count'] == 0 && $rs2['content_count'] == 0;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage() . ' Stack Trace: ' . $e->getTraceAsString());
            return false;
        }
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
     * Resets ContentCategoryManager.
     */
    public function reset()
    {
        $this->categories = [];

        getService('cache')->delete('content_categories');
    }

    /**
     * Sorts an array of categories by its internal_category property
     *
     * @param array $categories the list of categories to sort
     *
     * @return array the sorted list of categories
     */
    private function groupByType($categories)
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
}
