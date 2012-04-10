<?php
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Routing\RouteCollection,
    Symfony\Component\Routing\Matcher\UrlMatcher,
    Symfony\Component\Routing\RequestContext,
    Symfony\Component\Routing\Route;

$routes->add(
    'admin_user_list',
    new Route(
        '/accesscontrol/users',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php')
    ),
    '/admin'
);
$routes->add(
    'admin_user_save',
    new Route(
        '/accesscontrol/user/{id}',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php', 'action' => 'save'), array(
            '_method' => 'POST',
        )
    ),
    '/admin'
);
$routes->add(
    'admin_user_show',
    new Route(
        '/accesscontrol/user/{id}',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php', 'action' => 'read')
    ),
    '/admin'
);
$routes->add(
    'admin_user_new',
    new Route(
        '/accesscontrol/users/new',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/acl_user.php', 'action' => 'new')
    ),
    '/admin'
);
$routes->add(
    'admin_system_settings',
    new Route(
        '/system/settings',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/system_settings.php')
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
    'admin_system_sqllog',
    new Route(
        '/system/sql-error-log',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/sql_error_log.php')
    ),
    '/admin'
);

$routes->add(
    'admin_system_settings',
    new Route(
        '/system/settings',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/system_settings.php')
    ),
    '/admin'
);

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
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/login.php')
    ),
    '/admin'
);
$routes->add(
    'admin_login',
    new Route(
        '/login',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/login.php', 'action' => 'login'),
        array('_method' => 'POST')
    ),
    '/admin'
);
$routes->add(
    'admin_logout',
    new Route(
        '/logout',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/logout.php')
    ),
    '/admin'
);
$routes->add(
    'admin_welcome',
    new Route(
        '/',
        array('_controllerfile' => APP_PATH.'/Backend/Controllers/wellcome.php')
    ),
    '/admin'
);


