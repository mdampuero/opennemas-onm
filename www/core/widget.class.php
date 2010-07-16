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
     * @param int $pkContent 
     */
    public function __construct($pkContent=null)
    {
        parent::__construct($pkContent);        
        
        if (!is_null($pkContent)) {            
            $this->read($pkContent);
        }      
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
     * Update
     * 
     * @param array $data Array values
     * @return int
     */
    public function update($data)
    {
        // Clear magic_quotes
        String_Utils::disabled_magic_quotes( &$data );
        
        parent::update($data);
        
        $fields = array('content', 'renderlet');
        $where  = '`pk_widget` = ' . $data['pk_content'];
        
        $affectedRows = 0;
        
        try {
            $affectedRows = SqlHelper::bindAndUpdate('widgets', $fields, $data, $where);
        } catch(Exception $e) {
            return 0;
        }
        
        return $affectedRows;        
    }
    
    
    /**
     * Delete
     *
     * @param int $pk_content Identifier
     * @return boolean
     */
    public function delete($pk_content)
    {
        // Filter var, prevent Sql injection
        $pk_content = filter_var($pk_content, FILTER_VALIDATE_INT);
        
        parent::delete($pk_content);
        
        $filter = '`pk_widget`=' . $pk_content;
        
        return SqlHelper::delete('widgets', $filter);
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
        $tpl = Zend_Registry::get('tpl');
        
        $smarty = $tpl->createTemplate('string:' . $this->content);
        if(isset($this->_invokeArgs)) {
            foreach($this->_invokeArgs as $prop => $value) {
                $smarty->assign($prop, $value);
            }
        }
        
        $output = $smarty->fetch('string:' . $this->content);
        
        return $output;
    }
    
    
    /**
     *
     * @return String
     */
    public function __toString()
    {
        return $this->render();
    }
    
}
