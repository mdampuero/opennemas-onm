<?php
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

// Load the available route collection
$routes = new RouteCollection();

$adminRoutes = new RouteCollection();

// Common content management routes
$adminRoutes->add(
    'admin_content_set_available',
    new Route(
        '/content/set-available',
        array(
            '_controller' => 'Backend:Controllers:ContentController:setAvailable',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_content_set_draft',
    new Route(
        '/content/set-draft',
        array(
            '_controller' => 'Backend:Controllers:ContentController:setDraft',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_content_toggle_available',
    new Route(
        '/content/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:ContentController:toggleAvailable',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_content_set_archived',
    new Route(
        '/content/set-archived',
        array(
            '_controller' => 'Backend:Controllers:ContentController:setArchived',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_content_toggle_suggested',
    new Route(
        '/content/toggle-suggested',
        array(
            '_controller' => 'Backend:Controllers:ContentController:toggleSuggested',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_content_quick_info',
    new Route(
        '/content/quick-info',
        array(
            '_controller' => 'Backend:Controllers:ContentController:quickInfo',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_content_send_to_trash',
    new Route(
        '/content/send-to-trash',
        array(
            '_controller' => 'Backend:Controllers:ContentController:sendToTrash',
        )
    ),
    '/admin'
);

// Frontpage management routes
$adminRoutes->add(
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

$adminRoutes->add(
    'admin_frontpage_savepositions',
    new Route(
        '/frontpage/save-positions',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:savePositions'
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_frontpage_preview',
    new Route(
        '/frontpages/{category}/preview',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:preview',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_frontpage_pick_layout',
    new Route(
        '/frontpages/{category}/pick-layout',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:pickLayout',
        )
    ),
    '/admin'
);

// Static Pages controller
$adminRoutes->add(
    'admin_staticpages',
    new Route(
        '/static-pages',
        array(
            '_controller' => 'Backend:Controllers:StaticPagesController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_show',
    new Route(
        '/static-pages/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:StaticPagesController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_create',
    new Route(
        '/static-pages/create',
        array('_controller' => 'Backend:Controllers:StaticPagesController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_update',
    new Route(
        '/static-pages/{id}/update',
        array('_controller' => 'Backend:Controllers:StaticPagesController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_delete',
    new Route(
        '/static-pages/{id}/delete',
        array('_controller' => 'Backend:Controllers:StaticPagesController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_toggle_available',
    new Route(
        '/static-pages/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:StaticPagesController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_build_slug',
    new Route(
        '/static-pages/{id}/build-slug',
        array('_controller' => 'Backend:Controllers:StaticPagesController:buildSlug')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_staticpages_clean_metadata',
    new Route(
        '/static-pages/{id}/clean_metadata',
        array('_controller' => 'Backend:Controllers:StaticPagesController:cleanMetadata')
    ),
    '/admin'
);

# Widget manager routes
$adminRoutes->add(
    'admin_widgets',
    new Route(
        '/widgets',
        array('_controller' => 'Backend:Controllers:WidgetsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_widget_show',
    new Route(
        '/widget/{id}/show',
        array('_controller' => 'Backend:Controllers:WidgetsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_widget_delete',
    new Route(
        '/widget/{id}/delete',
        array('_controller' => 'Backend:Controllers:WidgetsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_widget_create',
    new Route(
        '/widget/create',
        array('_controller' => 'Backend:Controllers:WidgetsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_widget_update',
    new Route(
        '/widget/{id}/update',
        array('_controller' => 'Backend:Controllers:WidgetsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_widget_toogle_available',
    new Route(
        '/widget/{id}/toogle_available',
        array('_controller' => 'Backend:Controllers:WidgetsController:toogleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_widgets_content_provider',
    new Route(
        '/widget/content-provider',
        array('_controller' => 'Backend:Controllers:WidgetsController:contentProvider')
    ),
    '/admin'
);

// Menu manager routes
$adminRoutes->add(
    'admin_menus',
    new Route(
        '/menus',
        array('_controller' => 'Backend:Controllers:MenusController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_menu_show',
    new Route(
        '/menu/{id}/show',
        array('_controller' => 'Backend:Controllers:MenusController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_menu_create',
    new Route(
        '/menu/create',
        array('_controller' => 'Backend:Controllers:MenusController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_menu_update',
    new Route(
        '/menu/{id}/update',
        array('_controller' => 'Backend:Controllers:MenusController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_menu_delete',
    new Route(
        '/menu/{id}/delete',
        array('_controller' => 'Backend:Controllers:MenusController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_menu_batchdelete',
    new Route(
        '/menus/batchdelete',
        array('_controller' => 'Backend:Controllers:MenusController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_instance_sync',
    new Route(
        '/instance-sync',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_instance_sync_create',
    new Route(
        '/instance-sync/create',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_instance_sync_fetch_categories',
    new Route(
        '/instance-sync/fetch-categories',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:fetchCategories')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_instance_sync_show',
    new Route(
        '/instance-sync/show',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_instance_sync_delete',
    new Route(
        '/instance-sync/delete',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:delete')
    ),
    '/admin'
);

// Letter manager routes
$adminRoutes->add(
    'admin_polls',
    new Route(
        '/polls',
        array('_controller' => 'Backend:Controllers:PollsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_polls_widget',
    new Route(
        '/polls/widget',
        array('_controller' => 'Backend:Controllers:PollsController:widget')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_create',
    new Route(
        '/polls/create',
        array('_controller' => 'Backend:Controllers:PollsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_show',
    new Route(
        '/poll/{id}/show',
        array('_controller' => 'Backend:Controllers:PollsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_update',
    new Route(
        '/poll/{id}/update',
        array('_controller' => 'Backend:Controllers:PollsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_delete',
    new Route(
        '/poll/{id}/delete',
        array('_controller' => 'Backend:Controllers:PollsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_delete',
    new Route(
        '/poll/{id}/delete',
        array('_controller' => 'Backend:Controllers:PollsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_polls_config',
    new Route(
        '/polls/config',
        array('_controller' => 'Backend:Controllers:PollsController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_polls_batchpublish',
    new Route(
        '/polls/batch-publish',
        array('_controller' => 'Backend:Controllers:PollsController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_polls_batchdelete',
    new Route(
        '/polls/batch-delete',
        array('_controller' => 'Backend:Controllers:PollsController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_toggleavailable',
    new Route(
        '/poll/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:PollsController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_togglefavorite',
    new Route(
        '/poll/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:PollsController:toggleFavorite')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_poll_toggleinhome',
    new Route(
        '/poll/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:PollsController:toggleInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_polls_content_provider_related',
    new Route(
        '/polls/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:PollsController:contentProviderRelated',
        )
    ),
    '/admin'
);

// Ads manager routes
$adminRoutes->add(
    'admin_ads',
    new Route(
        '/ads',
        array('_controller' => 'Backend:Controllers:AdsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ad_create',
    new Route(
        '/ads/create',
        array('_controller' => 'Backend:Controllers:AdsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ad_show',
    new Route(
        '/ad/{id}/show',
        array('_controller' => 'Backend:Controllers:AdsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ad_update',
    new Route(
        '/ads/{id}/update',
        array('_controller' => 'Backend:Controllers:AdsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ad_delete',
    new Route(
        '/ad/{id}/delete',
        array('_controller' => 'Backend:Controllers:AdsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ads_batchpublish',
    new Route(
        '/ads/batch-publish',
        array('_controller' => 'Backend:Controllers:AdsController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ads_batchdelete',
    new Route(
        '/ads/batch-delete',
        array('_controller' => 'Backend:Controllers:AdsController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ads_content_provider',
    new Route(
        '/ads/content-provider',
        array('_controller' => 'Backend:Controllers:AdsController:contentProvider')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ad_toggleavailable',
    new Route(
        '/ads/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:AdsController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_ads_config',
    new Route(
        '/ads/config',
        array('_controller' => 'Backend:Controllers:AdsController:config')
    ),
    '/admin'
);

// Special manager routes
$adminRoutes->add(
    'admin_specials',
    new Route(
        '/specials',
        array('_controller' => 'Backend:Controllers:SpecialsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_specials_widget',
    new Route(
        '/specials/widget',
        array('_controller' => 'Backend:Controllers:SpecialsController:widget')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_create',
    new Route(
        '/special/create',
        array('_controller' => 'Backend:Controllers:SpecialsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_show',
    new Route(
        '/special/{id}/show',
        array('_controller' => 'Backend:Controllers:SpecialsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_update',
    new Route(
        '/special/{id}/update',
        array('_controller' => 'Backend:Controllers:SpecialsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_delete',
    new Route(
        '/special/{id}/delete',
        array('_controller' => 'Backend:Controllers:SpecialsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_widget_save_positions',
    new Route(
        '/specials/widget/save-positions',
        array('_controller' => 'Backend:Controllers:SpecialsController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_specials_config',
    new Route(
        '/specials/config',
        array('_controller' => 'Backend:Controllers:SpecialsController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_toggleavailable',
    new Route(
        '/special/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_togglefavorite',
    new Route(
        '/special/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleFavorite')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_togglefavorite',
    new Route(
        '/special/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleFavorite')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_special_toggleinhome',
    new Route(
        '/special/{id}/toggle-in-home',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_specials_batchpublish',
    new Route(
        '/special/batch-publish',
        array('_controller' => 'Backend:Controllers:SpecialsController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_specials_batchdelete',
    new Route(
        '/special/batch-delete',
        array('_controller' => 'Backend:Controllers:SpecialsController:batchDelete')
    ),
    '/admin'
);

// Letter manager routes
$adminRoutes->add(
    'admin_letters',
    new Route(
        '/letters',
        array('_controller' => 'Backend:Controllers:LettersController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letter_create',
    new Route(
        '/letter/create',
        array('_controller' => 'Backend:Controllers:LettersController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letter_show',
    new Route(
        '/letter/{id}/show',
        array('_controller' => 'Backend:Controllers:LettersController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letter_update',
    new Route(
        '/letter/{id}/update',
        array('_controller' => 'Backend:Controllers:LettersController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letter_delete',
    new Route(
        '/letter/{id}/delete',
        array('_controller' => 'Backend:Controllers:LettersController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letter_toggleavailable',
    new Route(
        '/letter/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:LettersController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letters_batchpublish',
    new Route(
        '/letter/batch-publish',
        array('_controller' => 'Backend:Controllers:LettersController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letters_batchdelete',
    new Route(
        '/letter/batch-delete',
        array('_controller' => 'Backend:Controllers:LettersController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_letters_content_list_provider',
    new Route(
        '/letters/content-list-provider',
        array('_controller' => 'Backend:Controllers:LettersController:contentListProvider')
    ),
    '/admin'
);

// Category manager routes
$adminRoutes->add(
    'admin_categories',
    new Route(
        '/categories',
        array('_controller' => 'Backend:Controllers:CategoriesController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_categories_config',
    new Route(
        '/categories/config',
        array('_controller' => 'Backend:Controllers:CategoriesController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_create',
    new Route(
        '/category/create',
        array('_controller' => 'Backend:Controllers:CategoriesController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_show',
    new Route(
        '/category/{id}/show',
        array('_controller' => 'Backend:Controllers:CategoriesController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_update',
    new Route(
        '/category/{id}/update',
        array('_controller' => 'Backend:Controllers:CategoriesController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_delete',
    new Route(
        '/category/{id}/delete',
        array('_controller' => 'Backend:Controllers:CategoriesController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_empty',
    new Route(
        '/category/{id}/empty',
        array('_controller' => 'Backend:Controllers:CategoriesController:empty')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_toggleavailable',
    new Route(
        '/category/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:CategoriesController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_category_togglerss',
    new Route(
        '/category/{id}/toggle-rss',
        array('_controller' => 'Backend:Controllers:CategoriesController:toggleRss')
    ),
    '/admin'
);

// Image manager routes
$adminRoutes->add(
    'admin_images_statistics',
    new Route(
        '/images/statistics',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:statistics',
        )
    ),
    '/admin'
);
$adminRoutes->add(
    'admin_images_search',
    new Route(
        '/images/search',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:search',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_images',
    new Route(
        '/images',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_image_new',
    new Route(
        '/images/new',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:new',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_image_show',
    new Route(
        '/images/show',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_image_create',
    new Route(
        '/image/create',
        array('_controller' => 'Backend:Controllers:ImagesController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_image_update',
    new Route(
        '/image/update',
        array('_controller' => 'Backend:Controllers:ImagesController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_image_delete',
    new Route(
        '/image/{id}/delete',
        array('_controller' => 'Backend:Controllers:ImagesController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_images_batchdelete',
    new Route(
        '/images/batchdelete',
        array('_controller' => 'Backend:Controllers:ImagesController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_images_config',
    new Route(
        '/images/config',
        array('_controller' => 'Backend:Controllers:ImagesController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_images_content_provider_gallery',
    new Route(
        '/images/content-provider-gallery',
        array('_controller' => 'Backend:Controllers:ImagesController:contentProviderGallery')
    ),
    '/admin'
);

// Videos controller routes
$adminRoutes->add(
    'admin_videos',
    new Route(
        '/videos',
        array('_controller' => 'Backend:Controllers:VideosController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_create',
    new Route(
        '/videos/create',
        array('_controller' => 'Backend:Controllers:VideosController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_get_info',
    new Route(
        '/videos/get-video-information',
        array('_controller' => 'Backend:Controllers:VideosController:videoInformation')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_show',
    new Route(
        '/videos/{id}/show',
        array('_controller' => 'Backend:Controllers:VideosController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_update',
    new Route(
        '/videos/{id}/update',
        array('_controller' => 'Backend:Controllers:VideosController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_delete',
    new Route(
        '/videos/{id}/delete',
        array('_controller' => 'Backend:Controllers:VideosController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_widget',
    new Route(
        '/videos/widget',
        array('_controller' => 'Backend:Controllers:VideosController:widget')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_config',
    new Route(
        '/videos/config',
        array('_controller' => 'Backend:Controllers:VideosController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_save_positions',
    new Route(
        '/videos/save-positions',
        array('_controller' => 'Backend:Controllers:VideosController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_toggle_available',
    new Route(
        '/videos/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:VideosController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_toggle_favorite',
    new Route(
        '/videos/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:VideosController:toggleFavorite')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_toggle_inhome',
    new Route(
        '/videos/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:VideosController:toggleInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_get_relations',
    new Route(
        '/videos/{id}/relations',
        array('_controller' => 'Backend:Controllers:VideosController:relations')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_batchdelete',
    new Route(
        '/videos/batch-delete',
        array('_controller' => 'Backend:Controllers:VideosController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_video_batchpublish',
    new Route(
        '/videos/batch-publish',
        array('_controller' => 'Backend:Controllers:VideosController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_content_provider',
    new Route(
        '/videos/content-provider',
        array('_controller' => 'Backend:Controllers:VideosController:contentProvider')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_content_provider_related',
    new Route(
        '/videos/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:VideosController:contentProviderRelated',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_videos_content_provider_gallery',
    new Route(
        '/videos/content-provider-gallery',
        array('_controller' => 'Backend:Controllers:VideosController:contentProviderGallery')
    ),
    '/admin'
);

// Album controller routes
$adminRoutes->add(
    'admin_albums',
    new Route(
        '/albums',
        array('_controller' => 'Backend:Controllers:AlbumsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_albums_config',
    new Route(
        '/albums/config',
        array('_controller' => 'Backend:Controllers:AlbumsController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_albums_widget',
    new Route(
        '/albums/widget',
        array('_controller' => 'Backend:Controllers:AlbumsController:widget')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_create',
    new Route(
        '/album/create',
        array('_controller' => 'Backend:Controllers:AlbumsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_update',
    new Route(
        '/album/{id}/update',
        array('_controller' => 'Backend:Controllers:AlbumsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_show',
    new Route(
        '/albums/{id}/show',
        array('_controller' => 'Backend:Controllers:AlbumsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_delete',
    new Route(
        '/albums/{id}/delete',
        array('_controller' => 'Backend:Controllers:AlbumsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_toggle_available',
    new Route(
        '/album/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:AlbumsController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_toggle_favorite',
    new Route(
        '/album/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:AlbumsController:toggleFavorite')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_toggle_inhome',
    new Route(
        '/album/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:AlbumsController:toggleInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_batchdelete',
    new Route(
        '/albums/batch-delete',
        array('_controller' => 'Backend:Controllers:AlbumsController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_album_batchpublish',
    new Route(
        '/albums/batch-publish',
        array('_controller' => 'Backend:Controllers:AlbumsController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_albums_savepositions',
    new Route(
        '/albums/save-positions',
        array('_controller' => 'Backend:Controllers:CoversController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_albums_content_provider',
    new Route(
        '/albums/content-provider',
        array('_controller' => 'Backend:Controllers:AlbumsController:contentProvider')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_albums_content_provider_related',
    new Route(
        '/albums/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:AlbumsController:contentProviderRelated',
        )
    ),
    '/admin'
);

// Covers controller routes
$adminRoutes->add(
    'admin_covers',
    new Route(
        '/covers',
        array('_controller' => 'Backend:Controllers:CoversController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_create',
    new Route(
        '/cover/create',
        array('_controller' => 'Backend:Controllers:CoversController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_show',
    new Route(
        '/covers/{id}/show',
        array('_controller' => 'Backend:Controllers:CoversController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_update',
    new Route(
        '/cover/{id}/update',
        array('_controller' => 'Backend:Controllers:CoversController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_toggleavailable',
    new Route(
        '/cover/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:CoversController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_togglefavorite',
    new Route(
        '/cover/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:CoversController:toggleFavorite')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_toggleinhome',
    new Route(
        '/cover/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:CoversController:toggleInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_covers_batchpublish',
    new Route(
        '/covers/batch-publish',
        array('_controller' => 'Backend:Controllers:CoversController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_covers_batchdelete',
    new Route(
        '/covers/batch-delete',
        array('_controller' => 'Backend:Controllers:CoversController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_covers_savepositions',
    new Route(
        '/covers/save-positions',
        array('_controller' => 'Backend:Controllers:CoversController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_cover_delete',
    new Route(
        '/cover/{id}/delete',
        array('_controller' => 'Backend:Controllers:CoversController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_covers_widget',
    new Route(
        '/cover/widget',
        array('_controller' => 'Backend:Controllers:CoversController:widget')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_covers_config',
    new Route(
        '/cover/config',
        array('_controller' => 'Backend:Controllers:CoversController:config')
    ),
    '/admin'
);

// Books controller routes
$adminRoutes->add(
    'admin_books',
    new Route(
        '/books',
        array(
            '_controller' => 'Backend:Controllers:BooksController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_widget',
    new Route(
        '/books/widget',
        array(
            '_controller' => 'Backend:Controllers:BooksController:widget',
            'category'    => 'widget',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_create',
    new Route(
        '/books/create',
        array('_controller' => 'Backend:Controllers:BooksController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_show',
    new Route(
        '/books/{id}/show',
        array('_controller' => 'Backend:Controllers:BooksController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_update',
    new Route(
        '/books/{id}/update',
        array('_controller' => 'Backend:Controllers:BooksController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_delete',
    new Route(
        '/books/{id}/delete',
        array('_controller' => 'Backend:Controllers:BooksController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_save_positions',
    new Route(
        '/books/save-positions',
        array('_controller' => 'Backend:Controllers:BooksController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_toggle_available',
    new Route(
        '/books/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:BooksController:toggleAvailable')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_toggle_inhome',
    new Route(
        '/books/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:BooksController:toggleInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_batchdelete',
    new Route(
        '/books/batch-delete',
        array('_controller' => 'Backend:Controllers:BooksController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_books_batchpublish',
    new Route(
        '/books/batch-publish',
        array('_controller' => 'Backend:Controllers:BooksController:batchPublish')
    ),
    '/admin'
);

// Files controller routes
$adminRoutes->add(
    'admin_files',
    new Route(
        '/files',
        array(
            '_controller' => 'Backend:Controllers:FilesController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_statistics',
    new Route(
        '/files/statistics',
        array(
            '_controller' => 'Backend:Controllers:FilesController:statistics',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_widget',
    new Route(
        '/files/widget',
        array(
            '_controller' => 'Backend:Controllers:FilesController:widget',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_create',
    new Route(
        '/files/create',
        array(
            '_controller' => 'Backend:Controllers:FilesController:create',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_show',
    new Route(
        '/files/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:FilesController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_update',
    new Route(
        '/files/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:FilesController:update',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_delete',
    new Route(
        '/files/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:FilesController:delete',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_toggle_favorite',
    new Route(
        '/files/{id}/toggle-favorite',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleFavorite',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_toggle_in_home',
    new Route(
        '/files/{id}/toggle-in-home',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleInHome',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_toggle_available',
    new Route(
        '/files/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleAvailable',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_file_save_positions',
    new Route(
        '/files/save-positions',
        array('_controller' => 'Backend:Controllers:FilesController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_batchdelete',
    new Route(
        '/files/batch-delete',
        array('_controller' => 'Backend:Controllers:FilesController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_batchpublish',
    new Route(
        '/files/batch-publish',
        array('_controller' => 'Backend:Controllers:FilesController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_files_content_provider',
    new Route(
        '/files/content-provider',
        array('_controller' => 'Backend:Controllers:FilesController:contentListProvider')
    ),
    '/admin'
);

// Search controller routes
$adminRoutes->add(
    'admin_search',
    new Route(
        '/search',
        array('_controller' => 'Backend:Controllers:SearchController:default')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_search_content_provider',
    new Route(
        '/search/content-provider',
        array('_controller' => 'Backend:Controllers:SearchController:contentProvider')
    ),
    '/admin'
);

// Keywork controller routes
$adminRoutes->add(
    'admin_newsletters',
    new Route(
        '/newsletters',
        array('_controller' => 'Backend:Controllers:NewsletterController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_create',
    new Route(
        '/newsletter/create',
        array('_controller' => 'Backend:Controllers:NewsletterController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_show_contents',
    new Route(
        '/newsletter/{id}/contents',
        array('_controller' => 'Backend:Controllers:NewsletterController:showContents')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_save_contents',
    new Route(
        '/newsletter/save-contents',
        array('_controller' => 'Backend:Controllers:NewsletterController:saveContents')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_preview',
    new Route(
        '/newsletter/{id}/preview',
        array('_controller' => 'Backend:Controllers:NewsletterController:preview')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_save_html',
    new Route(
        '/newsletter/{id}/save-html',
        array('_controller' => 'Backend:Controllers:NewsletterController:saveHtmlContent')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_pick_recipients',
    new Route(
        '/newsletter/{id}/pick-recipients',
        array('_controller' => 'Backend:Controllers:NewsletterController:pickRecipients')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_send',
    new Route(
        '/newsletter/{id}/send',
        array('_controller' => 'Backend:Controllers:NewsletterController:send')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_delete',
    new Route(
        '/newsletter/{id}/delete',
        array('_controller' => 'Backend:Controllers:NewsletterController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_config',
    new Route(
        '/newsletters/config',
        array('_controller' => 'Backend:Controllers:NewsletterController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptors',
    new Route(
        '/newsletters/subscriptors',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_create',
    new Route(
        '/newsletters/subscriptor/create',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_show',
    new Route(
        '/newsletters/subscriptor/{id}/show',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_update',
    new Route(
        '/newsletters/subscriptor/{id}/update',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_delete',
    new Route(
        '/newsletters/subscriptor/{id}/delete',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_toggle_subscription',
    new Route(
        '/newsletters/subscriptor/{id}/toggle-subscription',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:toggleSubscription')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_toggle_activated',
    new Route(
        '/newsletters/subscriptor/{id}/toggle-activated',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:toggleActivated')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_batch_delete',
    new Route(
        '/newsletters/subscriptors/batch-delete',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_newsletter_subscriptors_batch_subscribe',
    new Route(
        '/newsletters/subscriptors/batch-subscribe',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:batchSubscribe')
    ),
    '/admin'
);

// Keywork controller routes
$adminRoutes->add(
    'admin_keywords',
    new Route(
        '/keywords',
        array('_controller' => 'Backend:Controllers:KeywordsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_keyword_create',
    new Route(
        '/keywords/create',
        array('_controller' => 'Backend:Controllers:KeywordsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_keyword_show',
    new Route(
        '/keywords/{id}/show',
        array('_controller' => 'Backend:Controllers:KeywordsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_keyword_update',
    new Route(
        '/keywords/{id}/update',
        array('_controller' => 'Backend:Controllers:KeywordsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_keyword_delete',
    new Route(
        '/keywords/{id}/delete',
        array('_controller' => 'Backend:Controllers:KeywordsController:delete')
    ),
    '/admin'
);

// Statistics controller routes
$adminRoutes->add(
    'admin_statistics',
    new Route(
        '/statistics',
        array('_controller' => 'Backend:Controllers:StatisticsController:default')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_statistics_widget',
    new Route(
        '/statistics/widget',
        array('_controller' => 'Backend:Controllers:StatisticsController:getWidget')
    ),
    '/admin'
);

// Article controller routes
$adminRoutes->add(
    'admin_articles',
    new Route(
        '/articles',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_article_create',
    new Route(
        '/article/create',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:create',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_article_show',
    new Route(
        '/article/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_article_delete',
    new Route(
        '/article/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:delete',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_article_update',
    new Route(
        '/article/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:update',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_article_toggleavailable',
    new Route(
        '/article/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:toggleAvailable',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_article_preview',
    new Route(
        '/article/preview',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:preview',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_articles_content_provider_suggested',
    new Route(
        '/articles/content-provider-suggested',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderSuggested',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_articles_content_provider_category',
    new Route(
        '/articles/content-provider-category',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderCategory',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_articles_content_provider_related',
    new Route(
        '/articles/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderRelated',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_articles_content_provider_in_frontpage',
    new Route(
        '/articles/content-provider-in-frontpage',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderInFrontpage',
        )
    ),
    '/admin'
);


$adminRoutes->add(
    'admin_articles_batchdelete',
    new Route(
        '/articles/batch-delete',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:batchDelete',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_articles_batchpublish',
    new Route(
        '/articles/batch-publish',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:batchPublish',
        )
    ),
    '/admin'
);


// Opinion controller routes
$adminRoutes->add(
    'admin_opinions',
    new Route(
        '/opinions',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_frontpage',
    new Route(
        '/opinions/frontpage',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:frontpage',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_create',
    new Route(
        '/opinion/create',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:create',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_show',
    new Route(
        '/opinion/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_update',
    new Route(
        '/opinion/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:update',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_delete',
    new Route(
        '/opinion/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:delete',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_toggleavailable',
    new Route(
        '/opinion/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:toggleAvailable',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_toggleinhome',
    new Route(
        '/opinion/{id}/toggle-inhome',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:toggleInHome',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_togglefavorite',
    new Route(
        '/opinion/{id}/toggle-favorite',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:toggleFavorite',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_batch_delete',
    new Route(
        '/opinions/batch-delete',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_batch_publish',
    new Route(
        '/opinions/batch-publish',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchPublish')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_batch_inhome',
    new Route(
        '/opinions/batch-inhome',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_batch_inhome',
    new Route(
        '/opinions/save-positions',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchInHome')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_savepositions',
    new Route(
        '/opinions/batch-inhome',
        array('_controller' => 'Backend:Controllers:OpinionsController:savePositions')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_content_provider',
    new Route(
        '/opinions/content-provider',
        array('_controller' => 'Backend:Controllers:OpinionsController:contentProvider')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_content_provider_related',
    new Route(
        '/opinions/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:contentProviderRelated',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinions_config',
    new Route(
        '/opinions/config',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:config',
        )
    ),
    '/admin'
);

// Opinion author controller routes
$adminRoutes->add(
    'admin_opinion_authors',
    new Route(
        '/opinion/authors',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_author_show',
    new Route(
        '/opinion/author/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_author_create',
    new Route(
        '/opinion/authors/create',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:create',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_author_delete',
    new Route(
        '/opinion/author/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:delete',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_author_update',
    new Route(
        '/opinion/author/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:update',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_author_delete',
    new Route(
        '/opinion/author/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:delete',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_opinion_author_batchdelete',
    new Route(
        '/opinion/authors/batch-delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:batchDelete',
        )
    ),
    '/admin'
);

// Comments controller routes
$adminRoutes->add(
    'admin_comments',
    new Route(
        '/comments',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:list',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_show',
    new Route(
        '/comments/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:show',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_update',
    new Route(
        '/comments/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:update',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_delete',
    new Route(
        '/comments/{id}/delete',
        array('_controller' => 'Backend:Controllers:CommentsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_toggle_status',
    new Route(
        '/comments/{id}/toggle-status',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:toggleStatus',
        )
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_batch_status',
    new Route(
        '/comments/batch-status',
        array('_controller' => 'Backend:Controllers:CommentsController:batchStatus')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_batch_delete',
    new Route(
        '/comments/batch-delete',
        array('_controller' => 'Backend:Controllers:CommentsController:batchDelete')
    ),
    '/admin'
);

// Comments by Disqus controller routes
$adminRoutes->add(
    'admin_comments_disqus',
    new Route(
        '/comments/disqus',
        array('_controller' => 'Backend:Controllers:CommentsDisqusController:default')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_comments_disqus_config',
    new Route(
        '/comments/disqus/config',
        array('_controller' => 'Backend:Controllers:CommentsDisqusController:config')
    ),
    '/admin'
);

// Trash controller routes
$adminRoutes->add(
    'admin_trash',
    new Route(
        '/trash',
        array('_controller' => 'Backend:Controllers:TrashController:default')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_trash_delete',
    new Route(
        '/trash/{id}/delete',
        array('_controller' => 'Backend:Controllers:TrashController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_trash_restore',
    new Route(
        '/trash/{id}/restore',
        array('_controller' => 'Backend:Controllers:TrashController:restore')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_trash_batchdelete',
    new Route(
        '/trash/batchdelete',
        array('_controller' => 'Backend:Controllers:TrashController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_trash_batchrestore',
    new Route(
        '/trash/batchrestore',
        array('_controller' => 'Backend:Controllers:TrashController:batchRestore')
    ),
    '/admin'
);

// Importer Europapress controller routes
$adminRoutes->add(
    'admin_importer_europapress',
    new Route(
        '/importer/europapress',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_europapress_config',
    new Route(
        '/importer/europapress/config',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_europapress_unlock',
    new Route(
        '/importer/europapress/unlock',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:unlock')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_europapress_sync',
    new Route(
        '/importer/europapress/sync',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:sync')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_europapress_import',
    new Route(
        '/importer/europapress/{id}/import',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:import')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_europapress_show',
    new Route(
        '/importer/europapress/{id}/show',
        array('_controller' => 'Backend:Controllers:ImporterEuropapressController:show')
    ),
    '/admin'
);

// Importer Efe controller routes
$adminRoutes->add(
    'admin_importer_efe',
    new Route(
        '/importer/efe',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_config',
    new Route(
        '/importer/efe/config',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_unlock',
    new Route(
        '/importer/efe/unlock',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:unlock')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_sync',
    new Route(
        '/importer/efe/sync',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:sync')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_import',
    new Route(
        '/importer/efe/{id}/import',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:import')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_show',
    new Route(
        '/importer/efe/{id}/show',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_showattachment',
    new Route(
        '/importer/europapress/{id}/attachment/{attachment_id}',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:showAttachment')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_efe_pickcategory',
    new Route(
        '/importer/europapress/{id}/pickcategory',
        array('_controller' => 'Backend:Controllers:ImporterEfeController:selectCategoryWhereToImport')
    ),
    '/admin'
);

// Importer XML file controller routes
$adminRoutes->add(
    'admin_importer_xmlfile',
    new Route(
        '/importer/xml-file',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:default')
    ),
    '/admin'
);
$adminRoutes->add(
    'admin_importer_xmlfile_config',
    new Route(
        '/importer/xml-file/config',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_importer_xmlfile_import',
    new Route(
        '/importer/xml-file/import',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:import')
    ),
    '/admin'
);

// Template cache controller routes
$adminRoutes->add(
    'admin_tpl_manager',
    new Route(
        '/system/cachemanager',
        array('_controller' => 'Backend:Controllers:CacheManagerController:default')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_tpl_manager_config',
    new Route(
        '/system/cachemanager/config',
        array('_controller' => 'Backend:Controllers:CacheManagerController:config')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_tpl_manager_refresh',
    new Route(
        '/system/cachemanager/refresh',
        array('_controller' => 'Backend:Controllers:CacheManagerController:refresh')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_tpl_manager_update',
    new Route(
        '/system/cachemanager/update',
        array('_controller' => 'Backend:Controllers:CacheManagerController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_tpl_manager_delete',
    new Route(
        '/system/cachemanager/delete',
        array('_controller' => 'Backend:Controllers:CacheManagerController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_tpl_manager_deleteall',
    new Route(
        '/system/cachemanager/deleteall',
        array('_controller' => 'Backend:Controllers:CacheManagerController:deleteAll')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_tpl_manager_cleanfrontpage',
    new Route(
        '/system/cachemanager/cleanfrontapge',
        array('_controller' => 'Backend:Controllers:CacheManagerController:cleanFrontpage')
    ),
    '/admin'
);

// Database error controller routes
$adminRoutes->add(
    'admin_databaseerrors',
    new Route(
        '/system/databaseerrors',
        array('_controller' => 'Backend:Controllers:DatabaseErrorsController:default')
    ),
    '/admin'
);
$adminRoutes->add(
    'admin_databaseerrors_purge',
    new Route(
        '/system/databaseerrors/purge',
        array('_controller' => 'Backend:Controllers:DatabaseErrorsController:purge')
    ),
    '/admin'
);

// User management routes
$adminRoutes->add(
    'admin_acl_user',
    new Route(
        '/acl/users',
        array('_controller' => 'Backend:Controllers:AclUserController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_create',
    new Route(
        '/acl/user/create',
        array('_controller' => 'Backend:Controllers:AclUserController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_show',
    new Route(
        '/acl/user/{id}/show',
        array('_controller' => 'Backend:Controllers:AclUserController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_update',
    new Route(
        '/acl/user/{id}/update',
        array('_controller' => 'Backend:Controllers:AclUserController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_delete',
    new Route(
        '/acl/user/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclUserController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_batchdelete',
    new Route(
        '/acl/users/batchdelete',
        array('_controller' => 'Backend:Controllers:AclUserController:batchDelete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_connected_users',
    new Route(
        '/acl/users/connected-users',
        array('_controller' => 'Backend:Controllers:AclUserController:connectedUsers')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_user_set_meta',
    new Route(
        '/acl/user/set-meta',
        array('_controller' => 'Backend:Controllers:AclUserController:setMeta')
    ),
    '/admin'
);

// Privilege management routes
$adminRoutes->add(
    'admin_acl_privileges',
    new Route(
        '/acl/privileges',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_privileges_show',
    new Route(
        '/acl/privilege/show/{id}',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_privileges_create',
    new Route(
        '/acl/privilege/create',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_privileges_update',
    new Route(
        '/acl/privilege/{id}/update',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_privileges_delete',
    new Route(
        '/acl/privilege/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:delete')
    ),
    '/admin'
);

// User groups managerment routes
$adminRoutes->add(
    'admin_acl_usergroups',
    new Route(
        '/acl/usergroups',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:list')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_usergroups_show',
    new Route(
        '/acl/usergroup/{id}/show',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:show')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_usergroups_create',
    new Route(
        '/acl/usergroup/create',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:create')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_usergroups_update',
    new Route(
        '/acl/usergroup/{id}/update',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:update')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_acl_usergroups_delete',
    new Route(
        '/acl/usergroup/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:delete')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_system_settings',
    new Route(
        '/system/settings',
        array('_controller' => 'Backend:Controllers:SystemSettingsController:default')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_system_settings_save',
    new Route(
        '/system/settings/save',
        array('_controller' => 'Backend:Controllers:SystemSettingsController:save'),
        array('_method' => 'POST')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_utils_calculate_tags',
    new Route(
        '/utils/calculate-tags',
        array('_controller' => 'Backend:Controllers:UtilsController:calculateTags')
    ),
    '/admin'
);

$adminRoutes->add(
    'admin_login_form',
    new Route(
        '/login',
        array('_controller' => 'Backend:Controllers:AuthenticationController:default')
    ),
    '/admin'
);
$adminRoutes->add(
    'admin_login_processform',
    new Route(
        '/login/process',
        array('_controller' => 'Backend:Controllers:AuthenticationController:processform'),
        array('_method' => 'POST')
    ),
    '/admin'
);
$adminRoutes->add(
    'admin_logout',
    new Route(
        '/logout',
        array('_controller' => 'Backend:Controllers:AuthenticationController:logout')
    ),
    '/admin'
);
$adminRoutes->add(
    'admin_welcome',
    new Route(
        '/',
        array('_controller' => 'Backend:Controllers:WelcomeController:default')
    ),
    '/admin'
);

$routes->addCollection($adminRoutes);

