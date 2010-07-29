<?php

class Onm_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{
    
    protected function _initModule()
    {                
        $modulePath = realpath(APPLICATION_PATH . '/modules/' . strtolower($this->getModuleName()));        
        
        if($modulePath !== false) {
            
            // If exists folder "views"
            if(file_exists($modulePath . '/views')) {
                $tpl = Zend_Registry::get('tpl');
                $tpl->addTemplateDir( $modulePath . '/views' );
            }
            
            // If exists routes.xml
            if(file_exists($modulePath . '/configs/routes.xml')) {
                $this->bootstrap('frontController');
                $front = $this->getResource('frontController');
                
                // Routes
                $router = $front->getRouter();
                $router->addConfig( new Zend_Config_Xml( $modulePath . '/configs/routes.xml', APPLICATION_ENV) );
            }
        }
        
    }
    
    
    
}