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
 * @package    Core
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
 * GridManager
 * 
 * @package    Core
 * @subpackage FrontManager
 * @copyright  Copyright (c) 2010 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: grid_manager.class.php 1 2010-07-06 14:03:15Z vifito $
 */
class GridManager
{
    
    /**
     * Get available grids by theme
     *
     * @static
     * @param string $theme
     * @return array    List of available grids
    */
    public static function getGrids($theme=null)
    {
        $theme = (is_null($theme))? TEMPLATE_USER: $theme;
        $dir   = SITE_PATH. 'themes/' . $theme . '/grids/';
        
        $grids = array();
        foreach (glob($dir . '*.xml') as $filename) {
            $grids[] = basename($filename, '.xml');
        }
        
        return $grids;
    }
    
    
    /**
     * Get all grids from available themes
     * 
     * @uses ThemeManager
     * @return array
     */
    public function getAllGrids()
    {
        $themeMgr = ThemeManager::getInstance();
        
        $themes = $themeMgr->getThemes();
        
        $grids = array();
        foreach($themes as $t) {
            $grids[$t['name']] = GridManager::getGrids($t['name']);
        }
        
        return $grids;
    }
    
} // END: class GridManager