<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Message as m;
/**
 * Class for handling user access to sections in backend
 *
 * Explanation of this class
 *
 * @package    Onm
 * @subpackage Acl
 * @version    $Id: PrivilegesCheck.class.php 1 2010-10-07 17:44:01Z Fran Dieguez $
 */
class Acl
{
    /**
     * Shortcut to check privilege
     *
     * @see Privileges_check::CheckPrivileges()
     *
     * @param string $rule
     * @param string $module
     *
     * @return boolean
     **/
    public static function check($rule, $module=null)
    {
        if (!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }

        return PrivilegesCheck::CheckPrivileges($rule);
    }

    /**
     * Shortcut to check privilege and forward
     *
     * @see Privileges_check::CheckPrivileges()
     * @param string $rule
     * @param string $module
     * @return boolean
     **/
    public static function checkOrForward($rule, $module=null)
    {
        if (!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }

        if ( !PrivilegesCheck::CheckPrivileges($rule)) {
            m::add( _("Sorry, you don't have enought privileges") );
            Application::forward('/admin/');
        }
        return true;
    }

    /**
     * Check if is admin session
     * @return boolean
    */
    public static function isAdmin()
    {
        if (isset($_SESSION['isMaster'])
            && $_SESSION['isMaster']
        ) {
            return true;
        }
        if (isset($_SESSION['isAdmin'])
            && $_SESSION['isAdmin']
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if is master session
     * @return boolean
    */
    public static function isMaster()
    {
        if (isset($_SESSION['isMaster'])
            && $_SESSION['isMaster']
        ) {
            return true;
        }

        return false;
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
        return PrivilegesCheck::CheckAccessCategories($category);
    }

    public static function deny($message='Acceso no permitido')
    {
        if (strlen($message) > 0) {
            $message = new Message($message, 'error');
            $message->push();
        }

        m::add( _("Sorry, you don't have enought privileges") );
        Application::forward('/admin/');
    }
}
