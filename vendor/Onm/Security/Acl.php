<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Security;

use Onm\Message as m;

/**
 * Class for handling user access to modules, actions and categories in backend
 */
class Acl
{
    /**
     * Shortcut to check privilege.
     *
     * @param string $rule   The rule to execute.
     * @param string $module The module name.
     *
     * @return boolean True, if user has access.
     */
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
     * @param string $categoryId the category id to check.
     *
     * @return boolean True, if user has access.
     */
    public static function checkCategoryAccess($categoryID)
    {
        try {
            if (!isset($categoryID) || is_null($categoryID)) {
                $_SESSION['lasturlcategory'] = $_SERVER['REQUEST_URI'];
                return true;
            }

            $user = getService('security.context')->getToken()->getUser();

            if (is_null($user) || $user == 'anon.') {
                return false;
            }

            if ($user->isMaster() || $user->isAdmin()) {
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
     * Checks if the current user has an acl.
     *
     * @param string $rule   The rule to execute.
     * @param string $module The module name.
     *
     * @return boolean True, if user has an acl.
     */
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
     * Checks if the current user has access to one privilege and category.
     *
     * @param string $privilege  The privilege token.
     * @param string $categoryID The category id.
     *
     * @return boolean True, if the user has access.
     */
    public static function checkPrivileges($privilege, $categoryID = null)
    {
        try {
            if (self::isMaster()) {
                return true;
            }

            if (self::isAdmin() && ($privilege !='ONLY_MASTERS')) {
                return true;
            }

            $isGranted = false;
            if (getService('security.context')->getToken()) {
                $user = getService('security.context')->getToken()->getUser();

                if ($user && $user !== 'anon.') {
                    $isGranted = in_array(
                        $privilege,
                        $user->getRoles()
                    );
                }

            }

            if ($isGranted
                && (!is_null($categoryID)
                    && self::checkCategoryAccess($categoryID)
                )
            ) {
                return false;
            }

            return $isGranted;
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Performs the actions of denying a user action.
     *
     * @param string $message the message to show to the user.
     */
    public static function deny($message = 'Acceso no permitido')
    {
        if (strlen($message) > 0) {
            $message = new Message($message, 'error');
            $message->push();
        }

        m::add(_("Sorry, you don't have enough privileges"));
        forward('/admin/');
    }

    /**
     * Check if the user is an Administrator.
     *
     * @return boolean True, if the user is in the Administrator group.
     */
    public static function isAdmin()
    {
        if (getService('security.context')->getToken()) {
            $user = getService('security.context')->getToken()->getUser();

            if ($user && $user != 'anon.') {
                return $user->isAdmin();
            }
        }

        return false;
    }

    /**
     * Checks a privilege.
     *
     * @param string $rule   The acl to check.
     * @param string $module The module to check.
     *
     * @throws AclNotAllowed If the user has no access to the given ACL.
     */
    public static function isGranted($rule, $module = null)
    {

        if (!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }

        if (!self::checkPrivileges($rule)) {
            throw new \Onm\Security\Exception\AccessDeniedException();
        }
    }

    /**
     * Checks if the user is a Master.
     *
     * @return boolean True, if the user is in the Master group.
     */
    public static function isMaster()
    {
        if (getService('security.context')->getToken()) {
            $user = getService('security.context')->getToken()->getUser();

            if ($user && $user != 'anon.') {
                return $user->isMaster();
            }
        }

        return false;
    }
}
