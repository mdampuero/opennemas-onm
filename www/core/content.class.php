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
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Content
 * 
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: content.class.php 1 2009-11-30 18:16:56Z vifito $
 */
class Content
{
    
    public $pk_content = null;
    public $fk_content_type = null;
    public $title = null;
    public $description = null;
    public $metadata = null;
    public $starttime = null;
    public $endtime = null;
    public $created = null;
    public $changed = null;
    public $fk_author = null;
    public $fk_publisher = null;
    public $fk_user_last_editor = null;
    public $views = null;
    public $status = null;
    public $published = null;
    public $slug = null;
    
    public $cache = null;
    public $conn  = null;
    
    public static $validStatus = array('PENDING', 'AVAILABLE', 'REMOVED');
    
    /**
     * Constructor
     *
     * @param null|int $id  Pk_content identifier
     */
    public function __construct($id=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));
        
        if( Zend_Registry::isRegistered('conn') ) {
            $this->conn  = Zend_Registry::get('conn');
        }        
        
        if(!is_null($id)) {
            $this->read($id);
        }   
    }    
    
    /**
     * Create a new content
     * 
     * @param array $data   Values to save content (POST data, normally)
     * @return boolean
     */
    public function create( $data )
    {
        // Prepare array data with default values if it's necessary
        $this->prepareData(&$data);
        
        $fields = array('fk_content_type',
                        'title', 'slug', 'description', 'metadata',
                        'starttime', 'endtime', 'created', 'changed', 'published',
                        'fk_author', 'fk_publisher', 'fk_user_last_editor',
                        'views',
                        'status');
        
        $pk_content = SqlHelper::bindAndInsert('contents', $fields, $data);
        
        return $pk_content;
    }
    
    /**
     * Attach content to category
     *
     * @param int $pk_content
     * @param int $pk_category
     */
    public function attachCategory($pk_content, $pk_category)
    {        
        $catName = $conn->GetOne('SELECT `name` FROM `categories` WHERE `pk_content_category` = ?',
                                 array($pk_category));
        
        $sql = 'INSERT INTO `contents_categories` (`pk_fk_content`, `pk_fk_content_category`, `catName`)
                    VALUES (?, ?, ?)';
        $values = array($pk_content, $pk_category, $catName);
        
        if($this->conn->Execute($sql, $values) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
    }
    
    /**
     * Detach content to category
     *
     * @param int $pk_content
     * @param int $pk_category
     */
    public function detachCategory($pk_content, $pk_category)
    {        
        $sql = 'DELETE FROM `categories` `pk_fk_content` = ? AND `pk_fk_content_category` = ?';
        $values = array($pk_content, $pk_category);
        
        if($this->conn->Execute($sql, $values) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
        }
    }
    
    /**
     * Set init values
     *
     * @param array $data
     */
    private function prepareData($data)
    {
        if(!isset($data['fk_content_type']) && is_null($this->content_type)) {
            throw new Exception(__CLASS__ . '::' . __METHOD__ . ' need content_type property.');
        }
        
        $defaults = array(
            'fk_content_type' => $this->getContentTypeId(),
            'slug' => String_Utils::get_title($data['title'], false),
            
            'starttime' => '0000-00-00 00:00:00',
            'endtime'   => '0000-00-00 00:00:00',
            
            'created'   => date('Y-m-d H:i:s'),
            'changed'   => date('Y-m-d H:i:s'),
            'published' => date('Y-m-d H:i:s'),
            
            // TODO: remove $_SESSION['userid'] by object to manage session, $sess->getUserId()
            'fk_author'    => $_SESSION['userid'],
            'fk_publisher' => $_SESSION['userid'],
            'fk_user_last_editor' => $_SESSION['userid'],
            
            'views'  => 0,
            'status' => 'PENDING',
        );                
        
        // Dont use array_merge. Use "+" operator for arrays to preserve keys.
        $data = $data + $defaults;
    }
    
    
    /**
     * Get content type identifier (fk_content_type)
     *
     * @param string $content_type  Name of content
     * @return int  Return content type identifier
     */
    public function getContentTypeId($content_type=null)
    {
        if(is_null($content_type)) {
            $content_type = $this->content_type;
        }
        
        $fk_content_type = $this->conn->
            GetOne('SELECT * FROM `content_types` WHERE name = "' . $content_type . '"');
        
        return $fk_content_type;
    }
    
    /**
     * Check if $status is a valid status string
     *
     * @param string $status
     */
    public function isValidStatus($status)
    {
        return in_array($status, Content::$validStatus);
    }
    
    public function read($id)
    {        
        $sql = 'SELECT * FROM `contents` WHERE `pk_content` = ?';
        $rs = $this->conn->Execute( $sql, array($id) );
        
        if ( false === $rs) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        // Load object properties
        $this->load( $rs->fields );
    }
    
    
    function loadCategoryName($pk_content) {
        $ccm = ContentCategoryManager::get_instance();
        
        if(empty($this->category)) {
            $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';
            $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
            $this->category = $rs;
        }
        
        return $ccm->get_name($this->category);
    }


    function loadCategoryTitle($pk_content) {
        $ccm = ContentCategoryManager::get_instance();
        
        if(empty($this->category_name)) {
            $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';
            $rs = $GLOBALS['application']->conn->GetOne($sql, $pk_content);
            $this->category = $rs;
            $this->loadCategoryName( $this->category );
        }
        
        return $ccm->get_title($this->category_name);
    }
    
    /**
     * Load properties for "this" object from $properties
     * 
     * @param stdClass|array $properties
     */
    protected function load($properties)
    {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if( !is_numeric($k) ) {
                    $this->{$k} = $v;
                }
            }
        }
    }
    
    
    /**
     *
     */
    public function update($data)
    {
        // Prepare array data with default values if it's necessary
        $this->prepareData(&$data);
        
        $fields = array('title', 'slug', 'description', 'metadata',
                        'starttime', 'endtime', 'changed', 'published',
                        'fk_user_last_editor', 'status');
        
        SqlHelper::bindAndUpdate('contents', $fields, $data, 'pk_content = ' . $data['pk_content']);
    }
    
    /**
     * Change status 
     *
     * @param int $pk_content
     * @param string $status
     */
    public function changeStatus($pk_content, $status)
    {
        if($this->isValidStatus($status)) {
            // TODO: incorporar auditoría
            $fields = array('status');
            $data   = array('status' => $status);
            
            SqlHelper::bindAndUpdate('contents', $fields, $data, 'pk_content = ' . $pk_content);
        } else {
            throw new Exception(__METHOD__ . ' "' . $status . '" is not a valid status');
        }
    }
    
    //Elimina de la BD
    function remove($id) {       
        $sql = 'DELETE FROM contents WHERE pk_content='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            return;
        }
    
        $sql = 'DELETE FROM contents_categories WHERE pk_fk_content='.($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }
    
    //Envia a la papelera
    function delete($id, $last_editor) {
        $changed = date("Y-m-d H:i:s");
        
        $data = array(0, 0, $last_editor, $changed, $id);
        $this->set_available(array($data), $last_editor);
        
        $sql = 'UPDATE contents SET `in_litter`=?, `changed`=?, `fk_user_last_editor`=? 
          WHERE pk_content='.($id);
        
        $values = array(1, $changed, $last_editor);
           
        if($GLOBALS['application']->conn->Execute($sql, $values)===false) {
             $error_msg = $GLOBALS['application']->conn->ErrorMsg();
             $GLOBALS['application']->logger->debug('Error: '.$error_msg);
             $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
        
             return;
         }
    }


    
    
    /**
     * Check if a content is in time for publishing
     *
     * @return boolean
    */
    public function isInTime($starttime=null, $endtime=null, $time=null)
    {
        if(is_null($starttime)) {
            $start = strtotime($this->starttime);
            $end   = strtotime($this->endtime);
        }
        
        if(is_null($time)) {
            $now = time();
        } else {
            $now = strtotime($time);
        }        
        
        // If $start and $end not defined then return true
        if(empty($start) && empty($end)) {
            return true;
        }
        
        // only setted $end
        if(empty($start)) {
            return ($now < $end);
        } 
        
        // only setted $start
        if(empty($end)) {
            return ($now > $start);
        }
        
        // $start < $now < $end
        return (($now < $end) && ($now > $start));
    }
    
    /**
     * Check if a content start time for publishing
     * don't check Content::endtime
     *
     * @link https://redmine.openhost.es/issues/show/1058#note-8
     * @return boolean
    */
    public function isStarted()
    {
        $now = time();        
        $start = strtotime($this->starttime);
        
        // If $start isn't defined then return true
        if(empty($start)) {
            return true;
        }
        
        return ($now > $start);
    }    

    /**
     * Check if this content is obsolete
     *
     * @return boolean
     */
    public function isObsolete()
    {
        $end   = strtotime($this->endtime);
        $now   = time();

        if(!empty($end)) {
            return $end < $now;
        }

        return false;
    }
    
    /**
     * Check if a content is out of time for publishing
     *
     * @see Content::isInTime()
     * @return boolean
    */
    public function isOutTime($starttime=null, $endtime=null, $time=null)
    {
        return !$this->isInTime($starttime, $endtime, $time);
    }
    
    /**
     * Check if this content is scheduled
     * or, in others words, if this content has a starttime and/or endtime
     *
     * @return boolean
    */
    public function isScheduled()
    {
        return ((!empty($this->starttime) && !preg_match('/0000\-00\-00 00:00:00/', $this->starttime)) ||
                (!empty($this->endtime) && !preg_match('/0000\-00\-00 00:00:00/', $this->endtime)));
    }
  


    function set_numviews($id=null) {

        if(is_null($id) && $this->id != null ) {
            $id = $this->id;
        } elseif (is_null ($id)) {
            return false;
        }
        
        // Multiple exec SQL
        if(is_array($id) && count($id)>0) {
            // Recuperar todos los IDs a actualizar
            $ids = array();
            foreach($id as $item) {
                if(isset($item->pk_content) && !empty($item->pk_content)) {
                    $ids[] = $item->pk_content;
                }
            }

            $sql = 'UPDATE `contents` SET `views`=`views`+1 WHERE `available`=1 AND `pk_content` IN ('.implode(',', $ids).')';
        } else {
            $sql = 'UPDATE `contents` SET `views`=`views`+1 WHERE `available`=1 AND `pk_content`='.$id;
        }

	if($GLOBALS['application']->conn->Execute($sql) === false) {
	  $error_msg = $GLOBALS['application']->conn->ErrorMsg();
	  $GLOBALS['application']->logger->debug('Error: '.$error_msg);
	  $GLOBALS['application']->errors[] = 'Error: '.$error_msg;	  

	  return;
        }
    }
   
    function put_permalink($end, $type, $title, $cat) {
        //Definimos el permalink para la url.
        // Ejemplo: http://xornaldegalicia.com/2008/09/29/deportes/premio/Singapur/Alonso/proclama/campeon/2008092917564334523.html
        //artigo/2008/11/18/galicia/santiago/encuentran-tambre-cadaver-santiagues-desaparecido-lunes/2008111802293425694.html
        
        $fecha=date("Y/m/d");                    
        //Miramos el type.
        $tipo = $GLOBALS['application']->conn->GetOne('SELECT title FROM `content_types` WHERE name = "'. $type.'"');

        //Miramos la categoria y si eso padre.
        $cats = $GLOBALS['application']->conn->
            Execute('SELECT * FROM `content_categories` WHERE pk_content_category = "'. $cat.'"');

        $namecat=strtolower($cats->fields['name']);

        if($namecat){
            $padre=$cats->fields['fk_content_category'];
            if(($padre != 0) && ($tipo!="ficheiro")){ //Es subcategoria
                      $cats = $GLOBALS['application']->conn->
                  GetOne('SELECT name FROM `content_categories` WHERE pk_content_category = "'. $padre.'"');
                  $namecat = strtolower($cats)."/".$namecat;
            }
        }else{
            $namecat=$type;
        } //Para que no ponga //

        //funcion quita los sencillos al titulo
        $stringutils=new String_Utils();
        $titule=$stringutils->get_title($title);

        // $permalink=SITE_URL ."/". $fecha."/". $namecat."/".$titule ."/".$this->id.'.html';
        if($tipo=="album"){
                // /album/YYYY/MM/DD/foto/fechaIDlargo.html Ejem: /album/2008/11/28/foto/2008112811271251594.html
                $permalink="/".$tipo."/". $fecha."/foto/".$this->id.'.html';
        }elseif($tipo=="video"){
                $permalink="/".$tipo."/". $fecha."/".$this->id.'.html';
        }elseif($tipo=="ficheiro"){
                $permalink="/".$tipo."/". $fecha."/". $namecat."/".$end; //En el end esta pasando el nombre del pdf
        }elseif($tipo=="imaxe"){
                $permalink="/media/images" .$end . $title;
        }else{
                $permalink="/".$tipo."/". $fecha."/". $namecat."/".$titule ."/".$this->id.'.html';
        }        
        return $permalink;
    }
    
    /**
     * Abstract factory method getter
     *
     * @param string $pk_content Content identifier
     * @return object Instance of an specific object in function of content type
    */
    public static function get($pk_content)
    {
        $sql  = 'SELECT `content_types`.name FROM `contents`, `content_types` WHERE pk_content=? AND fk_content_type=pk_content_type';
        $type = $GLOBALS['application']->conn->GetOne($sql, array($pk_content));
        
        if($type === false) {
            return null;
        }
        
        $type = ucfirst( $type );
        try {
            return new $type($pk_content);
        } catch(Exception $e) {
            return null;
        }
    }
    
    
    public function __toString()
    {
        return $this->title;
    }
    
}


