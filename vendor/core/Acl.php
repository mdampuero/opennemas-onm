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

        return self::checkPrivileges($rule);
    }

    /**
     * Checks if the current user has access to category given its id.
     *
     * @param  string  $category
     *
     * @return boolean
    */
    public static function checkCategoryAccess($category)
    {
        try {
            if (!isset($categoryID)
                || is_null($categoryID)
            ) {
                $_SESSION['lasturlcategory'] = $_SERVER['REQUEST_URI'];

                return true;
            }

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

            if (!isset($_SESSION['accesscategories'])
                || empty($_SESSION['accesscategories'])
                || !in_array($categoryID, $_SESSION['accesscategories'])
            ) {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the current user has an acl
     *
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

        if (!self::checkPrivileges($rule)) {
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
        forward('/admin/');
    }

    /**
     * Checks if the current user has access to one privilege and category.
     *
     * @param string $privilege  the privelege token.
     * @param string $categoryID the category id
     *
     * @return boolean true if the user has access
     **/
    public static function checkPrivileges($privilege, $categoryID = null)
    {
        try {
            if (isset($_SESSION['isMaster'])
            && $_SESSION['isMaster']
            ) {
                return true;
            }

            if (isset($_SESSION['isAdmin'])
                && $_SESSION['isAdmin']
                && ($privilege !='ONLY_MASTERS')
            ) {
                return true;
            }

            if (!isset($_SESSION['privileges'])
                || empty($_SESSION['userid'])
                || !in_array($privilege, $_SESSION['privileges'])
                || (!is_null($categoryID) && !(self::checkAccessCategories($categoryID)))
            ) {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
