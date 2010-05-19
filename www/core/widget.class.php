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
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Widget
 * 
 * @package    Onm
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: widget.class.php 1 2010-05-14 18:31:58Z vifito $
 */
class Widget extends Content
{
    /**
     * @var int Identifier of class
     */
    public $pk_widget = null;
    
    public $content = null;
    public $renderlet = null;    
    
    /**
     * constructor
     *
     * @param int $id 
     */
    public function __construct($id=null)
    {
        parent::__construct();
        
        // FIXME: use reflexion to recover content name
        $this->content_type = 'Widget';
        
        if(!is_null($id)) {            
            $this->read($id);
        }
        
        $this->cache = new MethodCacheManager($this, array('ttl' => 30));        
    }
    
    /**
     * Create a new widget
     * 
     * @param array $data
     * @return boolean  True if operation is successful, otherwise false
     */
    public function create($data)
    {
        // Clear  magic_quotes
        String_Utils::disabled_magic_quotes( &$data );
        
        $pk_content = parent::create($data);
        
        if($pk_content === false) {
            return false;
        }
        
        $fields = array('pk_widget', 'content', 'renderlet');
        $data['pk_widget'] = $pk_content;
        
        try {
            SqlHelper::bindAndInsert('widgets', $fields, $data);
        } catch(Exception $e) {
            return false;
        }
        
        return $pk_content;
    }
    
    /**
     * Read, get a specific object
     *
     * @param int $pk_content Object ID
     * @return Widget Return instance to chaining method
     */
    public function read($pk_content)
    {        
        parent::read($pk_content);
        
        $sql = "SELECT * FROM `widgets` WHERE `pk_widget`=?";
        
        $rs = $this->conn->Execute($sql, array($pk_content));
        if($rs === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return null;
        }
        
        $this->load( $rs->fields );
        return $this;
    }
    
    /**
     * Load properties into this instance
     *
     * @param array $properties Array properties 
     */
    public function load($properties) {
        if(is_array($properties)) {
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        } elseif(is_object($properties)) {
            $properties = get_object_vars($properties);
            foreach($properties as $k => $v) {
                if(!is_numeric($k)) {
                    $this->{$k} = $v;
                }
            }
        }
    }
    
    /**
     * Update
     * 
     * @param array $data Array values
     * @return boolean
     */
    public function update($data) {
        // Clear magic_quotes
        String_Utils::disabled_magic_quotes( &$data );
        
        parent::update($data);
        
        $fields = array('content', 'renderlet');
        $where  = '`pk_widget` = ' . $data['pk_content'];
        
        try {
            SqlHelper::bindAndUpdate('widgets', $fields, $data, $where);
        } catch(Exception $e) {
            return false;
        }
        
        return true;        
    }
    
    
    /**
     * Delete
     *
     * @param int $pk_content Identifier
     * @return boolean
     */
    public function delete($pk_content)
    {        
        parent::delete($pk_content); 
        
        $sql = "DELETE FROM `widgets` WHERE `pk_widget` = ?";
        
        if($this->conn->Execute($sql, array($pk_content)) === false) {
            $error_msg = $this->conn->ErrorMsg();
            Zend_Registry::get('logger')->emerg($error_msg);
            
            return false;
        }
        
        return true;
    }
    
    
    public function render() {
        $method = '_renderlet_'.$this->renderlet;
        //call_user_func_array(array($this, $method), array($smarty));
        return $this->$method();
    }
    
    /**
     * Only return $this->content
     * 
     * @return string
     */
    private function _renderlet_html()
    {
        return $this->content;
    }
    
    /**
     * Eval PHP content
     * 
     * @return string
     */
    private function _renderlet_php()
    {
        ob_start();
        
        eval($this->content);
        
        $output = ob_get_contents();
        ob_end_clean();
        
        return $output;
    }
    
    /**
     *
     * SEE resource.string.php Smarty plugin
     * SEE resource.widget.php Smarty plugin
     *
     * @return string
     */
    private function _renderlet_smarty()
    {        
        Template::$registry['widget'][$this->pk_widget] = $this->content;
        $resource = 'widget:' . $this->pk_widget;
        
        $wgtTpl = new Template(TEMPLATE_USER);
        
        // no caching
        $wgtTpl->caching = 0;
        $wgtTpl->force_compile = true;
        
        $output = $wgtTpl->fetch($resource);
        
        return $output;
    }
}
