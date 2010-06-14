<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNemas project
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
 * @category   OpenNemas
 * @package    OpenNemas
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * Onm_Filter_Unhyphenate
 * 
 * @package    Onm
 * @subpackage Filter
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Unhyphenate.php 1 2010-06-02 14:45:11Z vifito $
 */
class Onm_Filter_Unhyphenate implements Zend_Filter_Interface
{
    
    /**
     * Unhyphenate keys from array
     * $value['q-index'] converts to $value['q']['index']
     *
     * @param array $value 
     */
    public function filter($value)
    {
        if(!is_array($value)) {
            return $value;
        }        
        
        $valueFiltered = array();
        
        foreach($value as $key => $data) {                                    
            $valueFiltered = array_merge_recursive($valueFiltered, $this->_unhyphenatedEntry($key, $data));
        }        
        
        return $valueFiltered;
    }
    
    
    /**
     * Unhyphenate an individual entry into a array
     *
     * <code>
     * $value = **mixed**;
     * $key = 'q-index';
     *
     * $arr = Onm_Filter_Unhyphenate::_unhyphenatedEntry($key, $value);
     * // $arr == array('q' => array('index' => **mixed** ));
     * </code>
     * 
     * @access private
     * @param string $key
     * @param mixed $value
     * @return array
     */
    private function _unhyphenatedEntry($key, $value)
    {
        $valueFiltered = array();
        
        $matches = array();
        if(preg_match_all('/([^\-]+)[\-]?/', $key, $matches)) {
            $keys = '';
            foreach($matches[1] as $newKey) {
                $keys .= '["' . $newKey . '"]';
            }                                
            
            $serialValue = serialize($value);
            eval('$valueFiltered' . $keys . '= unserialize(\'' . $serialValue . '\');');                
        }
        
        return $valueFiltered;
    }
    
}
