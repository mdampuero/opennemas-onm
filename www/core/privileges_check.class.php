<?php

class Privileges_check
{    

    public static function CheckAccessCategories($CategoryId)
    {
        $session = new Zend_Session_Namespace();
        try {
            if(!isset($CategoryId) || empty($CategoryId)) {
                $session->lasturlcategory = $_SERVER['REQUEST_URI'];
                return true;
            }
            
            if(isset($session->isAdmin) && $session->isAdmin) {
                return true;
            }
            
            if( !isset($session->accesscategories) || 
                empty($session->accesscategories)  ||
                !in_array($CategoryId, $session->accesscategories))
                return false;
                 
        } catch(Exception $e) {
            return false;
        }
        
        $session->lasturlcategory = $_SERVER['REQUEST_URI'];
        return true;
    }


    public static function CheckPrivileges($Privilege)
    {
        $session = new Zend_Session_Namespace();
        try {
            if( !isset($session->userid) || Privileges_check::CheckSessionExpireTime() ) {
                Privileges_check::SessionExpireTimeAction();
            }
            
            if(isset($session->isAdmin) && $session->isAdmin) {
                return true;
            }
            
            if( !isset($session->privileges) ||
                empty($session->userid) ||
                !in_array($Privilege, $session->privileges)) {                
                    return false;
            }
            
        } catch(Exception $e) {
            return false;
        }
        
        $session->lasturl = $_SERVER['REQUEST_URI'];
        return true;
    }

    private static function SessionExpireTimeAction() {
        $fc = Zend_Controller_Front::getInstance();
        
        $request = $fc->getRequest();
        $request->setControllerName('user')
            ->setActionName('login')
            ->setDispatched(true);
    }

    public static function LoadSessionExpireTime() {
        $session = new Zend_Session_Namespace();
        if(isset($session->default_expire)) {
            $session->expire = time()+($session->default_expire *60);
        }
    }

    private static function CheckSessionExpireTime() {
        $session = new Zend_Session_Namespace();
        //if(time() > $session->expire']) {
        //    session_destroy(); 
        //    unset($_SESSION);
        //    return true;
        //}
        //Actuliza la sesion
        Privileges_check::LoadSessionExpireTime();
        return false;
    }

    // Comprobación de session caducada y privilegios
    function HandleError($errno, $errstr, $errfile, $errline) {
        //no difference between excpetions and E_WARNING
        /*echo "<pre>user error handler:<il><li>e_warning=".E_WARNING."<li>num=".$errno." <li>msg=".$errstr.
            " <li>line=".$errline." <li>file=".$errfile."</il></pre>\n\n\n";*/
        throw new Exception($errstr, $errno);
        return true;
        //change to return false to make the "catch" block execute;
    }

    function InitHandleErrorPrivileges() {
        //set_error_handler('handleError');
    }
}

/**
 * Shortcut static class to test privileges
 * 
 */
class Acl
{
    /**
     * Shortcut to check privilege
     * 
     * @see Privileges_check::CheckPrivileges()
     * @param string $rule
     * @param string $module
     * @return boolean
    */
    public static function _($rule, $module=null)
    {
        if(!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }
        
        return Privileges_check::CheckPrivileges($rule);
    }
    
    /**
     * Shortcut to check access to category
     *
     * @see Privileges_check::CheckAccessCategories()
     * @param string $category
     * @return boolean
    */
    public static function _C($category)
    {
        return Privileges_check::CheckAccessCategories($category);
    }
    
    public static function deny($message='Acceso no permitido')
    {
        if(strlen($message) > 0) {
            $message = new Message($message, 'error');
            $message->push();
        }        
        
        $fc = Zend_Controller_Front::getInstance();
        $request = $fc->getRequest();
        $request->setControllerName('panel')
					->setActionName('index')
					->setDispatched(true);
    }
}


