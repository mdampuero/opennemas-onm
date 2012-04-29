<?php
use Symfony\Component\Routing\Route;

$routes->add(
    'admin_user_list',
    new Route(
        '/acl/users',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php')
    ),
    '/admin'
);
$routes->add(
    'admin_user_save',
    new Route(
        '/acl/user/{id}',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php', 'action' => 'save'), array(
            '_method' => 'POST',
        )
    ),
    '/admin'
);
$routes->add(
    'admin_user_show',
    new Route(
        '/acl/user/{id}',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php', 'action' => 'read')
    ),
    '/admin'
);
$routes->add(
    'admin_user_new',
    new Route(
        '/acl/users/new',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php', 'action' => 'new')
    ),
    '/admin'
);

$routes->add(
    'admin_system_settings',
    new Route(
        '/system/settings',
        array('_controller' => 'Backend:Controllers:SystemSettingsController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_system_settings_save',
    new Route(
        '/system/settings/save',
        array('_controller' => 'Backend:Controllers:SystemSettingsController:save'),
        array('_method' => 'POST')
    ),
    '/admin'
);

$routes->add(
    'admin_tpl_manager',
    new Route(
        '/system/cachemanager',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/tpl_manager.php')
    ),
    '/admin'
);

$routes->add(
    'admin_databaseerrors',
    new Route(
        '/system/databaseerrors',
        array('_controller' => 'Backend:Controllers:DatabaseErrorsController:default')
    ),
    '/admin'
);
$routes->add(
    'admin_databaseerrors_purge',
    new Route(
        '/system/databaseerrors/purge',
        array('_controller' => 'Backend:Controllers:DatabaseErrorsController:purge')
    ),
    '/admin'
);
$routes->add(
    'admin_php_status',
    new Route(
        '/system/php-status',
        array('_controller' => 'Backend:Controllers:SystemInformationController:default')
    ),
    '/admin'
);

// Privilege management routes
$routes->add(
    'admin_acl_privileges',
    new Route(
        '/acl/privileges',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:list')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_privileges_show',
    new Route(
        '/acl/privileges/show/{id}',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:show')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_privileges_create',
    new Route(
        '/acl/privileges/create',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_privileges_update',
    new Route(
        '/acl/privileges/{id}/update',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_privileges_delete',
    new Route(
        '/acl/privileges/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:delete')
    ),
    '/admin'
);

// User groups managerment routes
$routes->add(
    'admin_acl_usergroups',
    new Route(
        '/acl/usergroups',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:list')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_usergroups_show',
    new Route(
        '/acl/usergroups/show/{id}',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:show')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_usergroups_create',
    new Route(
        '/acl/usergroups/create',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_usergroups_update',
    new Route(
        '/acl/usergroups/{id}/update',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_usergroups_delete',
    new Route(
        '/acl/usergroups/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:delete')
    ),
    '/admin'
);

// Frontpage management routes
$routes->add(
    'admin_frontpage_list',
    new Route(
        '/frontpages',
        array('_controllerfile' => 'controllers/frontpagemanager/frontpagemanager.php', 'action' => 'list')
    ),
    '/admin'
);
$routes->add(
    'admin_frontpage_list_with_category',
    new Route(
        '/frontpages/{category}',
        array('_controllerfile' => 'controllers/frontpagemanager/frontpagemanager.php', 'action' => 'list')
    ),
    '/admin'
);
$routes->add(
    'admin_login_form',
    new Route(
        '/login',
        array('_controller' => 'Backend:Controllers:AuthenticationController:default')
    ),
    '/admin'
);
$routes->add(
    'admin_login_processform',
    new Route(
        '/login/process',
        array('_controller' => 'Backend:Controllers:AuthenticationController:processform'),
        array('_method' => 'POST')
    ),
    '/admin'
);
$routes->add(
    'admin_logout',
    new Route(
        '/logout',
        array('_controller' => 'Backend:Controllers:AuthenticationController:logout')
    ),
    '/admin'
);
$routes->add(
    'admin_welcome',
    new Route(
        '/',
        array('_controller' => 'Backend:Controllers:WelcomeController:default')
    ),
    '/admin'
);

