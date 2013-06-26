<?php
/**
 * Defines the Acl class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Onm_Acl
 */
use Onm\Message as m;

/**
 * Class for handling user access to modules, actions and categories in backend
 *
 * @package    Onm_Acl
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
    public static function check($rule, $module = null)
    {
        if (!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }

        return PrivilegesCheck::CheckPrivileges($rule);
    }

    /**
     * Shortcut to check access to category
     *
     * @see Privileges_check::CheckAccessCategories()
     * @param  string  $category
     *
     * @return boolean
    */
    public static function checkCategoryAccess($category)
    {
        return PrivilegesCheck::CheckAccessCategories($category);
    }

    /**
     * Shortcut to check privilege and forward
     *
     * @see Privileges_check::CheckPrivileges()
     * @param  string  $rule
     * @param  string  $module
     *
     * @return boolean
     **/
    public static function checkOrForward($rule, $module = null)
    {
        if (!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }

        if (!\PrivilegesCheck::CheckPrivileges($rule)) {
            m::add(_("Sorry, you don't have enought privileges"));
            forward301('/admin/');
        }

        return true;
    }

    /**
     * Check if the user is an Administrator
     *
     * @return boolean true if the user is in the Administrator group
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
     * Checks if the user is a Master
     *
     * @return boolean true if the user is in the Master group
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
     * Performs the actions of denying a user actino
     *
     * @param string $message the message to show to the user
     *
     * @return void
     **/
    public static function deny($message = 'Acceso no permitido')
    {
        if (strlen($message) > 0) {
            $message = new Message($message, 'error');
            $message->push();
        }

        m::add(_("Sorry, you don't have enought privileges"));
        Application::forward('/admin/');
    }
}
