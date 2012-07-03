<?php
use Symfony\Component\Routing\Route;

// Frontpage management routes
$routes->add(
    'admin_frontpage_list',
    new Route(
        '/frontpages/{category}',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:show',
            'category' => 'home',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_frontpage_savepositions',
    new Route(
        '/frontpages/{category}/save-positions',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:savePositions',
            'category' => 'home',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_frontpage_preview',
    new Route(
        '/frontpages/{category}/preview',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:preview',
        )
    ),
    '/admin'
);

// Static Pages controller
$routes->add(
    'admin_staticpages',
    new Route(
        '/static-pages',
        array(
            '_controller' => 'Backend:Controllers:StaticPagesController:list',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_staticpages_show',
    new Route(
        '/static-pages/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:StaticPagesController:show',
        )
    ),
    '/admin'
);


$routes->add(
    'admin_staticpages_create',
    new Route(
        '/static-pages/create',
        array('_controller' => 'Backend:Controllers:StaticPagesController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_staticpages_update',
    new Route(
        '/static-pages/{id}/update}',
        array('_controller' => 'Backend:Controllers:StaticPagesController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_staticpages_delete',
    new Route(
        '/static-pages/{id}/delete}',
        array('_controller' => 'Backend:Controllers:StaticPagesController:delete')
    ),
    '/admin'
);

// Videos controller routes
$routes->add(
    'admin_videos',
    new Route(
        '/videos',
        array('_controller' => 'Backend:Controllers:VideosController:list')
    ),
    '/admin'
);

$routes->add(
    'admin_videos_create',
    new Route(
        '/videos/create',
        array('_controller' => 'Backend:Controllers:VideosController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_videos_get_info',
    new Route(
        '/videos/get-video-information',
        array('_controller' => 'Backend:Controllers:VideosController:videoInformation')
    ),
    '/admin'
);

$routes->add(
    'admin_video_show',
    new Route(
        '/videos/{id}/show',
        array('_controller' => 'Backend:Controllers:VideosController:show')
    ),
    '/admin'
);

$routes->add(
    'admin_videos_update',
    new Route(
        '/videos/{id}/update',
        array('_controller' => 'Backend:Controllers:VideosController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_video_delete',
    new Route(
        '/videos/{id}/delete',
        array('_controller' => 'Backend:Controllers:VideosController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_videos_widget',
    new Route(
        '/videos/widget',
        array('_controller' => 'Backend:Controllers:VideosController:widget')
    ),
    '/admin'
);

$routes->add(
    'admin_videos_config',
    new Route(
        '/videos/config',
        array('_controller' => 'Backend:Controllers:VideosController:config')
    ),
    '/admin'
);

$routes->add(
    'admin_video_save_positions',
    new Route(
        '/videos/save-positions',
        array('_controller' => 'Backend:Controllers:VideosController:savePositions')
    ),
    '/admin'
);

$routes->add(
    'admin_video_toggle_availability',
    new Route(
        '/videos/{id}/toggle-availability',
        array('_controller' => 'Backend:Controllers:VideosController:toggleAvailability')
    ),
    '/admin'
);

$routes->add(
    'admin_video_toggle_favorite',
    new Route(
        '/videos/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:VideosController:toggleFavorite')
    ),
    '/admin'
);

$routes->add(
    'admin_video_toggle_inhome',
    new Route(
        '/videos/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:VideosController:toggleInHome')
    ),
    '/admin'
);

$routes->add(
    'admin_video_get_relations',
    new Route(
        '/videos/{id}/relations',
        array('_controller' => 'Backend:Controllers:VideosController:relations')
    ),
    '/admin'
);

$routes->add(
    'admin_video_batchdelete',
    new Route(
        '/videos/batch-delete',
        array('_controller' => 'Backend:Controllers:VideosController:batchDelete')
    ),
    '/admin'
);

$routes->add(
    'admin_video_batchpublish',
    new Route(
        '/videos/batch-publish',
        array('_controller' => 'Backend:Controllers:VideosController:batchPublish')
    ),
    '/admin'
);

$routes->add(
    'admin_videos_content_provider',
    new Route(
        '/videos/content-provider',
        array('_controller' => 'Backend:Controllers:VideosController:contentProvider')
    ),
    '/admin'
);


// Books controller routes
$routes->add(
    'admin_books',
    new Route(
        '/books',
        array(
            '_controller' => 'Backend:Controllers:BooksController:list',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_books_widget',
    new Route(
        '/books/widget',
        array('_controller' => 'Backend:Controllers:BooksController:widget')
    ),
    '/admin'
);

$routes->add(
    'admin_books_create',
    new Route(
        '/books/create',
        array('_controller' => 'Backend:Controllers:BooksController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_books_show',
    new Route(
        '/books/{id}/show',
        array('_controller' => 'Backend:Controllers:BooksController:show')
    ),
    '/admin'
);


$routes->add(
    'admin_books_update',
    new Route(
        '/books/{id}/update',
        array('_controller' => 'Backend:Controllers:BooksController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_books_delete',
    new Route(
        '/books/{id}/delete',
        array('_controller' => 'Backend:Controllers:BooksController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_books_save_positions',
    new Route(
        '/books/save-positions',
        array('_controller' => 'Backend:Controllers:BooksController:savePositions')
    ),
    '/admin'
);

$routes->add(
    'admin_books_toggle_availability',
    new Route(
        '/books/{id}/toggle-availability',
        array('_controller' => 'Backend:Controllers:BooksController:toggleAvailability')
    ),
    '/admin'
);

$routes->add(
    'admin_books_toggle_inhome',
    new Route(
        '/books/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:BooksController:toggleInHome')
    ),
    '/admin'
);

$routes->add(
    'admin_books_batchdelete',
    new Route(
        '/books/batch-delete',
        array('_controller' => 'Backend:Controllers:BooksController:batchDelete')
    ),
    '/admin'
);

$routes->add(
    'admin_books_batchpublish',
    new Route(
        '/books/batch-publish',
        array('_controller' => 'Backend:Controllers:BooksController:batchPublish')
    ),
    '/admin'
);


// Files controller routes
$routes->add(
    'admin_files_statistics',
    new Route(
        '/files/statistics',
        array(
            '_controller' => 'Backend:Controllers:FilesController:statistics',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_widget',
    new Route(
        '/files/widget',
        array(
            '_controller' => 'Backend:Controllers:FilesController:widget',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_create',
    new Route(
        '/files/create',
        array(
            '_controller' => 'Backend:Controllers:FilesController:create',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_show',
    new Route(
        '/files/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:FilesController:read',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_update',
    new Route(
        '/files/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:FilesController:update',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_delete',
    new Route(
        '/files/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:FilesController:delete',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_toggle_favorite',
    new Route(
        '/files/{id}/toggle-favorite',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleFavorite',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_toggle_in_home',
    new Route(
        '/files/{id}/toggle-in-home',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleInHome',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files_toggle_available',
    new Route(
        '/files/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleAvailable',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_files',
    new Route(
        '/files',
        array(
            '_controller' => 'Backend:Controllers:FilesController:list',
        )
    ),
    '/admin'
);


// Search controller routes
$routes->add(
    'admin_search',
    new Route(
        '/search',
        array('_controller' => 'Backend:Controllers:SearchController:default')
    ),
    '/admin'
);

// Keywork controller routes
$routes->add(
    'admin_keyword',
    new Route(
        '/keywords',
        array('_controller' => 'Backend:Controllers:KeywordsController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_keyword_create',
    new Route(
        '/keywords/create',
        array('_controller' => 'Backend:Controllers:KeywordsController:create')
    ),
    '/admin'
);

$routes->add(
    'admin_keyword_show',
    new Route(
        '/keywords/{id}/show',
        array('_controller' => 'Backend:Controllers:KeywordsController:show')
    ),
    '/admin'
);

$routes->add(
    'admin_keyword_update',
    new Route(
        '/keywords/{id}/update',
        array('_controller' => 'Backend:Controllers:KeywordsController:update')
    ),
    '/admin'
);

$routes->add(
    'admin_keyword_delete',
    new Route(
        '/keywords/{id}/delete',
        array('_controller' => 'Backend:Controllers:KeywordsController:delete')
    ),
    '/admin'
);

// Statistics controller routes
$routes->add(
    'admin_statistics',
    new Route(
        '/statistics',
        array('_controller' => 'Backend:Controllers:StatisticsController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_statistics_widget',
    new Route(
        '/statistics/widget',
        array('_controller' => 'Backend:Controllers:StatisticsController:getWidget')
    ),
    '/admin'
);

// Comments controller routes
$routes->add(
    'admin_comments',
    new Route(
        '/comments',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:list',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_comments_show',
    new Route(
        '/comments/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:show',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_comments_update',
    new Route(
        '/comments/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:update',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_comments_delete',
    new Route(
        '/comments/{id}/delete',
        array('_controller' => 'Backend:Controllers:CommentsController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_comments_toggle_status',
    new Route(
        '/comments/{id}/toggle-status',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:toggleStatus',
        )
    ),
    '/admin'
);

$routes->add(
    'admin_comments_batch_status',
    new Route(
        '/comments/batch-status',
        array('_controller' => 'Backend:Controllers:CommentsController:batchStatus')
    ),
    '/admin'
);

$routes->add(
    'admin_comments_batch_delete',
    new Route(
        '/comments/batch-delete',
        array('_controller' => 'Backend:Controllers:CommentsController:batchDelete')
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
        '/trash',
        array('_controller' => 'Backend:Controllers:TrashController:default')
    ),
    '/admin'
);

$routes->add(
    'admin_trash_delete',
    new Route(
        '/trash/{id}/delete',
        array('_controller' => 'Backend:Controllers:TrashController:delete')
    ),
    '/admin'
);

$routes->add(
    'admin_trash_restore',
    new Route(
        '/trash/{id}/restore',
        array('_controller' => 'Backend:Controllers:TrashController:restore')
    ),
    '/admin'
);

$routes->add(
    'admin_trash_batchdelete',
    new Route(
        '/trash/batchdelete',
        array('_controller' => 'Backend:Controllers:TrashController:batchDelete')
    ),
    '/admin'
);

$routes->add(
    'admin_trash_batchrestore',
    new Route(
        '/trash/batchrestore',
        array('_controller' => 'Backend:Controllers:TrashController:batchRestore')
    ),
    '/admin'
);

// Importer Europapress controller routes
$routes->add(
    'admin_importer_europapress',
    new Route(
        '/importer/europapress',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:list')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_europapress_config',
    new Route(
        '/importer/europapress/config',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:config')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_europapress_unlock',
    new Route(
        '/importer/europapress/unlock',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:unlock')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_europapress_sync',
    new Route(
        '/importer/europapress/sync',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:sync')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_europapress_import',
    new Route(
        '/importer/europapress/{id}/import',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:import')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_europapress_show',
    new Route(
        '/importer/europapress/{id}/show',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:show')
    ),
    '/admin'
);

// Importer Efe controller routes
$routes->add(
    'admin_importer_efe',
    new Route(
        '/importer/efe',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:list')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_config',
    new Route(
        '/importer/efe/config',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:config')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_unlock',
    new Route(
        '/importer/efe/unlock',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:unlock')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_sync',
    new Route(
        '/importer/efe/sync',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:sync')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_import',
    new Route(
        '/importer/efe/{id}/import',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:import')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_show',
    new Route(
        '/importer/efe/{id}/show',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:show')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_showattachment',
    new Route(
        '/importer/europapress/{id}/attachment/{attachment_id}',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:showAttachment')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_efe_pickcategory',
    new Route(
        '/importer/europapress/{id}/import/pickcategory',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:selectCategoryWhereToImport')
    ),
    '/admin'
);

// Importer XML file controller routes
$routes->add(
    'admin_importer_xmlfile',
    new Route(
        '/importer/xml-file',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:default')
    ),
    '/admin'
);
$routes->add(
    'admin_importer_xmlfile_config',
    new Route(
        '/importer/xml-file/config',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:config')
    ),
    '/admin'
);

$routes->add(
    'admin_importer_xmlfile_import',
    new Route(
        '/importer/xml-file/import',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:import')
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

$routes->add(
    'admin_tpl_manager_cleanfrontpage',
    new Route(
        '/system/cachemanager/cleanfrontapge',
        array('_controller' => 'Backend:Controllers:CacheManagerController:cleanFrontpage')
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
        array('_controller' => 'Backend:Controllers:AclUserController:list')
    ),
    '/admin'
);

$routes->add(
    'admin_acl_user_show',
    new Route(
        '/acl/user/{id}/show',
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

