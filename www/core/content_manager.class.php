<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * ContentManager
 *
 * @todo Refactor this class for complaint to 0.8.2v
 * @package    Core
 * @subpackage Content
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content_manager.class.php 1 2010-04-13 11:17:42Z vifito $
 */
class ContentManager 
{
    public $content_type = null;
    public $table = null;
    public $pager = null;
    
    private $conn = null;
    public $cache = null;
    
    // TODO: to study implementation singleton for ContentManager?
    /* private static $instance = null */
    
    public function __construct($content_type=null)
    {
        $this->conn = Zend_Registry::get('conn');
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if(!is_null($content_type)) {
            $this->init($content_type);
        }
    }
    
    
    public function init($content_type)
    {
        $this->table = $this->pluralize( $content_type );
        $this->content_type = $content_type;
    }
    
    
    // Cargar los valores devueltos del sql en objetos.
    public function load_obj($rs,$content_type)
    {
        $items = array();
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $obj = new $content_type();
                $obj->load($rs->fields);
                
                $items[] = $obj;
                
                $rs->MoveNext();
            }
        }
        
        return $items;
    }
    
    
    public function find($content_type, $filter=null, $_order_by='ORDER BY 1', $fields='*')
    {
        $this->init($content_type);
        $items = array();
        
        // FIXME: rewrite this condition into a method
        if($filter != null) {
            $filter .= ' AND';
        } else {
            $filter = '';
        }
        
        $sql = 'SELECT '.$fields.' FROM `contents`, `'.$this->table.'` ' .
                'WHERE '.$filter.' `contents`.`pk_content`= `'.$this->table.'`.`pk_'.strtolower($content_type).'` '.$_order_by;
        
        $rs = $this->conn->Execute($sql);
        
        $items = $this->load_obj($rs, $content_type);
        
        return $items;
    }
    
    
    /**
     * Generate a unique slug
     *
     * @param string $title
     * @param int $excludeId
     * @return string
     */
    public function slugIt($title, $excludeId=null)
    {
        $filter = new Onm_Filter_Slug();
        $slugs  = $this->_getSlugs($excludeId);
        
        $slug = $filter->filter($title);
        
        if(in_array($slug, $slugs)) {            
            $i = 1;            
            $new = $slug . '-' . $i;
            
            while( in_array($new, $slugs) ) {
                $i++;
                $new = $slug . '-' . $i;
            }
            
            return $new;
        }
        
        return $slug;
    }
    
    
    /**
     * TODO: review performance
     *
     * @param int $excludeId    Exclude this pk_content
     * @return array    Array of existent slugs
     */
    private function _getSlugs($excludeId=null)
    {
        if(is_null($excludeId) || !is_numeric($excludeId)) {
            $excludeId = -1;
        }
        
        $sql   = 'SELECT `slug` FROM `contents` WHERE `pk_content` <> ' . $excludeId;
        $slugs = $this->conn->GetCol($sql);
        
        return $slugs;
    }
    
    /**
     * Search for a content
     * 
     * <code>
     * $q = array(
     *     'text' => 'text to search',
     *     'category' => 1, // pk_category
     *     'content_type' => 1, //pk_content_type
     * );
     *
     * $options = array(
     *    'select' => 'pk_content, title',
     *    'from'   => 'articles',
     * );
     * </code>
     * @param array $q
     * @param array $options
     * @return string
     */
    public function search($q, $options=array())
    {
        $select = array();
        if(isset($options['select'])) {
            $select = $options['select'];
        } else {
            $select = array('`contents`.*');
        }
        
        $from = array();
        $from[] = '`contents`';
        
        $where = array();                
        
        if(!empty($q['category'])) {
            $from[] = '`contents_categories`';
            $where[] = '`pk_fk_content`=`pk_content` AND `pk_fk_category` = ' . $q['category'];
        }
        
        if(!empty($q['content_type'])) {
            $where[] = '`fk_content_type` = ' . $q['content_type'];
        }
        
        if(!empty($q['status'])) {
            $where[] = '`status` LIKE "' . $q['status'] . '"';
        }        
        
        $q['text'] = trim($q['text']);
        if(!empty($q['text']) && strlen($q['text']) > 2) {
            $match = 'MATCH (title,keywords,description) AGAINST (' . $this->conn->Quote($q['text']) .
                        ' IN BOOLEAN MODE)';
                        
            $where[] = $match;
            $select[] = $match . ' AS score';
        }
        
        $sql = 'SELECT ' . implode(', ', $select) .
               ' FROM '  . implode(', ', $from);
        
        if(count($where) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        
        // TODO: implement more search options
        $sql .= ' ORDER BY created DESC ' .
                ' LIMIT 0, 20 ';
        
        $rs = $this->conn->Execute($sql);
        
        $result = array();
        if($rs !== false) {            
            while(!$rs->EOF) {
                $result[] = $rs->GetRowAssoc(false);
                
                $rs->MoveNext();
            }
        }
        
        return $result;
    }
    
    
    /**
     * Para principal
     * @deprecated
     */
    public function findInGrid($positions, $category=null)
    {
        $where = 'content_status=1 AND available=1';
        
        if(!is_null($category)) {
            $sql = 'SELECT * FROM contents_categories, contents ' .
                   'WHERE '.$where.' AND frontpage=1 AND `contents_categories`.`pk_fk_content_category`=' . $category .
                   ' AND placeholder IN ("' . implode('","', $positions) . '") ' .
                   ' AND `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ' .
                   'ORDER BY placeholder, position';
        } else {
            $sql = 'SELECT pk_content FROM `contents` ' .
                   'WHERE ' . $where . ' AND in_home=1 ' .
                   ' AND home_placeholder IN ("' . implode('","', $positions) . '") ' .
                   ' ORDER BY home_placeholder, home_pos ASC';
        }
        
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $contents = array();
        
        if($rs !== false) {
            while(!$rs->EOF) {
                $o = Content::get($rs->fields['pk_content']);
                $contents[] = $o;
                
                $rs->MoveNext();
            }
        }
        
        return $contents;
    }
    
    
    public function find_all($content_type, $filter=null, $_order_by='ORDER BY 1', $fields='*')
    {
        $this->init($content_type);
        $items = array();
        
        $_where = '`contents`.`in_litter`=0';
        
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') { //se busca desde la litter.php
                $_where = $filter;
            } else{
                $_where = ' `contents`.`in_litter`=0 AND '.$filter;
            }
        }
        
        $sql = 'SELECT '.$fields.' FROM `contents`, `'.$this->table.'`, `contents_categories` ' .
                ' WHERE '.$_where.' AND `contents`.`pk_content`= `'.$this->table.'`.`pk_'.strtolower($content_type).'` '.
                ' AND `contents`.`pk_content`= `contents_categories`.`pk_fk_content` '.$_order_by;
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->load_obj($rs, $content_type);
        
        return $items;
    }
    
    
    /**
     * This function returns an array of objects $content_type of the most viewed in the last few days indicated.
     * @param string $content_type type of content
     * @param boolean $not_empty If there are no results regarding the days indicated, the query is performed on the entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $author pk_author of the contnet. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $days Interval of days on which the consultation takes place. For default is 2.
     * @param integer $num Number of objects that the function returns. For default is 8.
     * @param boolean $all Get all the content regardless of content status.
     * @return array of objects $content_type
     */
    public function getMostViewedContent($content_type, $not_empty = false, $category = 0, $author=0, $days=2, $num=6, $all=false)
    {
        $this->init($content_type);
        
        $items = array();
        $_tables = '`contents`, `'.$this->table.'` ';
        $_where = '`contents`.`in_litter`=0 ';
        if(!$all) {
            $_where .= 'AND `contents`.`content_status`=1 AND `contents`.`available`=1 ';
        }
        $_days = 'AND  `contents`.`changed`>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_order_by = 'ORDER BY `contents`.`views` DESC LIMIT 0 , '.$num;
        
        if( intval($category) > 0 ) {
            $_category = 'AND pk_fk_content_category='.$category.'  AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ';
            $_tables .= ', `contents_categories` ';
        } else {
            $_category = '';
        }
        
        if( intval($author) > 0 ) {
            if($content_type=='Opinion') {
                $_author = 'AND `opinions`.`fk_author`='.$author.' ';
            } else {
                $_author = 'AND `opinions`.`fk_author`='.$author.' ';
            }
        }
        
        $sql = 'SELECT * FROM '.$_tables .
                'WHERE '.$_where.$_category.$_author.$_days.' AND `contents`.`pk_content`=`pk_'.strtolower($content_type).'` '.
                $_order_by;
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        if($rs->_numOfRows<6 && $not_empty) {
            $sql = 'SELECT * FROM '.$_tables .
                    'WHERE '.$_where.$_category.$_author.' AND `contents`.`pk_content`=`pk_'.strtolower($content_type).'` '.
                    ' '.$_order_by;
            $rs = $GLOBALS['application']->conn->Execute($sql);
        }
        
        $items = $this->load_obj($rs, $content_type);
        
        return $this->getInTime($items);
    }
    
    
    /**
     * This function returns an array of objects $content_type of the most commented in the last few days indicated.
     * @param string $content_type type of content
     * @param boolean $not_empty If there are no results regarding the days indicated, the query is performed on the entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $days Interval of days on which the consultation takes place. For default is 2.
     * @param integer $num Number of objects that the function returns. For default is 8.
     * @param boolean $all Get all the content regardless of content status and endtime.
     * @return array
     */
    public function getMostComentedContent($content_type, $not_empty = false, $category = 0, $days=2, $num=6, $all=false)
    {
        $this->init($content_type);
        $items = array();
        
        $_where_slave = '';
        $_days = 'changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        if(!$all) {
            $_where_slave = ' content_status=1 AND available=1 ';
            $_days = 'AND changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        }
        
        $_comented = 'AND pk_content IN (SELECT DISTINCT(fk_content) FROM comments) ';
        $_limit = 'LIMIT 0 , '.$num;
        
        if (intval($category)>0) {
        
            $pks = $this->find_by_category($content_type, $category,$_where_slave.$_days.$_comented);
            if(!$all) {
                $pks = $this->getInTime($pks);
            }
            
            if(count($pks)<6 && $not_empty) {
                //En caso de que existan menos de 6 contenidos, lo hace referente a los 200 últimos contenidos
                $pks = $this->find_by_category($content_type, $category,$_where_slave.$_comented,
                                'ORDER BY changed DESC LIMIT 0,200','pk_content, starttime, endtime');
                if(!$all) {
                    $pks = $this->getInTime($pks);
                }
            }
        } else {
            $pks = $this->find($content_type,$_where_slave.$_days.$_comented,null,'pk_content, starttime, endtime');
            if(!$all) {
                $pks = $this->getInTime($pks);
            }
            
            if(count($pks)<6 && $not_empty) {
                //En caso de que existan menos de 6 contenidos, lo hace referente a los 200 últimos contenidos
                $pks = $this->getInTime($this->find($content_type,$_where_slave.$_comented,
                                'ORDER BY changed DESC LIMIT 0,200','pk_content, starttime, endtime'));
                if(!$all) {
                    $pks = $this->getInTime($pks);
                }
            }
        }
        
        $pk_list = '';
        foreach ($pks as $pk) {
            $pk_list .= ' '.$pk->id.',';
        }
        if (strlen($pk_list)==0) {
            return array();
        }
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);
        
        $comments = $this->find('Comment','available=1 AND fk_content IN ('.$pk_list.')',
                            ' GROUP BY fk_content ORDER BY num DESC LIMIT 0 , 8',
                            ' fk_content, count(pk_comment) AS num');
        
        $pk_list = '';
        foreach ($comments as $comment) {
            $pk_list .= ' '.$comment->fk_content.',';
        }
        
        if (strlen($pk_list)==0) {
            return array();
        }
        
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);
        
        $items = $this->find($content_type,'pk_content IN('.$pk_list.')',null,'`contents`.`pk_content`, `contents`.`title`, `contents`.`permalink`');
        if (empty($items)) {
            return array();
        }
        
        foreach($items as $item) {
            $articles[$item->pk_content] = array('pk_content'=>$item->pk_content,'num'=>0,'title'=>$item->title,'permalink'=>$item->permalink);
        }
        
        foreach($comments as $comment) {
            if (array_key_exists($comment->fk_content, $articles)) {
                $articles[$comment->fk_content]['num'] = $comment->num;
            }
        }
        
        function cmp($a, $b) {
            if ($a['num'] == $b['num']) {
                return 0;
            }
            return ($a['num'] > $b['num']) ? -1 : 1;
        }
        
        uasort($articles,'cmp');
        
        return $articles;
    }
    
    
    /**
     * This function returns an array of objects $content_type of the most voted in the last few days indicated.
     * Objects only have covered the fields pk_content, title, and total_value total_votes
     * @param string $content_type type of content
     * @param boolean $not_empty If there are no results regarding the days indicated, the query is performed on the entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $author pk_author of the contnet. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $days Interval of days on which the consultation takes place. For default is 2.
     * @param integer $num Number of objects that the function returns. For default is 8.
     * @param boolean $all Get all the content regardless of content status.
     * @return array of objects $content_type
     */
    public function getMostVotedContent($content_type, $not_empty = false, $category = 0, $author=0, $days=2, $num=8, $all=false)
    {
        $this->init($content_type);
        $items = array();
        
        $_tables = '`contents`, `' . $this->table . '`, `ratings` ';
        $_fields = '`contents`.pk_content, `contents`.title, `contents`.permalink, `ratings`.total_votes, `ratings`.total_value ';
        $_where = '`contents`.in_litter=0 ';
        if(!$all) {
            $_where .= 'AND `contents`.`content_status`=1 AND `contents`.`available`=1 ';
        }
        
        $_days = 'AND  `contents`.changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_tables_relations = ' AND `contents`.pk_content=`' . $this->table . '`.pk_' . strtolower($content_type) .
                             ' AND `ratings`.pk_rating=`contents`.pk_content ';
        $_order_by = 'ORDER BY `ratings`.total_votes DESC ';
        $_limit = 'LIMIT 0 , '.$num;
        
        if( isset($author) && !is_null($author) && intval($author) > 0 ) {
            if($content_type=='Opinion') {
                $_where .= 'AND `opinions`.fk_author='.$author.' ';
            } else {
                $_where .= 'AND `contents`.fk_author='.$author.' ';
            }
        }
        
        if (intval($category)>0) {
            $_tables .= ', `contents_categories` ';
            $_tables_relations .= ' AND  `contents_categories`.pk_fk_content = `contents`.pk_content ' .
                                  'AND `contents_categories`.pk_fk_content_category=' . $category . ' ';
        }
        
        $sql = 'SELECT ' . $_fields . ' FROM ' . $_tables . ' WHERE ' . $_where . $_days . $_tables_relations . $_order_by . $_limit;
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        if($rs->_numOfRows<=($num-3) && $not_empty) {
            $sql = 'SELECT ' . $_fields . ' FROM ' . $_tables . ' WHERE ' . $_where . $_tables_relations . $_order_by . $_limit;
            $rs = $GLOBALS['application']->conn->Execute($sql);
        }
        
        $items = $this->load_obj($rs, $content_type);
        return $items;
    }
 
/*****************************************************************************/
     /**
     * This function returns an array of objects all types of the most viewed in the last few days indicated.
     * @param boolean $not_empty If there are no results regarding the days indicated, the query is performed on the entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $days Interval of days on which the consultation takes place. For default is 2.
     * @param integer $num Number of objects that the function returns. For default is 8.
     * @param boolean $all Get all the content regardless of content status.
     * @return array of objects
     */
    function getAllMostViewed( $not_empty = false, $category = 0,  $days=2, $num=6, $all=false) {
        $this->init($content_type);


        $items = array();
        $_tables = '`contents`  ';
        $_where = '`contents`.`in_litter`=0 AND `fk_content_type` IN (1,3,4,7,9,11) ';
        if(!$all) {
            $_where .= 'AND `contents`.`content_status`=1 AND `contents`.`available`=1 ';
        }
        $_days = 'AND  `contents`.`changed`>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_order_by = 'ORDER BY `contents`.`views` DESC LIMIT 0 , '.$num;

        if( intval($category) > 0 ) {
            $_category = 'AND pk_fk_content_category='.$category.'  AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ';
            $_tables .= ', `contents_categories` ';
        } else {
            $_category = '';
        }



        $sql = 'SELECT * FROM '.$_tables .
                'WHERE '.$_where.$_category.$_days.
                $_order_by;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if($rs->_numOfRows<$num && $not_empty) {
            while($rs->_numOfRows<$num && $days<30){
                $_days = 'AND  `contents`.`changed`>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';

                $sql = 'SELECT * FROM '.$_tables .
                        'WHERE '.$_where.$_category. $_days.
                        ' '.$_order_by;
                $rs = $GLOBALS['application']->conn->Execute($sql);
                $days+=1;
            }  
            
        }


        $items = $this->load_obj($rs, 'content');

        return $this->getInTime($items);

    }

     /**
     * This function returns an array of objects all types of the most voted in the last few days indicated.
     * Objects only have covered the fields pk_content, title, and total_value total_votes
     * @param boolean $not_empty If there are no results regarding the days indicated, the query is performed on the entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $days Interval of days on which the consultation takes place. For default is 2.
     * @param integer $num Number of objects that the function returns. For default is 8.
     * @param boolean $all Get all the content regardless of content status.
     * @return array of objects
     */
    public function getAllMostVoted($not_empty = false, $category = 0, $days=2, $num=6, $all=false) {

        $items = array();

        $_tables = '`contents`, `ratings` ';
      //  $_fields = '`contents`.pk_content, `contents`.title, `contents`.permalink, `ratings`.total_votes, `ratings`.total_value ';
        $_fields = '*';
        $_where = '`contents`.in_litter=0 ';
        if(!$all) {
            $_where .= 'AND `contents`.`content_status`=1 AND `contents`.`available`=1 ';
        }
        $_days = 'AND  `contents`.changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        $_tables_relations = ' AND `ratings`.pk_rating=`contents`.pk_content ';
        $_order_by = 'ORDER BY `ratings`.total_votes DESC ';
        $_limit = 'LIMIT 0 , '.$num;

        if (intval($category)>0) {
            $_tables .= ', `contents_categories` ';
            $_tables_relations .= ' AND  `contents_categories`.pk_fk_content = `contents`.pk_content ' .
                                  'AND `contents_categories`.pk_fk_content_category=' . $category . ' ';
        }

        $sql = 'SELECT ' . $_fields . ' FROM ' . $_tables . ' WHERE ' . $_where . $_days . $_tables_relations . $_order_by . $_limit;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if($rs->_numOfRows<=$num && $not_empty) {
            while($rs->_numOfRows<$num && $days<30){
                  $days+=2;

                $sql = 'SELECT ' . $_fields . ' FROM ' . $_tables . ' WHERE ' . $_where . $_tables_relations . $_order_by . $_limit;
                $rs = $GLOBALS['application']->conn->Execute($sql);
            }
        }

        $items = $this->load_obj($rs, 'content');
   
        return $items;
    }

   /**
     * This function returns an array of objects $content_type of the most commented in the last few days indicated.
     * @param string $content_type type of content
     * @param boolean $not_empty If there are no results regarding the days indicated, the query is performed on the entire bd. For default is false
     * @param integer $category pk_content_category ok the contents. If value is 0, then does not filter by categories. For default is 0.
     * @param integer $days Interval of days on which the consultation takes place. For default is 2.
     * @param integer $num Number of objects that the function returns. For default is 8.
     * @param boolean $all Get all the content regardless of content status and endtime.
     * @return array
     */
    function getAllMostComented($not_empty = false, $category = 0, $days=2, $num=6, $all=false) {

        $items = array();

        $_where_slave = '';
        $_days = 'changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        if(!$all) {
            $_where_slave = ' content_status=1 AND available=1 ';
            $_days = 'AND changed>=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY) ';
        }

        $_comented = 'AND pk_content IN (SELECT DISTINCT(fk_content) FROM comments) ';
        $_limit = 'LIMIT 0 , '.$num;
        $_order_by = 'ORDER BY changed DESC';

        $_where=$_where_slave.$_days.$_comented;
        if (intval($category)>0) {
           
            // $pks = $this->find_by_category($content_type, $category,$_where_slave.$_days.$_comented);
            $sql = 'SELECT * FROM contents_categories, contents '.
               'WHERE '.$_where.' AND `contents_categories`.`pk_fk_content_category`=' .$pk_fk_content_category.
               '` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ' . $_order_by;
        } else {
             $sql = 'SELECT * FROM   contents '.
               'WHERE '.$_where.'  ' . $_order_by;
        }
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $pks = $this->load_obj($rs, 'content');
       
        if(!$all) {
                $pks = $this->getInTime($pks);
        }

        if(count($pks)<6 && $not_empty) {
            //En caso de que existan menos de 6 contenidos, lo hace referente a los 200 últimos contenidos
           /*  $pks = $this->getInTime($this->find($content_type,$_where_slave.$_comented,
                            'ORDER BY changed DESC LIMIT 0,200','pk_content, starttime, endtime')); */
            $sql = 'SELECT * FROM   contents '.
               'WHERE '.$_where_slave.$_comented.'  ' . $_order_by;
            $rs = $GLOBALS['application']->conn->Execute($sql);
            $pks = $this->load_obj($rs, 'content');
            $pks = $this->getInTime($pks);

            if(!$all) {
                $pks = $this->getInTime($pks);
            }
        }


        $pk_list = '';
        foreach ($pks as $pk) {
            $pk_list .= ' '.$pk->id.',';
        }
        if (strlen($pk_list)==0) {
            return array();
        }
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);
        $sql = 'SELECT fk_content, count(pk_comment) AS num FROM   contents, comments '.
               'WHERE available=1 AND fk_content IN ('.$pk_list.') GROUP BY fk_content ORDER BY num DESC LIMIT 0 , 8';

     /*   $comments = $this->find('Comment','available=1 AND fk_content IN ('.$pk_list.')',
                            ' GROUP BY fk_content ORDER BY num DESC LIMIT 0 , 8',
                            ' fk_content, count(pk_comment) AS num');
       */
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $comments = $this->load_obj($rs, 'Comment');
 
        $pk_list = '';
        foreach ($comments as $comment) {
            $pk_list .= ' '.$comment->fk_content.',';
        }
        if (strlen($pk_list)==0) {
            return array();
        }
        $pk_list = substr($pk_list, 0, strlen($pk_list)-1);

       // $items = $this->find($content_type,'pk_content IN('.$pk_list.')',null,'`contents`.`pk_content`, `contents`.`title`, `contents`.`permalink`');
        $sql = 'SELECT `contents`.`pk_content`, `contents`.`title`, `contents`.`permalink` FROM   contents, comments WHERE available=1 AND pk_content IN('.$pk_list.')';
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $this->load_obj($rs, 'content');
        if (empty($items)) {
            return array();
        }
        foreach($items as $item) {
            $articles[$item->pk_content] = array('pk_content'=>$item->pk_content,'num'=>0,'title'=>$item->title,'permalink'=>$item->permalink);
        }
        foreach($comments as $comment) {
            if (array_key_exists($comment->fk_content, $articles)) {
                $articles[$comment->fk_content]['num'] = $comment->num;
            }
        }

        function cmp($a, $b) {
            if ($a['num'] == $b['num']) {
                return 0;
            }
            return ($a['num'] > $b['num']) ? -1 : 1;
        }
        uasort($articles,'cmp');

        return $articles;
    }


    /**********************************************************************************************/
    /**
     * Filter content objects by starttime and endtime
     *
     * @see Content::isInTime()
     * @param array $items Array of Content objects
     * @param string $time Time filter, by default is now. Syntax: 'YYYY-MM-DD HH:MM:SS'
     * @return array Items filtered
    */
    public function getInTime($items, $time=null)
    {
        $filtered = array();
        if(is_array($items)) {
            foreach($items as $item) {
                if(is_object($item)) {
                    if($item->isInTime(null, null, $time)) {
                        $filtered[] = $item;
                    } /* elseif($item->isObsolete()) {
                        // If content  is obsolete then set available a 0
                        $item->set_available(0, $_SESSION['userid']);
                    } */
                } else {
                    if(Content::isInTime($item['starttime'], $item['endtime'], $time)) {
                        $filtered[] = $item;
                    }
                }
            }
        }
        
        return $filtered;
    }
    
    
     /**
     * Filter content objects by  available and not inlitter.
     * @param array $items Array of Content objects    
     * @return array Items filtered
    */
    public function getAvailable($items)
    {
        $filtered = array();
        if(is_array($items)) {
            foreach($items as $item) {
                if(is_object($item)) {
                    if(($item->available==1) && ($item->in_litter==0)) {
                        $filtered[] = $item;
                    } 
                } else {
                    if(($item['available']==1) && ($item['in_litter']==0)) {
                        $filtered[] = $item;
                    }
                }
            }
        }
        
        return $filtered;
    }
    
    
    /**
     * Return a SQL condition to filter by time
     *
     * @param string $time Time to test
     * @return string SQL condition
    */    
    public function getInTimeSQL($time=null)
    {
        $now = 'NOW()';
        if(!is_null($time)) {
            $now = $time;
        }
        
        $sql = " (`contents`.`starttime` > $now OR `contents`.`starttime` = '000-00-00 00:00:00') AND ".
               "(`contents`.`endtime` < $now OR `contents`.`endtime` = '000-00-00 00:00:00') ";
        
        return $sql;
    }
    
    
    /**
     * Get elements of a placeholder
     *
     * @param string $placeholder
     * @param array $items
     * @param boolean $isHomePlaceholder
     * @return array
    */
    public function getElementsByPlaceHolder($placeholder, $items, $isHomePlaceholder=false)
    {
        $filtered = array();
        
        $property = 'placeholder';
        if($isHomePlaceholder) {
            $property = 'home_placeholder';
        }
        
        foreach($items as $item) {
            if($item->{$property} == $placeholder) {
                $filtered[] = $item;
            }
        }
        
        return $filtered;
    }
    
    
    /**
     * Group elements by a placeholder
     *
     * @param array $items
     * @param boolean $isHomePlaceholder
     * @return array
    */
    public function groupByPlaceHolder($items, $isHomePlaceholder=false)
    {
        $placeholders = array();
        
        $property = 'placeholder';
        if($isHomePlaceholder) {
            $property = 'home_placeholder';
        }
        foreach($items as $item) {
            if($item->{$property} == $placeholder) {
                $filtered[] = $item;
            }
        }
        
        return $filtered;
    }
    
    
    /**
     * Count: Contanbiliza el numero de elementos de un tipo.
     */
    public function count($content_type, $filter=null, $pk_fk_content_category=null)
    {
        $this->init($content_type);
        $items = array();
        $_where = 'in_litter=0';
        
        if( !is_null($filter) ) {
            if(($filter == ' `contents`.`in_litter`=1')|| ($filter == 'in_litter=1')) { //se busca desde la litter.php
                  $_where = $filter;
            } else{
                $_where = ' `contents`.`in_litter`=0 AND '.$filter;
            }
        }
        
        if( intval($pk_fk_content_category) != null) {
            $sql = 'SELECT COUNT(contents.pk_content) FROM `contents_categories`, `contents`, ' . $this->table . '  ' .
                   ' WHERE '.$_where.' AND `contents_categories`.`pk_fk_content_category`='.$pk_fk_content_category .
                   '  AND pk_content=`'.$this->table.'`.`pk_'.strtolower($content_type) .
                   '` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ';
                
        } else {
           $sql = 'SELECT COUNT(contents.pk_content) AS total FROM `contents`, `'.$this->table.'` ' .
                  'WHERE '.$_where.' AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.strtolower($content_type).'` ';
        }
        
        $rs = $this->conn->GetOne($sql);
        
        return $rs;
    }
    
    
    /**
     * find_pages: Se utiliza para generar los listados en la parte de administracion.
     * Genera las consultas de find o find_by_category y la paginacion
     * Devuelve el array con el segmento de contents que se visualizan en la pagina dada.
     * 
     * <code>
     * ContentManager::find_pages($content_type, $filter=null, $_order_by='ORDER BY 1',
     *                            $page=1, $items_page=10, $pk_fk_content_category=null);
     * </code>
     * 
     * @param int $content_type     Tipo contenido.
     * @param string|null $filter   Condiciones para clausula where.
     * @param string $_order_by     Orden de visualizacion
     * @param int $page             Página que se quiere visualizar.
     * @param int $items_page       Número de elementos por pagina.
     * @param int|null $pk_fk_content_category Id de categoria (para find_by_category y si null es find).
     * @return array                Array ($items, $pager)
     */
    public function find_pages($content_type, $filter=null, $_order_by='ORDER BY 1', $page=1, $items_page=10,$pk_fk_content_category=null )
    {
        $this->init($content_type);
        $items = array();
        $_where = '`contents`.`in_litter`=0';
        
        if( !is_null($filter) ) {
            if(( $filter == ' `contents`.`in_litter`=1') || ($filter == 'in_litter=1')){ //se busca desde la litter.php
                $_where = $filter;
            } else {
                $_where = ' `contents`.`in_litter`=0 AND '.$filter;
            }
        }
        $total_contents=$this->count($content_type, $filter, $pk_fk_content_category);
        if(empty($page)) {
            $page = 1;
        }
        if(empty($page)) {
            $items_page=10;
        }
        $_limit='LIMIT '.($page-1)*$items_page.', '.($items_page);
        
   
        if( intval($pk_fk_content_category) != null) {
            $sql = 'SELECT * FROM contents_categories, contents, '.$this->table.'  ' .
                ' WHERE '.$_where.' AND `contents_categories`.`pk_fk_content_category`='.$pk_fk_content_category.
                '  AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.strtolower($content_type).'` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '.
                 $_order_by.$_limit;
        } else {       
            $sql = 'SELECT * FROM `contents`, `'.$this->table.'` ' .
                    ' WHERE '.$_where.' AND `contents`.`pk_content`=`'.$this->table.'`.`pk_'.strtolower($content_type).'` '.$_order_by.$_limit;
        }
        
        $rs = $this->conn->Execute($sql); 
        
        $items = $this->load_obj($rs, $content_type);
        
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => $total_contents,
        );
        $pager = Pager::factory($pager_options);
        
        return array($items, $pager);
    }
    
    
    public function find_by_category($content_type, $pk_fk_content_category, $filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type);
        
        $items = array();
        $_where = 'AND in_litter=0';
        
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') { //se busca desde la litter.php
                $_where = $filter;
            } else {
                $_where = ' in_litter=0 AND '.$filter;
            }
        }
        
        if( intval($pk_fk_content_category) > 0 ) {
            $sql = 'SELECT * FROM contents_categories, contents, '.$this->table.'  ' .
                   'WHERE '.$_where.' AND `contents_categories`.`pk_fk_content_category`=' .
                   $pk_fk_content_category.'  AND `contents`.`pk_content`=`' . $this->table . '`.`pk_'.strtolower($content_type) .
                   '` AND  `contents_categories`.`pk_fk_content` = `contents`.`pk_content` ' . $_order_by;
        } else {
            return( $items );
        }
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        $items=$this->load_obj($rs,$content_type);
        
        return $items;
    }
    
    
    public function find_by_category_name($content_type, $category_name, $filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type); //recupera el id de la categoria del array.
        $pk_fk_content_category=$this->get_id($category_name);
        $items = array();
        $_where = 'in_litter=0';
        
        if( !is_null($filter) ) {
            if( preg_match('/in_litter=1/i', $filter) ) { //se busca desde la litter.php
                $_where = $filter;
            } else {
                $_where = $filter.' AND in_litter=0';
            }
        }
        
        $sql = 'SELECT * FROM contents_categories, contents,  '.$this->table.'  ' .
                'WHERE '.$_where.' AND `contents_categories`.`pk_fk_content_category`='.$pk_fk_content_category .
                '  AND `contents`.`pk_content`= `'.$this->table.'`.`pk_'.strtolower($content_type) .
                '` AND `contents_categories`.`pk_fk_content` = `contents`.`pk_content` '.$_order_by;
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        $items = $this->load_obj($rs,$content_type);      
        
        return $items;
    }
    
    
    //this function returns last contents of Subcategories of a given category
    public function find_inSubcategory_by_categoryName($content_type, $category_name, $filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type);
        $items = array();
        $_where = '1=1  AND in_litter=0';
        
        if( !is_null($filter) )
        {
            if( $filter == 'in_litter=1') {
                //se busca desde la litter.php
                $_where = $filter;
            }
            
            $_where = $filter.' AND in_litter=0';
        }
        
        $sql= 'SELECT contents.pk_content FROM contents,content_categories, contents_categories '. $_where.
              ' AND content_categories.fk_content_category=\''.$this->get_id($category_name).'\''.
              ' WHERE content_categories.pk_content_category=contents_categories.pk_fk_content_category '.
              ' AND contents.pk_content = contents_categories.pk_fk_content '. $_order_by;
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        if (!$rs) {
            return( $items );
            
        } else {
            $items=$this->load_obj($rs,$content_type);      
        }
        
        return( $items );
    }
    
    
    //Find title, date and permalink from category id.
    // Assing values to new object call Headline
    public function find_category_headline($pk_fk_content_category, $filter=null, $_order_by='ORDER BY 1')
    {
        $_where = 'in_litter=0';
        if( !is_null($filter) ) {
            if( preg_match('/in_litter=1/i', $filter) ) {
                //se busca desde la litter.php
                $_where = $filter;
            } else {
                $_where = $filter.' AND in_litter=0';
            }
        }
        
        $sql = 'SELECT contents.pk_content, contents.title, contents.permalink, contents.created, contents.changed,
                       contents.starttime, contents.endtime  FROM contents_categories, contents ' .
               'WHERE contents.fk_content_type=1 and '.$_where.' AND pk_fk_content_category=\'' .
               $pk_fk_content_category.'\'  AND  pk_fk_content = pk_content '.$_order_by;
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        $items = $this->load_obj($rs, 'Headline');
        return $items;
    }
    
    
    //this function returns title,catName and permalinks of last headlines from Subcategories of a given category
    public function find_headlines(/*$filter=null, $_order_by='ORDER BY 1'*/)
    {
        $sql = 'SELECT `contents`.`title` , `contents`.`created` ,  `contents`.`permalink` , `contents`.`starttime` ,
                       `contents`.`endtime` , `contents_categories`.`pk_fk_content_category` AS `category_id` 
                FROM `contents`
                    LEFT JOIN contents_categories ON ( `contents`.`pk_content` = `contents_categories`.`pk_fk_content` )
                WHERE `contents`.`content_status` =1
                    AND `contents`.`frontpage` =1
                    AND `contents`.`available` =1
                    AND `contents`.`fk_content_type` =1
                    AND `contents`.`in_litter` =0
                ORDER BY `contents`.`placeholder` ASC, `created` DESC ';
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $ccm = ContentCategoryManager::get_instance();
        
        while(!$rs->EOF) {
            $items[] = array(
                'title'=>$rs->fields['title'],
                'catName'=> $ccm->get_name($rs->fields['category_id']),
                'permalink'=> $rs->fields['permalink'],
                'created'=> $rs->fields['created'],
                'category_title'=> $ccm->get_title($ccm->get_name($rs->fields['category_id']))
            );
            
            $rs->MoveNext();
        }
        
        $items = $this->getInTime($items);
        
        return( $items );
    }
    
    
    //this function returns title,catName and permalinks of last headlines from Subcategories of a given category
    public function find_listAuthors($filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type);
        $items = array();
        $_where = '1=1  AND in_litter=0';
        
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') {
                //se busca desde la litter.php
                $_where = $filter;
            }
            
            $_where = $filter.' AND in_litter=0';
        }
        // METER TB LEFT JOIN
        //necesita el as id para paginacion

         $sql= 'SELECT contents.pk_content, opinions.pk_opinion as id, authors.name, authors.pk_author,authors.condition, contents.title,
                    author_imgs.path_img, contents.permalink, opinions.type_opinion, opinions.body, contents.created, contents.starttime,
                    contents.endtime 
                FROM contents, opinions
                    LEFT JOIN authors ON (authors.pk_author=opinions.fk_author)
                    LEFT JOIN author_imgs ON (opinions.fk_author_img=author_imgs.pk_img)
                WHERE `contents`.`fk_content_type`=4 and contents.pk_content=opinions.pk_opinion
                    AND '.$_where.' '.$_order_by;
        
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);        
        $items = $rs->GetArray();
        
        return( $items );
    }
    
    
    public function find_listAuthorsEditorial($filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type);
        $items = array();
        $_where = '1=1  AND in_litter=0';
        
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') {
                //se busca desde la litter.php
                $_where = $filter;
            }
            
            $_where = $filter.' AND in_litter=0';
        }
        
        $sql= 'SELECT authors.name, opinions.pk_opinion as id, contents.title, contents.permalink, opinions.type_opinion,
                      opinions.body,contents.created
               FROM contents, opinions
                    LEFT JOIN authors ON (authors.pk_author=opinions.fk_author)
               WHERE `contents`.`fk_content_type`=4 and opinions.type_opinion=1 AND contents.pk_content=opinions.pk_opinion
                    AND '.$_where.' '.$_order_by;
        
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        $items = $rs->GetArray();
        
        return $items;
    }
    
    
    //FIXME: unificar todos los paginates
    //create_paginate() -
    /*  PARAMS:
     * $total_items ->num eltos a  paginar
     * $num_pages -> numero de elementos por pagina
     * $delta ->cantidad de numeros que se visualizan.
     *  $function ->nombre de la funcion en js / URL (segun se quiera recargar ajax o una url)
     * $params -> parametros de la funcion js / dir url  que se carga
     */
    public function create_paginate($total_items, $num_pages, $delta, $funcion='null', $params='null')
    {
        if(!isset($num_pages)) {
            $num_pages = 5;
        }
        
        if(!isset($total_items)) {
            $total_items = 40;
        }
        
        if(!isset($delta)) {
            $delta = 2;
        }
        
        $page='page';
        $path='';
        
        if($funcion == 'URL'){
            $fun="%d/";
            $append=false;
            $path = SITE_URL.$params;
            
            if($params=='/seccion/opinion') {
                //En listado de opinion, hay dos pages. List autors y list opinions.
                $page='pageop';
            }
            
        } elseif($function != "null") {
            if($params=='null') {
                $fun = 'javascript:'.$funcion.'(%d)';
            } else {
                $fun = 'javascript:'.$funcion.'('.$params.',%d)';
            }
            
            $append = false;
            
        } else {
            $fun = "";
            $append = true;
        }
        
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $num_pages,
            'delta'       => $delta,
            'clearIfVoid' => true,
            'urlVar'      => $page,
            'separator' => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator' => 1,
            'totalItems'  => $total_items,
            'append'      => $append,
            'path'        => $path,
            'fileName'    => $fun,
            'altPage'     => 'Página %d',
            'altFirst'    => 'Primera',
            'altLast'     => 'Última',
            'altPrev'     => 'Página previa',
            'altNext'     => 'Siguiente página'
        ); 
        
        $pager = Pager::factory($pager_options);
        
        return $pager;
    }
    
    
    //FIXME: unificar todos los paginates
    //Paginate para contents de num_pages
    //index_paginate_articles
    //Admin  advertisement.php, advertisement_images.php, opinion.php, preview_content.php
    public function paginate_num($items, $num_pages)
    {
        $_items = array();
        
        foreach($items as $v) {
            $_items[] = $v->id;
        }
        
        $items_page = (defined(ITEMS_PAGE))?ITEMS_PAGE: $num_pages;
        
        $params = array(
            'itemData'    => $_items,
            'perPage'     => $items_page,
            'delta'       => 1, //Num de paginas antes y despues de la actual
            'append'      => true,
            'separator'   => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator'  => 1,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'mode'        => 'Sliding',
            'linkClass'   => 'pagination',
            'altFirst'    => 'primera p&aacute;gina',
            'altLast'     => '&uacute;ltima p&aacute;gina',
            'altNext'     => 'p&aacute;gina seguinte',
            'altPrev'     => 'p&aacute;gina anterior',
            'altPage'     => 'p&aacute;gina'
        );
        
        $this->pager = &Pager::factory($params);
        $data  = $this->pager->getPageData();
        
        $result = array();
        foreach($items as $k => $v) {
            if( in_array($v->id, $data) ) {
                $result[] = $v; // Array 0-n compatible con sections Smarty
            }
        }
        
        return($result);
    }
    
    
    //Mantener pagina en frontend comentarios y Planconecta.
    //Paginate para contents de num_pages
    public function paginate_num_js($items, $num_pages, $delta, $funcion,$params='null')
    {
        if(!isset($num_pages)){
            $num_pages = 20;
        }
        
        if(!isset($delta)) {
            $delta = 1;
        }
        
        if($params=='null') {
            $fun = $funcion.'(%d)';
        } else { 
            $fun = $funcion.'('.$params.',%d)';
        }
        
        $_items = array();
        
        foreach($items as $v) {
            $_items[] = $v->id;
        }
        
        $items_page = (defined(ITEMS_PAGE))? ITEMS_PAGE: $num_pages;
        
        $params = array(
            'itemData'      => $_items,
            'perPage'       => $items_page,
            'delta'         => $delta, //Num de paginas antes y despues de la actual
            'append'        => true,
            'separator'     => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator' => 1,
            'clearIfVoid'   => true,
            'urlVar'        => 'page',
            'mode'          => 'Sliding',
            'append'        => false,
             'path'         => '',
            'fileName'      => 'javascript:'.$fun,
            'linkClass'     => 'pagination',
            'altFirst'      => 'primera p&aacute;gina',
            'altLast'       => '&uacute;ltima p&aacute;gina',
            'altNext'       => 'p&aacute;gina seguinte',
            'altPrev'       => 'p&aacute;gina anterior',
            'altPage'       => 'p&aacute;gina'
        );
        
        $this->pager = &Pager::factory($params);
        $data  = $this->pager->getPageData();
        
        $result = array();
        foreach($items as $k => $v) {
            if( in_array($v->id, $data) ) {
                $result[] = $v; // Array 0-n compatible con sections Smarty
            }
        }
        
        return($result);
    }
    
    
    // admin - article.php - search related.
    //FIXME: unificar todos los paginates
    public function paginate_array_num_js($items, $num_pages, $delta, $funcion, $params='null')
    {
        $_items = array();
        
        foreach($items as $v) {
            $_items[] = $v['id'];
        }
        
        if(!isset($num_pages)) {
            $num_pages = 20;
        }
        
        if(!isset($delta)) {
            $delta = 1;
        }
        
        if($params=='null') {
            $fun = $funcion.'(%d)';
        } else {
            $fun = $funcion.'('.$params.',%d)';
        }
        
        $items_page = (defined(ITEMS_PAGE))?ITEMS_PAGE: $num_pages;
        //'fileName' => '/opinion/%d'
        $params = array(
            'itemData'      => $_items,
            'perPage'       => $items_page,
            'delta'         => $delta,
            'append'        => true,
            'separator'     => '|',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator' => 1,
            'clearIfVoid'   => true,
            'urlVar'        => 'page',
            'append'        => false,
            'path'          => '',
            'fileName'      => 'javascript:'.$fun,
            'mode'          => 'Sliding',
            
            'linkClass'     => 'pagination',
            'altFirst'      => 'primera p&aacute;gina',
            'altLast'       => '&uacute;ltima p&aacute;gina',
            'altNext'       => 'p&aacute;gina seguinte',
            'altPrev'       => 'p&aacute;gina anterior',
            'altPage'       => 'p&aacute;gina'
        );
        
        $this->pager = &Pager::factory($params);
        $data  = $this->pager->getPageData();
        
        $result = array();
        foreach($items as $k => $v) {
            if( in_array($v['id'], $data) ) {
                $result[] = $v; // Array 0-n compatible con sections Smarty
            }
        }
        
        return($result);
    }
    
    
    //FIXME: pinta las paginas que ejecutan js
    //admin article.php, article_change_videos.php
    public function makePagesLinkjs($Pager, $funcion, $params)
    {
        $szPages=null;
        if($Pager->_totalPages>1) {
            $szPages = '<p align="center">';
            
            if ($Pager->_currentPage != 1) {
                $szPages .= '<a style="cursor:pointer;" onClick="'.$funcion.'('.$params.',1);">Primera</a> ... | ';
            }
            
            for($iIndex=$Pager->_currentPage-2; $iIndex<=$Pager->_currentPage+2 && $iIndex <= $Pager->_totalPages;$iIndex++) {
                
                if($Pager->_currentPage == 1) {
                    if(($iIndex+2) > $Pager->_totalPages) {
                        break;
                    }
                    
                    $szPages .= '<a style="cursor:pointer;" onClick="'.$funcion.'('.$params.','.($iIndex+2).');">';
                    if($Pager->_currentPage == ($iIndex+2)) {
                        $szPages .= '<b>' . ($iIndex+2) . '</b></a> | ';
                    } else {
                        $szPages .= ($iIndex+2) . '</a> | ';
                    }
                    
                } elseif($Pager->_currentPage == 2) {
                    if(($iIndex+1) > $Pager->_totalPages) {
                        break;
                    }
                    
                    $szPages .= '<a style="cursor:pointer;" onClick="'.$funcion.'('.$params.','.($iIndex+1).');">';
                    if($Pager->_currentPage == ($iIndex+1)) {
                        $szPages .= '<b>' . ($iIndex+1) . '</b></a> | ';
                    } else {
                        $szPages .= ($iIndex+1) . '</a> | ';
                    }
                    
                } else {
                    $szPages .= '<a style="cursor:pointer;" onClick="'.$funcion.'('.$params.','.$iIndex.');">';
                    if($Pager->_currentPage == ($iIndex)) {
                        $szPages .= '<b>' . $iIndex . '</b></a> | ';
                    } else {
                        $szPages .= $iIndex . '</a> | ';
                    }
                }
                
            }
            
            if($Pager->_currentPage != $Pager->_lastPageText) {
                $szPages .= '... <a style="cursor:pointer;" onClick="' . $funcion .
                                    '(' . $params.','.$Pager->_lastPageText.');">Última </a>';
            }
            
            $szPages .= "</p> ";
        }
        
        return $szPages;
    }
    
    
    //FIXME: unificar todos los paginates   
    //Print Pagination links for function get_images(category,page,action, metadatas)
    //admin article.php, article_change_images.php
    public function makePagesLink($Pager, $category, $action, $metadatas)
    {
        $szPages = null;
        
        if($Pager->_totalPages>1) {
            $szPages = '<p align="center">';
            if ($Pager->_currentPage != 1) {
                $szPages .= '<a style="cursor:pointer;" onClick="get_images(' .
                        $category.',1, \''.$action.'\', \''.$metadatas.'\');">Primera</a> ... | ';
            }
            
            for($iIndex=$Pager->_currentPage-2; $iIndex<=$Pager->_currentPage+2 && $iIndex <= $Pager->_totalPages; $iIndex++) {
                if($Pager->_currentPage == 1) {
                    if(($iIndex+2) > $Pager->_totalPages) {
                        break;
                    }
                    
                    $szPages .= '<a style="cursor:pointer;" onClick="get_images('.$category.',' .
                            ($iIndex+2).',     \''.$action.'\', \''.$metadatas.'\');">';
                    
                    if($Pager->_currentPage == ($iIndex+2)) {
                        $szPages .= '<b>' . ($iIndex+2) . '</b></a> | ';
                    } else {
                        $szPages .= ($iIndex+2) . '</a> | ';
                    }
                    
                } elseif($Pager->_currentPage == 2) {
                    if(($iIndex+1) > $Pager->_totalPages) {
                        break;
                    }
                    
                    $szPages .= '<a style="cursor:pointer;" onClick="get_images('.$category.',' .
                            ($iIndex+1).',     \''.$action.'\', \''.$metadatas.'\');">';
                    if($Pager->_currentPage == ($iIndex+1)) {
                        $szPages .= '<b>' . ($iIndex+1) . '</b></a> | ';
                    } else {
                        $szPages .= ($iIndex+1) . '</a> | ';
                    }
                    
                } else {
                    $szPages .= '<a style="cursor:pointer;" onClick="get_images(' . $category.',' .
                            $iIndex.', \''.$action.'\', \''.$metadatas.'\');">';
                    if($Pager->_currentPage == ($iIndex)) {
                        $szPages .= '<b>' . $iIndex . '</b></a> | ';
                    } else {
                        $szPages .= $iIndex . '</a> | ';
                    }
                }
                
            }
            
            if($Pager->_currentPage != $Pager->_lastPageText) {
                $szPages .= '... <a style="cursor:pointer;" onClick="get_images('.$category.',' .
                        $Pager->_lastPageText.', \''.$action.'\', \''.$metadatas.'\');">Última </a>';
            }
            
            $szPages .= "</p> ";
        }
        
        return $szPages;
    }
    
    
    /* FIXME: Establecer los plurales siguiendo el criterio del idioma espanhol
    para otros casos ya tenemos versiones inglesas */
    public function pluralize($name)
    {
        $name = strtolower($name);
        return $name . 's';
    }
    
    
    //Coge todos los tipos que hay en la tabla
    public function get_types()
    {
        $items = array();
        $sql = 'SELECT pk_content_type, name, title FROM content_types ';
        
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while(!$rs->EOF) {
            $pk_content_type = $rs->fields['pk_content_type'];
            $items[$pk_content_type] = $rs->fields['title'];
            $rs->MoveNext();
        }
        
        return $items;
    }
    
    
    //returns an array with permalinks of the articles in the subsections of a given section
    public function get_permalinks_by_categoryID($categoryID, $filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type); // ¿?¿?¿? $content_type ¿?¿?¿?
        $items = array();
        $_where = '1=1  AND in_litter=0';
        
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') {
                $_where = $filter;
            }
            
            $_where = $filter . ' AND in_litter=0';
        }
        
        $sql= 'SELECT contents.title, contents.permalink, contents.created, contents.changed,
                      contents.metadata, contents.starttime, contents.endtime FROM contents, contents_categories 
               WHERE contents.pk_content = contents_categories.pk_fk_content 
                     AND contents_categories.pk_fk_content_category=\''.$categoryID.'\' 
                     AND '.$_where.' '.$_order_by;
        
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);
        
        $items = $rs->GetArray();
        return $items;
    }
    
    
    /**
      * Get authors for sitemap XML
      *
      * @param string $filter
      * @param string $_order_by
      * @return array
     */
    public function getOpinionAuthorsPermalinks($filter=null, $_order_by='ORDER BY 1')
    {
        $this->init($content_type);
        $items = array();
        $_where = '1=1  AND in_litter=0';
        
        if( !is_null($filter) ) {
            if( $filter == 'in_litter=1') {
                //se busca desde la litter.php
                $_where = $filter;
            }
            
            $_where = $filter.' AND in_litter=0';
        }
        
        // METER TB LEFT JOIN
        //necesita el as id para paginacion
        
         $sql= 'SELECT contents.title, contents.metadata,contents.permalink,contents.changed,contents.starttime,contents.endtime 
                FROM contents, opinions
                    LEFT JOIN authors ON (authors.pk_author=opinions.fk_author)
                    LEFT JOIN author_imgs ON (opinions.fk_author_img=author_imgs.pk_img)
                WHERE `contents`.`fk_content_type`=4 and contents.pk_content=opinions.pk_opinion
                    AND '.$_where.' '.$_order_by;
        
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql);
        $items = $rs->GetArray();
        
        return $items;
    }
    
    
    /// QUITAR - esta en content_category_manager
    //Returns cetegory id
    public function get_id($category)
    {
        $sql = 'SELECT pk_content_category FROM content_categories WHERE name = \''.$category.'\'';
        //echo "<hr>".$sql."<br>";
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        return $rs->fields['pk_content_category'];
    }
    
    
    //Returns categoryName with the content Id
    public function get_categoryName_by_contentId($contentId)
    {
        $sql = 'SELECT pk_fk_contents_category FROM `contents_categories` where pk_fk_content = \''.$contentId.'\'';
        $rs = $GLOBALS['application']->conn->GetOne($sql);
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
        
        $category_name=$this->get_title($rs);
        
        return $category_name;
    }
    
    
    //Devuelve un array de objetos segun se pase un array de id's
    public function getContents($pk_contents)
    {
        $contents = array();
        if( is_array($pk_contents) && count($pk_contents) > 0 ) {
            $sql  = 'SELECT * FROM `contents` WHERE pk_content IN ('.implode(',', $pk_contents).')';
            $rs = $GLOBALS['application']->conn->Execute($sql);
            
            if($rs !== false) {
                while(!$rs->EOF) {
                    $obj = new Content();
                    $obj->load($rs->fields);
                    $obj->content_type = $GLOBALS['application']->conn->GetOne('SELECT title FROM `content_types` WHERE pk_content_type = "' .
                                                                                    $obj->fk_content_type . '"');
                    $obj->category_name = $obj->loadCategoryName($obj->id);
                    
                    $contents[] = $obj; 
                    
                    $rs->MoveNext();
                }
            }
        }
        
        $contentsOrdered = array();
        foreach($pk_contents as $pk_content) {
            foreach($contents as $content) {
                if($content->pk_content == $pk_content) {
                    $contentsOrdered[] = $content;
                    break;
                }
            }
        }
        
        return $contentsOrdered;
    }
    
} // END: class ContentManager