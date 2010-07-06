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
 * ThemeManager
 * 
 * @package    Core
 * @subpackage FrontManager
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: theme_manager.class.php 1 2010-05-27 11:01:18Z vifito $
 */
class ThemeManager
{
    /**
     * @var ThemeManager    Singleton instance
     */
    private static $_instance = null;
    
    /**
     * @var array   Array of themes
     */
    private $themes = array();
    
    
    /**
     * Construct
     *
     * @uses ThemeManager::populate()
     */
    private function __construct()
    {        
        $this->populate();
    }
    
    
    /**
     * Get instance (singleton)
     *
     * @return ThemeManager
     */
    public function getInstance()
    {
        if(self::$_instance == null) {
            self::$_instance = new ThemeManager();
        }
        
        return self::$_instance;
    }
    
    
    /**
     * Get available themes 
     * 
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }
    
    
    /**
     * Load internal array $themes
     *
     * @access private
     * @uses Zend_Config_Ini
     */
    private function populate()
    {
        $path = SITE_PATH . '/themes';
        
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $dirName = $fileinfo->getFilename();
                
                $infoFile = $path . '/' . $dirName . '/theme.ini'; 
                
                if( file_exists($infoFile) ) {
                    $info = new Zend_Config_Ini($infoFile);
                    
                    $this->themes[] = array(
                        'name' => $dirName,
                        'info' => $info->toArray(),
                    );
                }
            }
        }
    }
    
    
    /** 
     * Check if $theme is a valid theme
     *
     * @param string $theme
     * @return boolean
     */
    public function isValid($theme)
    {
        foreach($this->themes as $it) {
            if($it['name'] == $theme) {
                return true;
            }
        }
        
        return false;
    }
    
} // END: class ThemeManager