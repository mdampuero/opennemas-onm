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
    public $categories = [];

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
            if (!$this->categories) {
                $this->categories = [];
                getService('error.log')->error("an error has occurred in retrieving the BD categories");
            }

            self::$instance = $this;
        }

        return self::$instance;
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
     * Returns a category object given its id
     *
     * @param int $categoryID the Category ID
     *
     * @return ContentCategory|null the category if found
     **/
    public function findById($categoryId)
    {
        if (empty($categoryId)
            || !is_array($this->categories)
            || !array_key_exists($categoryId, $this->categories)
        ) {
            return null;
        }

        return $this->categories[$categoryId];
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
     * Returns the category name given its id.
     *
     * @param integer $id The category id.
     *
     * @return mixed The category name if it exists. False otherwise.
     */
    public function getName($id)
    {
        if (!empty($id)
            && array_key_exists($id, $this->categories)
            && !empty($this->categories[$id])
            && isset($this->categories[$id]->name)
        ) {
            return $this->categories[$id]->name;
        }

        return null;
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
        if (empty($categoryName)) {
            return '';
        }

        foreach ($this->categories as $category) {
            if ($category->name == $categoryName) {
                return $category->title;
            }
        }

        return '';
    }
}
