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
class ContentCategoryManager {

    /**
     * @var array with categories
     */
    public $categories = null;

    /**
     * @var ContentCategoryManager instance, singleton pattern
     */
    static private $instance = null;

    /**
     * @var MethodCacheManager cache, object to cache requests
    */
    public $cache = null;

    function __construct() {
        if( is_null(self::$instance) ) {
            // Posibilidad de cachear resultados de métodos
            $this->cache = new MethodCacheManager($this, array('ttl' => 300));

            // Rellenar categorías dende caché
            $this->categories = $this->cache->populate_categories();

            self::$instance = $this;
            return self::$instance;
        } else {
           return self::$instance;
        }
    }

    static function get_instance() {
        if( is_null(self::$instance) ) {
            $instance = new ContentCategoryManager();

            self::$instance = $instance;
            return self::$instance;
        } else {
            return self::$instance;
        }

    }

    /**
     *  reload internal array $this->categories and
     *  delete APC cache
     *  call when change or create categories
     *
     * @return array Array with Content_category objects
    */
    function reloadCategories() {

        $this->categories = null;
        $method ='populate_categories';
        $args = array();
        $key = 'ContentCategoryManager'.$method.md5(serialize($args));
        if(defined('APC_PREFIX')) {
            $key = APC_PREFIX . $key;
        }

        $result = apc_delete($key);
        $result = call_user_func_array(array('ContentCategoryManager', $method), $args);
        apc_store($key, serialize($result), 300);

        return( $result );

    }

    /**
     * populate_categories, load internal array $this->categories for a
     * singleton instance
     *
     * @return array Array with Content_category objects
    */
    function populate_categories() {
        $sql = 'SELECT * FROM content_categories ORDER BY posmenu ASC';
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            // FIXME: controlar erros
            return('');
        }

        // clear content
        $this->categories = array();
        if($rs!=false) {
            while($obj = $rs->FetchNextObject($toupper=false)) {
                $this->categories[ $obj->pk_content_category ] = $obj;
            }
        }

        return( $this->categories );
    }

    /**
     * Normalize names of category and subcategory
     *
     * @param $category_name Name of category
     * @param $subcategory_name Name of subcategory
     * @return array Return category_name and subcategory_name fixed
    */
    function normalize($category_name, $subcategory_name=null) {
        if(!empty($subcategory_name)) {
            // It's a father category
            return array($category_name, $subcategory_name);
        }

        $father = $this->get_father($category_name);
        if(!empty($father)) {
            return( array($father, $category_name) );
        }

        // If don't match return same values
        return array($category_name, $subcategory_name);
    }

 /**
     * find objects of category and subcategory
     *
     * @param $filter - filter of sql
     * @param $order_by
     * @return array Return category objects
    */
    function find($filter=NULL, $_order_by='ORDER BY 1') {
        $items = array();
        $_where = '1=1';

        if( !is_null($filter) ) {
            $_where = $filter;
        }

        $sql = 'SELECT * FROM content_categories ' .
                'WHERE internal_category<>0 AND '.$_where.' '.$_order_by;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if($rs !== false) {
            while(!$rs->EOF) {
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
     * @param $fk_content_type type of elements category.
     * @param $filter - filter of sql
     * @param $order_by
     * @return array Return category objects
    */
    function find_by_type($fk_content_type, $filter=NULL, $_order_by='ORDER BY 1') {
        $_where = 'fk_content_type='. $fk_content_type .' ';
        return $this->find($_where);
    }

    //Devuelve el nombre de una categoria para los upload y posible las urls
    function get_name($id) {
        if( is_null($this->categories) ) {
            $sql = 'SELECT name FROM content_categories WHERE pk_content_category = ?';
            $rs = $GLOBALS['application']->conn->Execute( $sql, array($id) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }
            return $rs->fields['name'];
        }
        if(isset($this->categories[$id]->name)) {
            return($this->categories[$id]->name);
        } else {
            return('');
        }
    }


    /**
     * Returns the position in menu
     *
     * @param int $id Category ID
     * @return int Return posmenu
     */
    function get_pos($id) {
        if( is_null($this->categories) ) {
            if(is_numeric($id)) {
                $sql = 'SELECT posmenu FROM content_categories WHERE pk_content_category = ?';
                $rs = $GLOBALS['application']->conn->Execute( $sql, array($id) );

                if (!$rs) {
                    $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                    $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                    $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                    return;
                }
                return $rs->fields['posmenu'];
            }

            return 0;
        }

        if(!isset($this->categories[$id])) {
            return 0;
        }

        // Singleton version
        return $this->categories[$id]->posmenu;
    }

    //Returns cetegory id
    function get_id($category_name) {
        if( is_null($this->categories) ) {
            $sql = 'SELECT pk_content_category FROM content_categories WHERE name = ?';
            $rs  = $GLOBALS['application']->conn->Execute( $sql, array($category_name) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return NULL;
            }

            return $rs->fields['pk_content_category'];
        }

        // Singleton version
        foreach($this->categories as $category) {
            if($category->name == $category_name) {
                return( $category->pk_content_category );
            }
        }

        return(0);
    }

    /**
     * Return first category data in the menu of type
     *
     * @param int $category_type content_type.
     *
     * @return array with category data
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    function getFirstCategory($category_type) {

        if( is_null($this->categories) ) {
            $sql = 'SELECT * FROM content_categories WHERE '.
                   ' inmenu=1 AND internal_category = '.$category_type.
                   ' ORDER BY posmenu LIMIT 1';

            $rs = $GLOBALS['application']->conn->Execute( $sql, array($category_type) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            return $rs->fields;
        }

        // Singleton version
        $categories = $this->order_by_posmenu($this->categories);

        foreach($categories as $category) {
            if(($category->internal_category == $category_type) && ($category->inmenu==1) ) {

                return( $category->name );
            }
        }
    }

    //Returns the title of category
    function get_title($category_name) {
        if( is_null($this->categories) ) {
            $sql = 'SELECT title FROM content_categories WHERE name = ?';

            $rs = $GLOBALS['application']->conn->Execute( $sql, array($category_name) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            return $rs->fields['title'];
        }

        // Singleton version
        foreach($this->categories as $category) {
            if($category->name == $category_name) {
                return( $category->title );
            }
        }

        return('');
    }

    function getByName($category_name) {
        if( is_null($this->categories) ) {
            $sql = 'SELECT title FROM content_categories WHERE name = ?';

            $rs = $GLOBALS['application']->conn->Execute( $sql, array($category_name) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            return $rs->fields;
        }

        // Singleton version
        foreach($this->categories as $category) {
            if($category->name == $category_name) {
                return $category;
            }
        }

        return null;
    }

    //Returns an all cetegories array
    function get_all_categories() {
        if( is_null($this->categories) ) {
            $sql = 'SELECT name FROM content_categories';

            $rs = $GLOBALS['application']->conn->Execute( $sql );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            $items = array ();
            while(!$rs->EOF) {
                $str = $rs->fields['name'];
                $items[$str]=0;
                $rs->MoveNext();
            }

            return $items;
        }

        // Singleton version
        $items = array();
        foreach($this->categories as $category) {
            $items[$category->name] = 0;
        }

        return( $items );
    }

        //Returns an all cetegories array
    function get_all_categoriesID() {
        if( is_null($this->categories) ) {
            $sql = 'SELECT pk_content_category FROM content_categories WHERE internal_category=1';
            $rs = $GLOBALS['application']->conn->Execute( $sql );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }
            $items = array ();
            while(!$rs->EOF) {
                $str = $rs->fields['pk_content_category'];
                $items[$str]=$str;
                $rs->MoveNext();
            }

            return $items;
        }

        // Singleton version
        $items = array();
        foreach($this->categories as $category) {
            if( $category->internal_category == 1 ) {
                $items[$category->pk_content_category] = $category->pk_content_category;
            }
        }

        return( $items );
    }

    //Returns an array with subcetegories from a single category
    //with internal_name as index
    function get_all_subcategories($category_id) {
        if( is_null($this->categories) ) {
            $sql = 'SELECT name,title,internal_category FROM content_categories WHERE internal_category<>0 AND inmenu=1 AND
                fk_content_category = ? ORDER BY posmenu';

            $rs = $GLOBALS['application']->conn->Execute( $sql, array($category_id) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            $items = array ();
            while(!$rs->EOF) {
                $items[ $rs->fields['name'] ]['title']=$rs->fields['title'];
                $items[ $rs->fields['name'] ]['internal_category']=$rs->fields['title'];
                $rs->MoveNext();
            }
        }

        // Singleton version
        $categories = $this->order_by_posmenu($this->categories);

        $items = array ();
        foreach($categories as $category) {
            if( ($category->internal_category) && ($category->inmenu == 1)
                 && ($category->fk_content_category == $category_id) ) {
                    $items[ $category->name ]['title']=$category->title;
                    $items[ $category->name ]['internal_category']=$category->internal_category;

            }
        }

        return $items;
    }

    function order_by_posmenu($categories) {
        $categories = array_values($categories);

        // FIXME: create a lambda function once we upgrade to new version 5.3 of PHP
        if(!function_exists('__order_by_posmenu')) {
            // Ordenar
            function __order_by_posmenu($a, $b) {
                //Las que no están en el menú colocarlas al final
                if($b->inmenu == 0) {
                     return 0;
                }
                if($a->inmenu == 0) {
                     return +1;
                }
                if  ($a->posmenu == $b->posmenu) {

                }
                return ($a->posmenu > $b->posmenu) ? +1 : -1;

            }
        }
        usort($categories, '__order_by_posmenu');

        return( $categories );
    }

    function group_by_type($categories) {
        $categories = array_values($categories);

        // FIXME: create a lambda function once we upgrade to new version 5.3 of PHP
        if(!function_exists('__group_by_type')) {
            // Ordenar
            function __group_by_type($a, $b) {
                //Las que no están en el menú colocarlas al final
                if($b->internal_category == 0) {
                     return 0;
                }
                if($a->internal_category == 0) {
                     return +1;
                }
                if  ($a->internal_category == $b->internal_category) {
                    return ($a->posmenu > $b->posmenu) ? +1 : -1;
                }
                return ($a->internal_category < $b->internal_category) ? 1 : +1;

            }
        }
        usort($categories, '__group_by_type');

        return( $categories );
    }

    /**
     * Get a tree with categories and subcategories
     *
     * @todo To do work recursive for varios nested levels
     * @return array Tree structure
    */
    function getCategoriesTree() {
        $tree = array();

        $categories = $this->order_by_posmenu($this->categories);

        // First loop categories
        foreach($categories as $category) {
            if(($category->fk_content_category == 0) && ($category->internal_category != 0)) {
            //if(($category->fk_content_category == 0) && ($category->internal_category == 1)) {
                $tree[$category->pk_content_category] = $category;
                $tree[$category->pk_content_category]->childNodes = array();
            }
        }

        // Loop on subcategories
        foreach($categories as $category) {
            //if(($category->fk_content_category != 0) && ($category->internal_category == 1)) {
            if(($category->fk_content_category != 0) && ($category->internal_category != 0) &&
               (isset($tree[$category->fk_content_category]))) {

                $tree[$category->fk_content_category]->childNodes[$category->pk_content_category] = $category;
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
    function renderCategoriesTree() {
       // $this->getCategoriesTree();
        $categories = $this->getCategoriesTreeMenu();
        $i=0;
        $tree =array();
         foreach($categories as $category) {
            if(($category->fk_content_category == 0) && ($category->internal_category != 0) /*&& ($category->inmenu == 1) */ && ($category->pk_content_category != 4)) {
                $tree[$i] = new stdClass();
            //if(($category->fk_content_category == 0) && ($category->internal_category == 1)) {
                $tree[$i]->pk_content_category = $category->pk_content_category;
                $tree[$i]->title = ' '. $category->title;
                $i++;
                if(!empty($category->childNodes)){ //subcategorys
                    foreach($category->childNodes as $subcat){
                         $tree[$i] = new stdClass();
                         $tree[$i]->pk_content_category = $subcat->pk_content_category;
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
    function getCategoriesTreeMenu() {
        $tree = array();

        $categories = $this->order_by_posmenu($this->categories);

        // First loop categories
        foreach($categories as $category) {
            $category->params = unserialize($category->params);
            if(($category->fk_content_category == 0) && ($category->internal_category == 1) && ($category->inmenu == 1)) {
            //if(($category->fk_content_category == 0) && ($category->internal_category == 1)) {
                $tree[$category->pk_content_category] = $category;
                $tree[$category->pk_content_category]->childNodes = array();
            }
        }

        // Loop on subcategories
        foreach($categories as $category) {
            //if(($category->fk_content_category != 0) && ($category->internal_category == 1)) {
            if(($category->fk_content_category != 0) && ($category->internal_category != 0) &&
               (isset($tree[$category->fk_content_category])) /* && ($category->inmenu == 1)*/ ) {
                
                $tree[$category->fk_content_category]->childNodes[$category->pk_content_category] = $category;
            }
        }
        
        return $tree;
    }

    //Returns first subcategory from a father category_id
    function get_first_subcategory($category_id) {
        if( is_null($this->categories) ) {
            $sql = 'SELECT name FROM content_categories
                    WHERE inmenu=1 AND fk_content_category=? AND internal_category<>0
                    ORDER BY posmenu LIMIT 0,1';

            $rs = $GLOBALS['application']->conn->Execute( $sql, array($category_id) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            return $rs->fields['name'];
        }

        // Singleton version
        $categories = $this->order_by_posmenu($this->categories);

        foreach($categories as $category) {
            if(($category->fk_content_category==$category_id) && ($category->inmenu==1)
                && ($category->internal_category!=0) ) {
                return( $category->name );
            }
        }
    }

    function get_father($category_name){
        if( is_null($this->categories) ) {
            $sql='SELECT content2.name FROM `content_categories` as content1,`content_categories` as content2 '.
                 'WHERE content1.name=? and content1.fk_content_category=content2.pk_content_category';

            $rs = $GLOBALS['application']->conn->Execute( $sql, array($category_name) );
            if(!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }

            return $rs->fields['name'];
        }


        // Singleton version
        $fk_content_category = '';
        // Search fk_content_category
        foreach($this->categories as $category) {
            if($category->name == $category_name) {
                $fk_content_category = $category->fk_content_category;
                break;
            }
        }

        foreach($this->categories as $category) {
            if($category->pk_content_category == $fk_content_category) {
                return $category->name;
            }
        }

        // FIXME: if flow of code could arrive here then throw a exception
        return '';
    }

    //Returns false if the category does not exist
    function exists($category_name) {
   //     if( is_null($this->categories) ) {
            $sql = 'SELECT count(*) AS total FROM content_categories WHERE name = ?';
            $rs  = $GLOBALS['application']->conn->GetOne( $sql, $category_name );

            return( $rs || $rs > 0 );
      //  }

        // Singleton version
        // search into categories internal array ($this->categories)
        foreach($this->categories as $category) {
            if($category->name == $category_name) {
                return true;
            }
        }

        return false;
    }

    //Returns true if there is no contents in that category name
    function isEmpty($category) {
        $pk_category = $this->get_id($category);
        $sql1 = 'SELECT count( * )
                 FROM `content_positions`
                 WHERE `fk_category` ='.$pk_category;
        $rs1 = $GLOBALS['application']->conn->Execute( $sql1 );

        if (!$rs1) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }


        $sql = 'SELECT count(pk_content) AS number FROM `contents`, `contents_categories`
                WHERE `contents`.`fk_content_type`=1
                AND `contents`.`in_litter`=0
                AND `contents_categories`.`pk_fk_content_category`=?
                AND `contents`.`pk_content`=`contents_categories`.`pk_fk_content`';
        $rs = $GLOBALS['application']->conn->Execute( $sql, array($pk_category) );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }

        return( $rs->fields['number'] == 0 && $rs1->fields[0] == 0 );
    }

 //Returns true if there is no contents in that category id
    function is_Empty($category) {
        $sql1 = 'SELECT count(pk_content) AS number FROM `contents`, `contents_categories`
            WHERE `fk_content_type`=1 AND `in_litter`=0 AND contents_categories.pk_fk_content_category=? AND contents.pk_content=pk_fk_content';
        $rs1 = $GLOBALS['application']->conn->Execute( $sql1, array($category) );

        $sql2 = 'SELECT count(pk_content_category) AS number FROM `content_categories`
            WHERE content_categories.fk_content_category = ?';

        $rs2 = $GLOBALS['application']->conn->Execute( $sql2, array($category) );

        $number = $rs1->fields['number'] + $rs2->fields['number'];

        return( $number == 0 );
    }


    function count_content_by_type($category, $type) {
        $sql = 'SELECT count(pk_content) AS number FROM `contents`,`contents_categories` WHERE'.
            ' contents.pk_content=pk_fk_content AND pk_fk_content_category=? AND `fk_content_type`=?';
        $rs = $GLOBALS['application']->conn->Execute( $sql, array($category, $type) );

        if($rs->fields['number']) {
            return $rs->fields['number'];
        } else {
            return 0;
        }
    }

    /**
     *
     *
     * @see ContentCategoryManager::count_content_by_type
    */
    function count_content_by_type_group($type, $filter=NULL) {
         $_where = '1=1';
        if( !is_null($filter) ) {
            $_where = $filter;
        }
        $sql = 'SELECT count(contents.pk_content) AS number, `contents_categories`.`pk_fk_content_category` AS cat
                FROM `contents`,`contents_categories`
                WHERE `contents`.`pk_content`=`contents_categories`.`pk_fk_content` AND `in_litter`=0 AND `contents`.`fk_content_type`=? AND '.$_where.
                ' GROUP BY `contents_categories`.`pk_fk_content_category`';

        $rs = $GLOBALS['application']->conn->Execute( $sql, array($type) );

        $groups = array();

        if($rs!==false) {
            while(!$rs->EOF) {
                $groups[ $rs->fields['cat'] ] = $rs->fields['number'];
                $rs->MoveNext();
            }
        }

        return $groups;
    }

     function countMediaByTypeGroup($filter=NULL) {
         $_where = '1=1';
        if( !is_null($filter) ) {
            $_where = $filter;
        }
        $sql = 'SELECT count(photos.pk_photo) AS number, `contents_categories`.`pk_fk_content_category` AS cat
                FROM `contents_categories`,`photos`,`contents`
                WHERE `photos`.`pk_photo`=`contents`.`pk_content` AND `photos`.`pk_photo`=`contents_categories`.`pk_fk_content`  AND contents.`in_litter`=0 AND '.$_where.
                ' GROUP BY `contents_categories`.`pk_fk_content_category`';

        $rs = $GLOBALS['application']->conn->Execute( $sql );

        $groups = array();

        if($rs!==false) {
            while(!$rs->EOF) {
                $groups[ $rs->fields['cat'] ] = $rs->fields['number'];
                $rs->MoveNext();
            }
        }

        return $groups;
    }

    function data_media_by_type_group($filter=NULL) {
         $_where = '1=1';
        if( !is_null($filter) ) {
            $_where = $filter;
        }
        $sql = 'SELECT count(photos.pk_photo) AS number, `contents_categories`.`pk_fk_content_category` AS cat,
                sum(`photos`.`size`) as size FROM `contents_categories`,`photos`,`contents`
                WHERE `photos`.`pk_photo`=`contents`.`pk_content` AND `photos`.`pk_photo`=`contents_categories`.`pk_fk_content`  AND contents.`in_litter`=0 AND '.$_where.
                ' GROUP BY `contents_categories`.`pk_fk_content_category`';

        $rs = $GLOBALS['application']->conn->Execute( $sql );

        $groups = array();

        if($rs!==false) {
            while(!$rs->EOF) {
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
    function getArraysMenu($category=NULL, $internal_category = 1) {

        //fullcat contains array with all cats order by posmenu
        //parentCategories is an array with all menu cats in frontpage
        //subcat is an array with all subcat form the parentCategories array
        //$categoryData is the info of the category selected


       //$fullcat = $this->order_by_posmenu($this->categories);
        $fullcat = $this->group_by_type($this->categories);

        $parentCategories = array();
        $categoryData = array();
        foreach( $fullcat as $prima) {

            if (!empty($category) && ($prima->pk_content_category == $category) &&
                ($category !='home') && ( $category !='todos') ) {
                $categoryData[] = $prima;
            }
            if ((($prima->internal_category == 1)
                 || ($prima->internal_category == $internal_category))
                && ($prima->fk_content_category == 0) ) {

                $parentCategories[] = $prima;
            }
        }
        $subcat = array();
        foreach($parentCategories as $k => $v) {
            $subcat[$k] = array();

            foreach($fullcat as $child) {
                if($v->pk_content_category == $child->fk_content_category) {
                    $subcat[$k][] = $child;
                }
            }
        }


        if ( empty($category) && !empty($parentCategories) ) {
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
     * @return array  childs categorys
     *
     * @throws <b>Exception</b> Explanation of exception.
     */
    function getSubcategories($category_id) {
        if( is_null($this->categories) ) {
            $this->categories = $this->cache->populate_categories();
        }

        $items = array();
        foreach($this->categories as $category) {
            if( $category->fk_content_category == $category_id) {
                    $items[]=$category;
            }
        }

        return $items;
    }



    /**
     * Returns the category name of a Content throw ID
     *
     * @param int $id Content ID
     * @return int Return category name
     */
    function get_category_name_by_content_id($id) {
        if(is_numeric($id)) {
            $sql = 'SELECT catName FROM contents_categories WHERE pk_fk_content=?';
            $rs = $GLOBALS['application']->conn->Execute( $sql, array($id) );

            if (!$rs) {
                $error_msg = $GLOBALS['application']->conn->ErrorMsg();
                $GLOBALS['application']->logger->debug('Error: '.$error_msg);
                $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
                return;
            }
            return $rs->fields['catName'];
        }

        return NULL;
    }


}
