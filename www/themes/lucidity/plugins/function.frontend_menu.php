<?php

/**
 * <code>
 * {frontend_menu active=10 level=3}
 * </code>
 */
function smarty_function_frontend_menu($params, &$smarty)
{
    // Page Manager instance
    $pageMgr = PageManager::getInstance();
    
    // Get root
    $root = $pageMgr->getRoot();
    
    // Get subtree of root
    $tree = $pageMgr->getTree($root->pk_page);        
    
    $status = array('AVAILABLE');
    if(isset($params['status'])) {
        $status = explode(',', $params['status']);
    }    
    
    $type = array('STANDARD', 'SHORTCUT', 'EXTERNAL');
    if((isset($params['type']))) {
        $type = explode(',', $params['type']);
    }    
    
    $front = Zend_Controller_Front::getInstance();
    $baseurl = (isset($params['type']))? $params['baseurl'] : $front->getBaseUrl();
    if($baseurl == null) {
        $baseurl = '/';
    }
    
    
    $active  = (isset($params['active']))? $params['active']  : null;
    $level   = (isset($params['level']))?  $params['level']   : 2;
    
    $options = array(
        'conditions' => array(
            'status' => $status,
            'type'   => $type,
        ),
        
        'active'  => $active,
        'level'   => $level,
        'baseurl' => $baseurl,
    );    
    
    $output = $pageMgr->tree2html($tree, $options);    
    
    return $output;
}