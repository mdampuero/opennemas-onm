<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty url function plugin
 *
 * Type:     function<br>
 * Name:     url<br>
 * Purpose:  Build a valid route for use Zend MVC
 * @author   Tomás Vilariño <vifito at openhost dot es>
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_url($params, &$smarty)
{
    if(!isset($params['route'])) {
        $smarty->trigger_error('[plugin] url needs a "name" param');
        return;
    }
    
    $name  = $params['route'];
    unset($params['route']);        
    
    $front = Zend_Controller_Front::getInstance();
    
    // Rendering mask from backend
    $isBackend = $front->getParam('isBackend');
    if($isBackend) {
        $router = Zend_Registry::get('routerFrontend');
    } else {        
        $router = $front->getRouter();                
    }    
    
    if($router->hasRoute($name)) {
        $querystring = (count($params)>0)? $params: array();
        $route = $router->getRoute($name);
        
        $urlMvc = $route->assemble($querystring, $reset=true, $encode=true);        
        
        return $urlMvc; 
    }
    
    return '/';
}