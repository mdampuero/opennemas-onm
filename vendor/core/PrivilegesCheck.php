<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles all the operations with Privilege checking.
 *
 * @package    Onm
 * @subpackage Acl
 **/
class PrivilegesCheck
{
    /**
     * Checks if the current user has access to category given its id.
     *
     * @param string $categoryID the category id.
     *
     * @return boolean true if the user has access
     **/
    public static function CheckAccessCategories($categoryID)
    {
        try {
            if (
                !isset($categoryID)
                || is_null($categoryID)
            ) {
                $_SESSION['lasturlcategory'] = $_SERVER['REQUEST_URI'];

                return true;
            }

            if ( isset($_SESSION['isMaster'])
                && $_SESSION['isMaster']
            ) {
                return true;
            }

            if (
                isset($_SESSION['isAdmin'])
                && $_SESSION['isAdmin']
            ) {
                return true;
            }

            if (
                !isset($_SESSION['accesscategories'])
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
     * Checks if the current user has access to one privilege and category.
     *
     * @param string $privilege  the privelege token.
     * @param string $categoryID the category id
     *
     * @return boolean true if the user has access
     **/
    public static function CheckPrivileges($privilege, $categoryID = null)
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
                || (!is_null($categoryID) && !(self::CheckAccessCategories($category)))
            ) {
                return false;
            }

        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
