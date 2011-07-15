<?php

class Privileges_check
{

    public static function CheckAccessCategories($CategoryId)
    {
        try {
            if (
                !isset($CategoryId)
                || is_null($CategoryId)
                )
            {
                $_SESSION['lasturlcategory'] = $_SERVER['REQUEST_URI'];
                return true;
            }

            if (
                isset($_SESSION['isAdmin'])
                && $_SESSION['isAdmin']
                )
            {
                return true;
            }
            //var_dump(!in_array($CategoryId,$_SESSION['accesscategories']));die();

            if (
                !isset($_SESSION['accesscategories'])
                || empty($_SESSION['accesscategories'])
                || !in_array($CategoryId,$_SESSION['accesscategories'])
                )
            {
                return false;
            }


        } catch(Exception $e) {
            return false;
        }

        $_SESSION['lasturlcategory'] = $_SERVER['REQUEST_URI'];
        return true;
    }


    public static function CheckPrivileges($Privilege, $category = null)
    {
        try {
            if( !isset($_SESSION['userid']) || Privileges_check::CheckSessionExpireTime() ) {
                Privileges_check::SessionExpireTimeAction();
            }

            if( isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] ) {
                return true;
            }

            if (
                !isset($_SESSION['privileges'])
                || empty($_SESSION['userid'])
                || !in_array($Privilege,$_SESSION['privileges'])
                || (!is_null($category) && !(Privileges_check::CheckAccessCategories($category)))
                )
            {
                    return false;
            }

        } catch(Exception $e) {
            return false;
        }

        $_SESSION['lasturl'] = $_SERVER['REQUEST_URI'];
        return true;
    }

    private static function SessionExpireTimeAction() {
        Application::forwardTargetParent("/admin/login.php");
    }

    public static function AccessDeniedAction() {
        Application::forward('/admin/controllers/accessdenied/accessdenied.php'.'?action=list_pendientes&category='.$_REQUEST['category']);
    }

    public static function AccessCategoryDeniedAction() {
        Application::forward('/admin/controllers/accessdenied/accesscategorydenied.php');
    }

    public static function LoadSessionExpireTime() {
        if(isset($_SESSION) && isset($_SESSION['default_expire'])) {
            $_SESSION['expire'] = time()+($_SESSION['default_expire']*60);
        }
    }

    private static function CheckSessionExpireTime() {
        if(time() > $_SESSION['expire']) {
            session_destroy();
            unset($_SESSION);
            return true;
        }
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
        set_error_handler('handleError');
    }
}
