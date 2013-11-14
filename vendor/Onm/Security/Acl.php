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
namespace Onm\Security;

/**
 * Class for handling user access to modules, actions and categories in backend
 *
 * @package    Onm_Acl
 */
class Acl
{
    /**
     * Checks a privilege
     *
     * @param string $rule the acl to check
     * @param string $module the module to check
     *
     * @throws Onm\Exception\AclNotAllowed If the user has no access to the given ACL
     *
     * @return void
     **/
    public static function isGranted($rule, $module = null)
    {

        if (!is_null($module)) {
            $rule = strtoupper($module) . '_' . strtoupper($rule);
        }

        if (!\Acl::checkPrivileges($rule)) {
            throw new \Onm\Security\Exception\AccessDeniedException();
        }
    }

}
