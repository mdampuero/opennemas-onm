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
 * Grid
 * 
 * @package    Onm
 * @subpackage 
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: grid.class.php 1 2010-05-24 16:49:40Z vifito $
 */
class Grid
{

    private $_filename = null;
    private $_grid = null;
    private $_positions = null;
    private $_page = null;

    /**
     * @var array   Array of Grid instances
     */
    static private $_instances = array();
    
    /**
     *
     *
     */
    private function __construct($filename)
    {
        if(!file_exists($filename)) {
            throw new GridNotFoundException("Grid file not found: " . $filename);
        }
        
        $this->_filename = $filename;
        $this->load();        
    }
    
    
    /**
     * Get instance to this file
     *
     * @param string|Page $it
     * @return Grid     Return grid instance
     */
    public static function getInstance($it)
    {
        $filename = Grid::getFilename($it);
        
        $id = Grid::generateId($filename);
        
        if(empty(self::$_instances) || !isset(self::$_instances[$id])) {            
            self::$_instances[$id] = new Grid($filename);
            
            // Inject page
            if($it instanceof Page) {
                self::$_instances[$id]->setPage($it);
            }
        }
        
        return self::$_instances[$id];
    }
    
    
    /**
     * Reset instance
     *
     * @static
     * @param string|Page $it
     * @return Grid     Return grid instance
     */
    public function resetInstance($it)
    {
        $filename = Grid::getFilename($it);
        $id = Grid::getId($filename);
        
        unset(self::$_instances[$id]);
    }
    
    
    /**
     *
     *
     */
    public function setPage(Page $page)
    {
        $this->_page = $page;
    }
    
    
    public function getPage()
    {
        return $this->_page;
    }
    
    /**
     * Get filename
     *
     * @static
     * @param string|Page $it
     * @return Grid     Return grid instance
     */
    public static function getFilename($it)
    {
        $filename = '';
        
        if( $it instanceof Page ) {
            $theme = (!is_null($it->theme))? $it->theme: TEMPLATE_USER;                        
            
            if(is_null($it->grid)) {
                throw new GridNotFoundException('Grid file not defined for page: ' . $it->title);
            }
            
            $filename = SITE_PATH. 'themes/' . $theme . '/grids/' . $it->grid . '.xml';
        } else {
            // $it must be absolute path
            $filename = $it;
        }
        
        return $filename;
    }
    
    
    /**
     * Generate a identifier using absolute path of grid
     *
     * @static
     * @param string $filename
     * @return string
     */
    public function generateId($filename)
    {
        return basename($filename, '.xml');
    }
    
    
    /**
     *
     */
    private function load()
    {
        $xml = simplexml_load_file($this->_filename);
        
        $this->_grid = (string)$xml->content;
        $this->_positions = array();
        
        foreach($xml->positions->children() as $position) {
            $this->_positions[] = (string)$position;
        }
        
    }
    
    
    /**
     * Magic method to get property for this object
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $propertyName = '_' . $name;
        if(property_exists($this, $propertyName)) {
            return $this->{$propertyName};
        }
    }
    
    
    /**
     *
     *
     */
    public function __call($name, $args=array())
    {
        if(preg_match('/^get/', $name)) {
            $propertyName = '_' . strtolower(str_replace('get', '', $name));
            
            return $this->{$propertyName};
        }
    }
    
    
    /**
     * Render, one by one, ContentBox objects from array $contents
     *
     * @param array $contents   Sample array('content-left' => array($contentBox1, $contentBox2))
     * @return string
     */
    public function render($contents, $args=array())
    {
        $html = $this->_grid;
        
        if(!empty($contents)) {
            foreach($contents as $placeholder => $items) {
                $output = '';
                foreach($items as $content) {
                    // FIXME: arranxar esta chapuza
                    $args['page'] = $this->getPage();
                    
                    $output .= $content->render($args);
                }                
                
                $html = str_replace('<!-- #' . $placeholder . '# -->', $output, $html);
            }            
        }
        
        
        return $html;
    }
}