<?php
/**
 * Defines all the routes for the manager interface
 *
 * @package  Manager
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$managerRoutes = new RouteCollection();

$managerRoutes->add(
    'manager_framework_commands',
    new Route(
        '/commands',
        array('_controller' => 'Manager:Controllers:CommandsController:list')
    )
);
$managerRoutes->add(
    'manager_framework_command_execute',
    new Route(
        '/commands/execute',
        array('_controller' => 'Manager:Controllers:CommandsController:executeCommand')
    )
);

$managerRoutes->add(
    'manager_instances',
    new Route(
        '/instances',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:list',
        )
    )
);

$managerRoutes->add(
    'manager_instances_list_export',
    new Route(
        '/instance/list-export.csv',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:listExport',
        )
    )
);

$managerRoutes->add(
    'manager_instance_show',
    new Route(
        '/instance/{id}/show',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:show',
        )
    )
);

$managerRoutes->add(
    'manager_instance_create',
    new Route(
        '/instance/create',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:create',
        )
    )
);

$managerRoutes->add(
    'manager_instance_update',
    new Route(
        '/instance/{id}/update',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:update',
        )
    )
);

$managerRoutes->add(
    'manager_instance_delete',
    new Route(
        '/instance/{id}/delete',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:delete',
        )
    )
);

$managerRoutes->add(
    'manager_instance_batch_delete',
    new Route(
        '/instance/batch-delete',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:batchDelete',
        )
    )
);

$managerRoutes->add(
    'manager_instance_toggleavailable',
    new Route(
        '/instance/{id}/toggle-available',
        array(
            '_controller' => 'Manager:Controllers:InstancesController:toggleAvailable',
        )
    )
);

$managerRoutes->add(
    'manager_framework_check_dependencies',
    new Route(
        '/framework/check-dependencies',
        array(
            '_controller' => 'Manager:Controllers:FrameworkStatusController:checkDependencies',
        )
    )
);

$managerRoutes->add(
    'manager_framework_apc',
    new Route(
        '/framework/apc',
        array(
            '_controller' => 'Manager:Controllers:FrameworkStatusController:apcStatus',
        )
    )
);

$managerRoutes->add(
    'manager_login_form',
    new Route(
        '/login',
        array('_controller' => 'Manager:Controllers:AuthenticationController:default')
    )
);
$managerRoutes->add(
    'manager_login_processform',
    new Route(
        '/login/process',
        array('_controller' => 'Manager:Controllers:AuthenticationController:processform'),
        array('_method' => 'POST')
    )
);
$managerRoutes->add(
    'manager_logout',
    new Route(
        '/logout',
        array('_controller' => 'Manager:Controllers:AuthenticationController:logout')
    )
);

// User management routes
$managerRoutes->add(
    'manager_acl_user',
    new Route(
        '/acl/users',
        array('_controller' => 'Manager:Controllers:AclUserController:list')
    )
);

$managerRoutes->add(
    'manager_acl_user_create',
    new Route(
        '/acl/user/create',
        array('_controller' => 'Manager:Controllers:AclUserController:create')
    )
);

$managerRoutes->add(
    'manager_acl_user_show',
    new Route(
        '/acl/user/{id}/show',
        array('_controller' => 'Manager:Controllers:AclUserController:show')
    )
);

$managerRoutes->add(
    'manager_acl_user_update',
    new Route(
        '/acl/user/{id}/update',
        array('_controller' => 'Manager:Controllers:AclUserController:update')
    )
);

$managerRoutes->add(
    'manager_acl_user_delete',
    new Route(
        '/acl/user/{id}/delete',
        array('_controller' => 'Manager:Controllers:AclUserController:delete')
    )
);

$managerRoutes->add(
    'manager_acl_user_batchdelete',
    new Route(
        '/acl/user/batchdelete',
        array('_controller' => 'Manager:Controllers:AclUserController:batchDelete')
    )
);

$managerRoutes->add(
    'manager_acl_user_toogle_enabled',
    new Route(
        '/acl/user/{id}/toogle-enabled',
        array('_controller' => 'Manager:Controllers:AclUserController:toogleEnabled')
    )
);

// User groups managerment routes
$managerRoutes->add(
    'manager_acl_usergroups',
    new Route(
        '/acl/usergroups',
        array('_controller' => 'Manager:Controllers:AclUserGroupsController:list')
    )
);

$managerRoutes->add(
    'manager_acl_usergroups_show',
    new Route(
        '/acl/usergroup/{id}/show',
        array('_controller' => 'Manager:Controllers:AclUserGroupsController:show')
    )
);

$managerRoutes->add(
    'manager_acl_usergroups_create',
    new Route(
        '/acl/usergroup/create',
        array('_controller' => 'Manager:Controllers:AclUserGroupsController:create')
    )
);

$managerRoutes->add(
    'manager_acl_usergroups_update',
    new Route(
        '/acl/usergroup/{id}/update',
        array('_controller' => 'Manager:Controllers:AclUserGroupsController:update')
    )
);

$managerRoutes->add(
    'manager_acl_usergroups_delete',
    new Route(
        '/acl/usergroup/{id}/delete',
        array('_controller' => 'Manager:Controllers:AclUserGroupsController:delete')
    )
);

$managerRoutes->addPrefix('/manager');

$routes->add(
    'manager_welcome',
    new Route(
        '/manager',
        array('_controller' => 'Manager:Controllers:WelcomeController:default')
    )
);

$routes->addCollection($managerRoutes);
