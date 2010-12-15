<?php 
// TODO: documentar

/**
 * Privileges_check class
 *
 * Explanation of this class
 *
 * @package    Onm
 * @subpackage
 * @version    $Id: privileges_check.class.php 1 2010-10-07 17:44:01Z Fran Dieguez $
 */
/**
 * Shortcut static class to test privileges
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
     * Long explanation
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

        Application::forward('welcome.php');
    }
}

