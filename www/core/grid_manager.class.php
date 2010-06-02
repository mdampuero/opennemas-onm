<?php

class GridManager
{
    
    
    
    
    /**
     * Get available grids
     *
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
}