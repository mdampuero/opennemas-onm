<?php
use Symfony\Component\Routing\Route;

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

// Comments by Disqus controller routes
$routes->add(
    'admin_comments_disqus',
    new Route(
        '/comments/disqus',
        array('_controller' => 'Backend:Controllers:CommentsDisqusController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_comments_disqus_config',
    new Route(
        '/comments/disqus/config',
        array('_controller' => 'Backend:Controllers:CommentsDisqusController:config')
    ),
    '/admin'
);

// Trash controller routes
$routes->add(
    'admin_trash',
    new Route(
        '/system/trash',
        array('_controller' => 'Backend:Controllers:TrashController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_trash_delete',
    new Route(
        '/system/trash/{id}/delete',
        array('_controller' => 'Backend:Controllers:TrashController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_trash_batchdelete',
    new Route(
        '/system/trash/batchdelete',
        array('_controller' => 'Backend:Controllers:TrashController:batchDelete')
    ),
    '/admin'
);

// Template cache controller routes
$routes->add(
    'admin_tpl_manager',
    new Route(
        '/system/cachemanager',
        array('_controller' => 'Backend:Controllers:CacheManagerController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_tpl_manager_config',
    new Route(
        '/system/cachemanager/config',
        array('_controller' => 'Backend:Controllers:CacheManagerController:config')
    ),
    '/admin'
);

$routes->add(
    'admin_tpl_manager_refresh',
    new Route(
        '/system/cachemanager/refresh',
        array('_controller' => 'Backend:Controllers:CacheManagerController:refresh')
    ),
    '/admin'
);

$routes->add(
    'admin_tpl_manager_update',
    new Route(
        '/system/cachemanager/update',
        array('_controller' => 'Backend:Controllers:CacheManagerController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_tpl_manager_delete',
    new Route(
        '/system/cachemanager/delete',
        array('_controller' => 'Backend:Controllers:CacheManagerController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_tpl_manager_deleteall',
    new Route(
        '/system/cachemanager/deleteall',
        array('_controller' => 'Backend:Controllers:CacheManagerController:deleteAll')
    ),
    '/admin'
);


// Database error controller routes
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


// User management routes
$routes->add(
    'admin_acl_user',
    new Route(
        '/acl/users',
        array('_controller' => 'Backend:Controllers:AclUserController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_user_show',
    new Route(
        '/acl/user/show/{id}',
        array('_controller' => 'Backend:Controllers:AclUserController:show')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_user_create',
    new Route(
        '/acl/user/create',
        array('_controller' => 'Backend:Controllers:AclUserController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_user_update',
    new Route(
        '/acl/user/{id}/update',
        array('_controller' => 'Backend:Controllers:AclUserController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_user_delete',
    new Route(
        '/acl/user/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclUserController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_user_batchdelete',
    new Route(
        '/acl/users/batchdelete',
        array('_controller' => 'Backend:Controllers:AclUserController:batchDelete')
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
        '/acl/privilege/show/{id}',
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

