<?php
/**
 * Defines all the routes for backend interface
 *
 * @package  Backend
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

$adminRoutes = new RouteCollection();

// Common content management routes
$adminRoutes->add(
    'admin_content_set_available',
    new Route(
        '/content/set-available',
        array(
            '_controller' => 'Backend:Controllers:ContentController:setAvailable',
        )
    )
);

$adminRoutes->add(
    'admin_content_set_draft',
    new Route(
        '/content/set-draft',
        array(
            '_controller' => 'Backend:Controllers:ContentController:setDraft',
        )
    )
);

$adminRoutes->add(
    'admin_content_toggle_available',
    new Route(
        '/content/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:ContentController:toggleAvailable',
        )
    )
);

$adminRoutes->add(
    'admin_content_set_archived',
    new Route(
        '/content/set-archived',
        array(
            '_controller' => 'Backend:Controllers:ContentController:setArchived',
        )
    )
);

$adminRoutes->add(
    'admin_content_toggle_suggested',
    new Route(
        '/content/toggle-suggested',
        array(
            '_controller' => 'Backend:Controllers:ContentController:toggleSuggested',
        )
    )
);

$adminRoutes->add(
    'admin_content_quick_info',
    new Route(
        '/content/quick-info',
        array(
            '_controller' => 'Backend:Controllers:ContentController:quickInfo',
        )
    )
);

$adminRoutes->add(
    'admin_content_send_to_trash',
    new Route(
        '/content/send-to-trash',
        array(
            '_controller' => 'Backend:Controllers:ContentController:sendToTrash',
        )
    )
);


$adminRoutes->add(
    'admin_content_update_property',
    new Route(
        '/content/update-property',
        array(
            '_controller' => 'Backend:Controllers:ContentController:updateProperty',
        )
    )
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
    )
);

$adminRoutes->add(
    'admin_frontpage_savepositions',
    new Route(
        '/frontpage/save-positions',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:savePositions'
        )
    )
);

$adminRoutes->add(
    'admin_frontpage_preview',
    new Route(
        '/frontpages/{category}/preview',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:preview',
        )
    )
);

$adminRoutes->add(
    'admin_frontpage_get_preview',
    new Route(
        '/frontpages/{category}/get-preview',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:getPreview',
        )
    )
);

$adminRoutes->add(
    'admin_frontpage_pick_layout',
    new Route(
        '/frontpages/{category}/pick-layout',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:pickLayout',
        )
    )
);

$adminRoutes->add(
    'admin_frontpage_last_version',
    new Route(
        '/frontpages/{category}/exists-new-version',
        array(
            '_controller' => 'Backend:Controllers:FrontpagesController:lastVersion',
        )
    )
);

// Static Pages controller
$adminRoutes->add(
    'admin_staticpages',
    new Route(
        '/static-pages',
        array(
            '_controller' => 'Backend:Controllers:StaticPagesController:list',
        )
    )
);

$adminRoutes->add(
    'admin_staticpages_show',
    new Route(
        '/static-pages/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:StaticPagesController:show',
        )
    )
);

$adminRoutes->add(
    'admin_staticpages_create',
    new Route(
        '/static-pages/create',
        array('_controller' => 'Backend:Controllers:StaticPagesController:create')
    )
);

$adminRoutes->add(
    'admin_staticpages_update',
    new Route(
        '/static-pages/{id}/update',
        array('_controller' => 'Backend:Controllers:StaticPagesController:update')
    )
);

$adminRoutes->add(
    'admin_staticpages_delete',
    new Route(
        '/static-pages/{id}/delete',
        array('_controller' => 'Backend:Controllers:StaticPagesController:delete')
    )
);

$adminRoutes->add(
    'admin_staticpages_toggle_available',
    new Route(
        '/static-pages/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:StaticPagesController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_staticpages_build_slug',
    new Route(
        '/static-pages/{id}/build-slug',
        array('_controller' => 'Backend:Controllers:StaticPagesController:buildSlug')
    )
);

$adminRoutes->add(
    'admin_staticpages_clean_metadata',
    new Route(
        '/static-pages/{id}/clean_metadata',
        array('_controller' => 'Backend:Controllers:StaticPagesController:cleanMetadata')
    )
);

# Widget manager routes
$adminRoutes->add(
    'admin_widgets',
    new Route(
        '/widgets',
        array('_controller' => 'Backend:Controllers:WidgetsController:list')
    )
);

$adminRoutes->add(
    'admin_widget_show',
    new Route(
        '/widget/{id}/show',
        array('_controller' => 'Backend:Controllers:WidgetsController:show')
    )
);

$adminRoutes->add(
    'admin_widget_delete',
    new Route(
        '/widget/{id}/delete',
        array('_controller' => 'Backend:Controllers:WidgetsController:delete')
    )
);

$adminRoutes->add(
    'admin_widget_create',
    new Route(
        '/widget/create',
        array('_controller' => 'Backend:Controllers:WidgetsController:create')
    )
);

$adminRoutes->add(
    'admin_widget_update',
    new Route(
        '/widget/{id}/update',
        array('_controller' => 'Backend:Controllers:WidgetsController:update')
    )
);

$adminRoutes->add(
    'admin_widget_toogle_available',
    new Route(
        '/widget/{id}/toogle_available',
        array('_controller' => 'Backend:Controllers:WidgetsController:toogleAvailable')
    )
);

$adminRoutes->add(
    'admin_widgets_content_provider',
    new Route(
        '/widget/content-provider',
        array('_controller' => 'Backend:Controllers:WidgetsController:contentProvider')
    )
);

// Menu manager routes
$adminRoutes->add(
    'admin_menus',
    new Route(
        '/menus',
        array('_controller' => 'Backend:Controllers:MenusController:list')
    )
);

$adminRoutes->add(
    'admin_menu_show',
    new Route(
        '/menu/{id}/show',
        array('_controller' => 'Backend:Controllers:MenusController:show')
    )
);

$adminRoutes->add(
    'admin_menu_create',
    new Route(
        '/menu/create',
        array('_controller' => 'Backend:Controllers:MenusController:create')
    )
);

$adminRoutes->add(
    'admin_menu_update',
    new Route(
        '/menu/{id}/update',
        array('_controller' => 'Backend:Controllers:MenusController:update')
    )
);

$adminRoutes->add(
    'admin_menu_delete',
    new Route(
        '/menu/{id}/delete',
        array('_controller' => 'Backend:Controllers:MenusController:delete')
    )
);

$adminRoutes->add(
    'admin_menu_batchdelete',
    new Route(
        '/menus/batchdelete',
        array('_controller' => 'Backend:Controllers:MenusController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_instance_sync',
    new Route(
        '/instance-sync',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:list')
    )
);

$adminRoutes->add(
    'admin_instance_sync_create',
    new Route(
        '/instance-sync/create',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:create')
    )
);

$adminRoutes->add(
    'admin_instance_sync_fetch_categories',
    new Route(
        '/instance-sync/fetch-categories',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:fetchCategories')
    )
);

$adminRoutes->add(
    'admin_instance_sync_show',
    new Route(
        '/instance-sync/show',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:show')
    )
);

$adminRoutes->add(
    'admin_instance_sync_delete',
    new Route(
        '/instance-sync/delete',
        array('_controller' => 'Backend:Controllers:InstanceSyncController:delete')
    )
);

// Polls manager routes
$adminRoutes->add(
    'admin_polls',
    new Route(
        '/polls',
        array('_controller' => 'Backend:Controllers:PollsController:list')
    )
);

$adminRoutes->add(
    'admin_polls_widget',
    new Route(
        '/polls/widget',
        array('_controller' => 'Backend:Controllers:PollsController:widget')
    )
);

$adminRoutes->add(
    'admin_poll_create',
    new Route(
        '/polls/create',
        array('_controller' => 'Backend:Controllers:PollsController:create')
    )
);

$adminRoutes->add(
    'admin_poll_show',
    new Route(
        '/poll/{id}/show',
        array('_controller' => 'Backend:Controllers:PollsController:show')
    )
);

$adminRoutes->add(
    'admin_poll_update',
    new Route(
        '/poll/{id}/update',
        array('_controller' => 'Backend:Controllers:PollsController:update')
    )
);

$adminRoutes->add(
    'admin_poll_delete',
    new Route(
        '/poll/{id}/delete',
        array('_controller' => 'Backend:Controllers:PollsController:delete')
    )
);

$adminRoutes->add(
    'admin_poll_delete',
    new Route(
        '/poll/{id}/delete',
        array('_controller' => 'Backend:Controllers:PollsController:delete')
    )
);

$adminRoutes->add(
    'admin_polls_config',
    new Route(
        '/polls/config',
        array('_controller' => 'Backend:Controllers:PollsController:config')
    )
);

$adminRoutes->add(
    'admin_polls_batchpublish',
    new Route(
        '/polls/batch-publish',
        array('_controller' => 'Backend:Controllers:PollsController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_polls_batchdelete',
    new Route(
        '/polls/batch-delete',
        array('_controller' => 'Backend:Controllers:PollsController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_poll_toggleavailable',
    new Route(
        '/poll/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:PollsController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_poll_togglefavorite',
    new Route(
        '/poll/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:PollsController:toggleFavorite')
    )
);

$adminRoutes->add(
    'admin_poll_toggleinhome',
    new Route(
        '/poll/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:PollsController:toggleInHome')
    )
);

$adminRoutes->add(
    'admin_polls_content_provider_related',
    new Route(
        '/polls/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:PollsController:contentProviderRelated',
        )
    )
);

// Ads manager routes
$adminRoutes->add(
    'admin_ads',
    new Route(
        '/ads',
        array('_controller' => 'Backend:Controllers:AdsController:list')
    )
);

$adminRoutes->add(
    'admin_ad_create',
    new Route(
        '/ads/create',
        array('_controller' => 'Backend:Controllers:AdsController:create')
    )
);

$adminRoutes->add(
    'admin_ad_show',
    new Route(
        '/ad/{id}/show',
        array('_controller' => 'Backend:Controllers:AdsController:show')
    )
);

$adminRoutes->add(
    'admin_ad_update',
    new Route(
        '/ads/{id}/update',
        array('_controller' => 'Backend:Controllers:AdsController:update')
    )
);

$adminRoutes->add(
    'admin_ad_delete',
    new Route(
        '/ad/{id}/delete',
        array('_controller' => 'Backend:Controllers:AdsController:delete')
    )
);

$adminRoutes->add(
    'admin_ads_batchpublish',
    new Route(
        '/ads/batch-publish',
        array('_controller' => 'Backend:Controllers:AdsController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_ads_batchdelete',
    new Route(
        '/ads/batch-delete',
        array('_controller' => 'Backend:Controllers:AdsController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_ads_content_provider',
    new Route(
        '/ads/content-provider',
        array('_controller' => 'Backend:Controllers:AdsController:contentProvider')
    )
);

$adminRoutes->add(
    'admin_ad_toggleavailable',
    new Route(
        '/ads/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:AdsController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_ads_config',
    new Route(
        '/ads/config',
        array('_controller' => 'Backend:Controllers:AdsController:config')
    )
);

// Special manager routes
$adminRoutes->add(
    'admin_specials',
    new Route(
        '/specials',
        array('_controller' => 'Backend:Controllers:SpecialsController:list')
    )
);

$adminRoutes->add(
    'admin_specials_widget',
    new Route(
        '/specials/widget',
        array('_controller' => 'Backend:Controllers:SpecialsController:widget')
    )
);

$adminRoutes->add(
    'admin_special_create',
    new Route(
        '/special/create',
        array('_controller' => 'Backend:Controllers:SpecialsController:create')
    )
);

$adminRoutes->add(
    'admin_special_show',
    new Route(
        '/special/{id}/show',
        array('_controller' => 'Backend:Controllers:SpecialsController:show')
    )
);

$adminRoutes->add(
    'admin_special_update',
    new Route(
        '/special/{id}/update',
        array('_controller' => 'Backend:Controllers:SpecialsController:update')
    )
);

$adminRoutes->add(
    'admin_special_delete',
    new Route(
        '/special/{id}/delete',
        array('_controller' => 'Backend:Controllers:SpecialsController:delete')
    )
);

$adminRoutes->add(
    'admin_special_widget_save_positions',
    new Route(
        '/specials/widget/save-positions',
        array('_controller' => 'Backend:Controllers:SpecialsController:savePositions')
    )
);

$adminRoutes->add(
    'admin_specials_config',
    new Route(
        '/specials/config',
        array('_controller' => 'Backend:Controllers:SpecialsController:config')
    )
);

$adminRoutes->add(
    'admin_special_toggleavailable',
    new Route(
        '/special/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_special_togglefavorite',
    new Route(
        '/special/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleFavorite')
    )
);

$adminRoutes->add(
    'admin_special_togglefavorite',
    new Route(
        '/special/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleFavorite')
    )
);

$adminRoutes->add(
    'admin_special_toggleinhome',
    new Route(
        '/special/{id}/toggle-in-home',
        array('_controller' => 'Backend:Controllers:SpecialsController:toggleInHome')
    )
);

$adminRoutes->add(
    'admin_specials_batchpublish',
    new Route(
        '/special/batch-publish',
        array('_controller' => 'Backend:Controllers:SpecialsController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_specials_batchdelete',
    new Route(
        '/special/batch-delete',
        array('_controller' => 'Backend:Controllers:SpecialsController:batchDelete')
    )
);

// Letter manager routes
$adminRoutes->add(
    'admin_letters',
    new Route(
        '/letters',
        array('_controller' => 'Backend:Controllers:LettersController:list')
    )
);

$adminRoutes->add(
    'admin_letter_create',
    new Route(
        '/letter/create',
        array('_controller' => 'Backend:Controllers:LettersController:create')
    )
);

$adminRoutes->add(
    'admin_letter_show',
    new Route(
        '/letter/{id}/show',
        array('_controller' => 'Backend:Controllers:LettersController:show')
    )
);

$adminRoutes->add(
    'admin_letter_update',
    new Route(
        '/letter/{id}/update',
        array('_controller' => 'Backend:Controllers:LettersController:update')
    )
);

$adminRoutes->add(
    'admin_letter_delete',
    new Route(
        '/letter/{id}/delete',
        array('_controller' => 'Backend:Controllers:LettersController:delete')
    )
);

$adminRoutes->add(
    'admin_letter_toggleavailable',
    new Route(
        '/letter/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:LettersController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_letters_batchpublish',
    new Route(
        '/letter/batch-publish',
        array('_controller' => 'Backend:Controllers:LettersController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_letters_batchdelete',
    new Route(
        '/letter/batch-delete',
        array('_controller' => 'Backend:Controllers:LettersController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_letters_content_list_provider',
    new Route(
        '/letters/content-list-provider',
        array('_controller' => 'Backend:Controllers:LettersController:contentListProvider')
    )
);

// Category manager routes
$adminRoutes->add(
    'admin_categories',
    new Route(
        '/categories',
        array('_controller' => 'Backend:Controllers:CategoriesController:list')
    )
);

$adminRoutes->add(
    'admin_categories_config',
    new Route(
        '/categories/config',
        array('_controller' => 'Backend:Controllers:CategoriesController:config')
    )
);

$adminRoutes->add(
    'admin_category_create',
    new Route(
        '/category/create',
        array('_controller' => 'Backend:Controllers:CategoriesController:create')
    )
);

$adminRoutes->add(
    'admin_category_show',
    new Route(
        '/category/{id}/show',
        array('_controller' => 'Backend:Controllers:CategoriesController:show')
    )
);

$adminRoutes->add(
    'admin_category_update',
    new Route(
        '/category/{id}/update',
        array('_controller' => 'Backend:Controllers:CategoriesController:update')
    )
);

$adminRoutes->add(
    'admin_category_delete',
    new Route(
        '/category/{id}/delete',
        array('_controller' => 'Backend:Controllers:CategoriesController:delete')
    )
);

$adminRoutes->add(
    'admin_category_empty',
    new Route(
        '/category/{id}/empty',
        array('_controller' => 'Backend:Controllers:CategoriesController:empty')
    )
);

$adminRoutes->add(
    'admin_category_toggleavailable',
    new Route(
        '/category/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:CategoriesController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_category_togglerss',
    new Route(
        '/category/{id}/toggle-rss',
        array('_controller' => 'Backend:Controllers:CategoriesController:toggleRss')
    )
);

// Image manager routes
$adminRoutes->add(
    'admin_images_statistics',
    new Route(
        '/images/statistics',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:statistics',
        )
    )
);
$adminRoutes->add(
    'admin_images_search',
    new Route(
        '/images/search',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:search',
        )
    )
);

$adminRoutes->add(
    'admin_images',
    new Route(
        '/images',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:list',
        )
    )
);

$adminRoutes->add(
    'admin_image_new',
    new Route(
        '/images/new',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:new',
        )
    )
);

$adminRoutes->add(
    'admin_image_show',
    new Route(
        '/images/show',
        array(
            '_controller' => 'Backend:Controllers:ImagesController:show',
        )
    )
);

$adminRoutes->add(
    'admin_image_create',
    new Route(
        '/image/create',
        array('_controller' => 'Backend:Controllers:ImagesController:create')
    )
);

$adminRoutes->add(
    'admin_image_update',
    new Route(
        '/image/update',
        array('_controller' => 'Backend:Controllers:ImagesController:update')
    )
);

$adminRoutes->add(
    'admin_image_delete',
    new Route(
        '/image/{id}/delete',
        array('_controller' => 'Backend:Controllers:ImagesController:delete')
    )
);

$adminRoutes->add(
    'admin_images_batchdelete',
    new Route(
        '/images/batchdelete',
        array('_controller' => 'Backend:Controllers:ImagesController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_images_config',
    new Route(
        '/images/config',
        array('_controller' => 'Backend:Controllers:ImagesController:config')
    )
);

$adminRoutes->add(
    'admin_images_content_provider_gallery',
    new Route(
        '/images/content-provider-gallery',
        array('_controller' => 'Backend:Controllers:ImagesController:contentProviderGallery')
    )
);

// Videos controller routes
$adminRoutes->add(
    'admin_videos',
    new Route(
        '/videos',
        array('_controller' => 'Backend:Controllers:VideosController:list')
    )
);

$adminRoutes->add(
    'admin_videos_create',
    new Route(
        '/videos/create',
        array('_controller' => 'Backend:Controllers:VideosController:create')
    )
);

$adminRoutes->add(
    'admin_videos_get_info',
    new Route(
        '/videos/get-video-information',
        array('_controller' => 'Backend:Controllers:VideosController:videoInformation')
    )
);

$adminRoutes->add(
    'admin_video_show',
    new Route(
        '/videos/{id}/show',
        array('_controller' => 'Backend:Controllers:VideosController:show')
    )
);

$adminRoutes->add(
    'admin_videos_update',
    new Route(
        '/videos/{id}/update',
        array('_controller' => 'Backend:Controllers:VideosController:update')
    )
);

$adminRoutes->add(
    'admin_video_delete',
    new Route(
        '/videos/{id}/delete',
        array('_controller' => 'Backend:Controllers:VideosController:delete')
    )
);

$adminRoutes->add(
    'admin_videos_widget',
    new Route(
        '/videos/widget',
        array('_controller' => 'Backend:Controllers:VideosController:widget')
    )
);

$adminRoutes->add(
    'admin_videos_config',
    new Route(
        '/videos/config',
        array('_controller' => 'Backend:Controllers:VideosController:config')
    )
);

$adminRoutes->add(
    'admin_video_save_positions',
    new Route(
        '/videos/save-positions',
        array('_controller' => 'Backend:Controllers:VideosController:savePositions')
    )
);

$adminRoutes->add(
    'admin_video_toggle_available',
    new Route(
        '/videos/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:VideosController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_video_toggle_favorite',
    new Route(
        '/videos/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:VideosController:toggleFavorite')
    )
);

$adminRoutes->add(
    'admin_video_toggle_inhome',
    new Route(
        '/videos/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:VideosController:toggleInHome')
    )
);

$adminRoutes->add(
    'admin_video_get_relations',
    new Route(
        '/videos/{id}/relations',
        array('_controller' => 'Backend:Controllers:VideosController:relations')
    )
);

$adminRoutes->add(
    'admin_video_batchdelete',
    new Route(
        '/videos/batch-delete',
        array('_controller' => 'Backend:Controllers:VideosController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_video_batchpublish',
    new Route(
        '/videos/batch-publish',
        array('_controller' => 'Backend:Controllers:VideosController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_videos_content_provider',
    new Route(
        '/videos/content-provider',
        array('_controller' => 'Backend:Controllers:VideosController:contentProvider')
    )
);

$adminRoutes->add(
    'admin_videos_content_provider_related',
    new Route(
        '/videos/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:VideosController:contentProviderRelated',
        )
    )
);

$adminRoutes->add(
    'admin_videos_content_provider_gallery',
    new Route(
        '/videos/content-provider-gallery',
        array('_controller' => 'Backend:Controllers:VideosController:contentProviderGallery')
    )
);

// Album controller routes
$adminRoutes->add(
    'admin_albums',
    new Route(
        '/albums',
        array('_controller' => 'Backend:Controllers:AlbumsController:list')
    )
);

$adminRoutes->add(
    'admin_albums_config',
    new Route(
        '/albums/config',
        array('_controller' => 'Backend:Controllers:AlbumsController:config')
    )
);

$adminRoutes->add(
    'admin_albums_widget',
    new Route(
        '/albums/widget',
        array('_controller' => 'Backend:Controllers:AlbumsController:widget')
    )
);

$adminRoutes->add(
    'admin_album_create',
    new Route(
        '/album/create',
        array('_controller' => 'Backend:Controllers:AlbumsController:create')
    )
);

$adminRoutes->add(
    'admin_album_update',
    new Route(
        '/album/{id}/update',
        array('_controller' => 'Backend:Controllers:AlbumsController:update')
    )
);

$adminRoutes->add(
    'admin_album_show',
    new Route(
        '/albums/{id}/show',
        array('_controller' => 'Backend:Controllers:AlbumsController:show')
    )
);

$adminRoutes->add(
    'admin_album_delete',
    new Route(
        '/albums/{id}/delete',
        array('_controller' => 'Backend:Controllers:AlbumsController:delete')
    )
);

$adminRoutes->add(
    'admin_album_toggle_available',
    new Route(
        '/album/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:AlbumsController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_album_toggle_favorite',
    new Route(
        '/album/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:AlbumsController:toggleFavorite')
    )
);

$adminRoutes->add(
    'admin_album_toggle_inhome',
    new Route(
        '/album/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:AlbumsController:toggleInHome')
    )
);

$adminRoutes->add(
    'admin_album_batchdelete',
    new Route(
        '/albums/batch-delete',
        array('_controller' => 'Backend:Controllers:AlbumsController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_album_batchpublish',
    new Route(
        '/albums/batch-publish',
        array('_controller' => 'Backend:Controllers:AlbumsController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_albums_savepositions',
    new Route(
        '/albums/save-positions',
        array('_controller' => 'Backend:Controllers:CoversController:savePositions')
    )
);

$adminRoutes->add(
    'admin_albums_content_provider',
    new Route(
        '/albums/content-provider',
        array('_controller' => 'Backend:Controllers:AlbumsController:contentProvider')
    )
);

$adminRoutes->add(
    'admin_albums_content_provider_related',
    new Route(
        '/albums/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:AlbumsController:contentProviderRelated',
        )
    )
);

// Covers controller routes
$adminRoutes->add(
    'admin_covers',
    new Route(
        '/covers',
        array('_controller' => 'Backend:Controllers:CoversController:list')
    )
);

$adminRoutes->add(
    'admin_cover_create',
    new Route(
        '/cover/create',
        array('_controller' => 'Backend:Controllers:CoversController:create')
    )
);

$adminRoutes->add(
    'admin_cover_show',
    new Route(
        '/covers/{id}/show',
        array('_controller' => 'Backend:Controllers:CoversController:show')
    )
);

$adminRoutes->add(
    'admin_cover_update',
    new Route(
        '/cover/{id}/update',
        array('_controller' => 'Backend:Controllers:CoversController:update')
    )
);

$adminRoutes->add(
    'admin_cover_toggleavailable',
    new Route(
        '/cover/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:CoversController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_cover_togglefavorite',
    new Route(
        '/cover/{id}/toggle-favorite',
        array('_controller' => 'Backend:Controllers:CoversController:toggleFavorite')
    )
);

$adminRoutes->add(
    'admin_cover_toggleinhome',
    new Route(
        '/cover/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:CoversController:toggleInHome')
    )
);

$adminRoutes->add(
    'admin_covers_batchpublish',
    new Route(
        '/covers/batch-publish',
        array('_controller' => 'Backend:Controllers:CoversController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_covers_batchdelete',
    new Route(
        '/covers/batch-delete',
        array('_controller' => 'Backend:Controllers:CoversController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_covers_savepositions',
    new Route(
        '/covers/save-positions',
        array('_controller' => 'Backend:Controllers:CoversController:savePositions')
    )
);

$adminRoutes->add(
    'admin_cover_delete',
    new Route(
        '/cover/{id}/delete',
        array('_controller' => 'Backend:Controllers:CoversController:delete')
    )
);

$adminRoutes->add(
    'admin_covers_widget',
    new Route(
        '/cover/widget',
        array('_controller' => 'Backend:Controllers:CoversController:widget')
    )
);

$adminRoutes->add(
    'admin_covers_config',
    new Route(
        '/covers/config',
        array('_controller' => 'Backend:Controllers:CoversController:config')
    )
);

// Books controller routes
$adminRoutes->add(
    'admin_books',
    new Route(
        '/books',
        array(
            '_controller' => 'Backend:Controllers:BooksController:list',
        )
    )
);

$adminRoutes->add(
    'admin_books_widget',
    new Route(
        '/books/widget',
        array(
            '_controller' => 'Backend:Controllers:BooksController:widget',
            'category'    => 'widget',
        )
    )
);

$adminRoutes->add(
    'admin_books_create',
    new Route(
        '/books/create',
        array('_controller' => 'Backend:Controllers:BooksController:create')
    )
);

$adminRoutes->add(
    'admin_books_show',
    new Route(
        '/books/{id}/show',
        array('_controller' => 'Backend:Controllers:BooksController:show')
    )
);

$adminRoutes->add(
    'admin_books_update',
    new Route(
        '/books/{id}/update',
        array('_controller' => 'Backend:Controllers:BooksController:update')
    )
);

$adminRoutes->add(
    'admin_books_delete',
    new Route(
        '/books/{id}/delete',
        array('_controller' => 'Backend:Controllers:BooksController:delete')
    )
);

$adminRoutes->add(
    'admin_books_save_positions',
    new Route(
        '/books/save-positions',
        array('_controller' => 'Backend:Controllers:BooksController:savePositions')
    )
);

$adminRoutes->add(
    'admin_books_toggle_available',
    new Route(
        '/books/{id}/toggle-available',
        array('_controller' => 'Backend:Controllers:BooksController:toggleAvailable')
    )
);

$adminRoutes->add(
    'admin_books_toggle_inhome',
    new Route(
        '/books/{id}/toggle-inhome',
        array('_controller' => 'Backend:Controllers:BooksController:toggleInHome')
    )
);

$adminRoutes->add(
    'admin_books_batchdelete',
    new Route(
        '/books/batch-delete',
        array('_controller' => 'Backend:Controllers:BooksController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_books_batchpublish',
    new Route(
        '/books/batch-publish',
        array('_controller' => 'Backend:Controllers:BooksController:batchPublish')
    )
);

// Files controller routes
$adminRoutes->add(
    'admin_files',
    new Route(
        '/files',
        array(
            '_controller' => 'Backend:Controllers:FilesController:list',
        )
    )
);

$adminRoutes->add(
    'admin_files_statistics',
    new Route(
        '/files/statistics',
        array(
            '_controller' => 'Backend:Controllers:FilesController:statistics',
        )
    )
);

$adminRoutes->add(
    'admin_files_widget',
    new Route(
        '/files/widget',
        array(
            '_controller' => 'Backend:Controllers:FilesController:widget',
        )
    )
);

$adminRoutes->add(
    'admin_files_create',
    new Route(
        '/files/create',
        array(
            '_controller' => 'Backend:Controllers:FilesController:create',
        )
    )
);

$adminRoutes->add(
    'admin_files_show',
    new Route(
        '/files/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:FilesController:show',
        )
    )
);

$adminRoutes->add(
    'admin_files_update',
    new Route(
        '/files/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:FilesController:update',
        )
    )
);

$adminRoutes->add(
    'admin_files_delete',
    new Route(
        '/files/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:FilesController:delete',
        )
    )
);

$adminRoutes->add(
    'admin_files_toggle_favorite',
    new Route(
        '/files/{id}/toggle-favorite',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleFavorite',
        )
    )
);

$adminRoutes->add(
    'admin_files_toggle_in_home',
    new Route(
        '/files/{id}/toggle-in-home',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleInHome',
        )
    )
);

$adminRoutes->add(
    'admin_files_toggle_available',
    new Route(
        '/files/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:FilesController:toggleAvailable',
        )
    )
);

$adminRoutes->add(
    'admin_file_save_positions',
    new Route(
        '/files/save-positions',
        array('_controller' => 'Backend:Controllers:FilesController:savePositions')
    )
);

$adminRoutes->add(
    'admin_files_batchdelete',
    new Route(
        '/files/batch-delete',
        array('_controller' => 'Backend:Controllers:FilesController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_files_batchpublish',
    new Route(
        '/files/batch-publish',
        array('_controller' => 'Backend:Controllers:FilesController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_files_content_provider',
    new Route(
        '/files/content-provider',
        array('_controller' => 'Backend:Controllers:FilesController:contentListProvider')
    )
);

// Search controller routes
$adminRoutes->add(
    'admin_search',
    new Route(
        '/search',
        array('_controller' => 'Backend:Controllers:SearchController:default')
    )
);

$adminRoutes->add(
    'admin_search_content_provider',
    new Route(
        '/search/content-provider',
        array('_controller' => 'Backend:Controllers:SearchController:contentProvider')
    )
);

// Keywork controller routes
$adminRoutes->add(
    'admin_newsletters',
    new Route(
        '/newsletters',
        array('_controller' => 'Backend:Controllers:NewsletterController:list')
    )
);

$adminRoutes->add(
    'admin_newsletter_create',
    new Route(
        '/newsletter/create',
        array('_controller' => 'Backend:Controllers:NewsletterController:create')
    )
);

$adminRoutes->add(
    'admin_newsletter_show_contents',
    new Route(
        '/newsletter/{id}/contents',
        array('_controller' => 'Backend:Controllers:NewsletterController:showContents')
    )
);

$adminRoutes->add(
    'admin_newsletter_save_contents',
    new Route(
        '/newsletter/save-contents',
        array('_controller' => 'Backend:Controllers:NewsletterController:saveContents')
    )
);

$adminRoutes->add(
    'admin_newsletter_preview',
    new Route(
        '/newsletter/{id}/preview',
        array('_controller' => 'Backend:Controllers:NewsletterController:preview')
    )
);

$adminRoutes->add(
    'admin_newsletter_save_html',
    new Route(
        '/newsletter/{id}/save-html',
        array('_controller' => 'Backend:Controllers:NewsletterController:saveHtmlContent')
    )
);

$adminRoutes->add(
    'admin_newsletter_pick_recipients',
    new Route(
        '/newsletter/{id}/pick-recipients',
        array('_controller' => 'Backend:Controllers:NewsletterController:pickRecipients')
    )
);

$adminRoutes->add(
    'admin_newsletter_send',
    new Route(
        '/newsletter/{id}/send',
        array('_controller' => 'Backend:Controllers:NewsletterController:send')
    )
);

$adminRoutes->add(
    'admin_newsletter_delete',
    new Route(
        '/newsletter/{id}/delete',
        array('_controller' => 'Backend:Controllers:NewsletterController:delete')
    )
);

$adminRoutes->add(
    'admin_newsletter_config',
    new Route(
        '/newsletters/config',
        array('_controller' => 'Backend:Controllers:NewsletterController:config')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptors',
    new Route(
        '/newsletters/subscriptors',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:list')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_create',
    new Route(
        '/newsletters/subscriptor/create',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:create')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_show',
    new Route(
        '/newsletters/subscriptor/{id}/show',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:show')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_update',
    new Route(
        '/newsletters/subscriptor/{id}/update',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:update')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_delete',
    new Route(
        '/newsletters/subscriptor/{id}/delete',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:delete')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_toggle_subscription',
    new Route(
        '/newsletters/subscriptor/{id}/toggle-subscription',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:toggleSubscription')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_toggle_activated',
    new Route(
        '/newsletters/subscriptor/{id}/toggle-activated',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:toggleActivated')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptor_batch_delete',
    new Route(
        '/newsletters/subscriptors/batch-delete',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_newsletter_subscriptors_batch_subscribe',
    new Route(
        '/newsletters/subscriptors/batch-subscribe',
        array('_controller' => 'Backend:Controllers:NewsletterSubscriptorsController:batchSubscribe')
    )
);

// Keywork controller routes
$adminRoutes->add(
    'admin_keywords',
    new Route(
        '/keywords',
        array('_controller' => 'Backend:Controllers:KeywordsController:list')
    )
);

$adminRoutes->add(
    'admin_keyword_create',
    new Route(
        '/keywords/create',
        array('_controller' => 'Backend:Controllers:KeywordsController:create')
    )
);

$adminRoutes->add(
    'admin_keyword_show',
    new Route(
        '/keywords/{id}/show',
        array('_controller' => 'Backend:Controllers:KeywordsController:show')
    )
);

$adminRoutes->add(
    'admin_keyword_update',
    new Route(
        '/keywords/{id}/update',
        array('_controller' => 'Backend:Controllers:KeywordsController:update')
    )
);

$adminRoutes->add(
    'admin_keyword_delete',
    new Route(
        '/keywords/{id}/delete',
        array('_controller' => 'Backend:Controllers:KeywordsController:delete')
    )
);

$adminRoutes->add(
    'admin_keyword_autolink',
    new Route(
        '/keywords/autolink',
        array('_controller' => 'Backend:Controllers:KeywordsController:autolink')
    )
);

// Statistics controller routes
$adminRoutes->add(
    'admin_statistics',
    new Route(
        '/statistics',
        array('_controller' => 'Backend:Controllers:StatisticsController:default')
    )
);

$adminRoutes->add(
    'admin_statistics_widget',
    new Route(
        '/statistics/widget',
        array('_controller' => 'Backend:Controllers:StatisticsController:getWidget')
    )
);

// Article controller routes
$adminRoutes->add(
    'admin_articles',
    new Route(
        '/articles',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:list',
        )
    )
);

$adminRoutes->add(
    'admin_article_create',
    new Route(
        '/article/create',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:create',
        )
    )
);

$adminRoutes->add(
    'admin_article_show',
    new Route(
        '/article/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:show',
        )
    )
);

$adminRoutes->add(
    'admin_article_delete',
    new Route(
        '/article/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:delete',
        )
    )
);

$adminRoutes->add(
    'admin_article_update',
    new Route(
        '/article/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:update',
        )
    )
);

$adminRoutes->add(
    'admin_article_toggleavailable',
    new Route(
        '/article/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:toggleAvailable',
        )
    )
);

$adminRoutes->add(
    'admin_article_preview',
    new Route(
        '/article/preview',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:preview',
        )
    )
);

$adminRoutes->add(
    'admin_article_get_preview',
    new Route(
        '/article/get-preview',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:getPreview',
        )
    )
);

$adminRoutes->add(
    'admin_articles_content_provider_suggested',
    new Route(
        '/articles/content-provider-suggested',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderSuggested',
        )
    )
);

$adminRoutes->add(
    'admin_articles_content_provider_category',
    new Route(
        '/articles/content-provider-category',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderCategory',
        )
    )
);

$adminRoutes->add(
    'admin_articles_content_provider_related',
    new Route(
        '/articles/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderRelated',
        )
    )
);

$adminRoutes->add(
    'admin_articles_content_provider_in_frontpage',
    new Route(
        '/articles/content-provider-in-frontpage',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:contentProviderInFrontpage',
        )
    )
);


$adminRoutes->add(
    'admin_articles_batchdelete',
    new Route(
        '/articles/batch-delete',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:batchDelete',
        )
    )
);

$adminRoutes->add(
    'admin_articles_batchpublish',
    new Route(
        '/articles/batch-publish',
        array(
            '_controller' => 'Backend:Controllers:ArticlesController:batchPublish',
        )
    )
);


// Opinion controller routes
$adminRoutes->add(
    'admin_opinions',
    new Route(
        '/opinions',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:list',
        )
    )
);

$adminRoutes->add(
    'admin_opinions_frontpage',
    new Route(
        '/opinions/frontpage',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:frontpage',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_create',
    new Route(
        '/opinion/create',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:create',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_show',
    new Route(
        '/opinion/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:show',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_update',
    new Route(
        '/opinion/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:update',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_delete',
    new Route(
        '/opinion/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:delete',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_toggleavailable',
    new Route(
        '/opinion/{id}/toggle-available',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:toggleAvailable',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_toggleinhome',
    new Route(
        '/opinion/{id}/toggle-inhome',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:toggleInHome',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_togglefavorite',
    new Route(
        '/opinion/{id}/toggle-favorite',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:toggleFavorite',
        )
    )
);

$adminRoutes->add(
    'admin_opinions_batch_delete',
    new Route(
        '/opinions/batch-delete',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_opinions_batch_publish',
    new Route(
        '/opinions/batch-publish',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchPublish')
    )
);

$adminRoutes->add(
    'admin_opinions_batch_inhome',
    new Route(
        '/opinions/batch-inhome',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchInHome')
    )
);

$adminRoutes->add(
    'admin_opinions_batch_inhome',
    new Route(
        '/opinions/save-positions',
        array('_controller' => 'Backend:Controllers:OpinionsController:batchInHome')
    )
);

$adminRoutes->add(
    'admin_opinions_savepositions',
    new Route(
        '/opinions/batch-inhome',
        array('_controller' => 'Backend:Controllers:OpinionsController:savePositions')
    )
);

$adminRoutes->add(
    'admin_opinions_content_provider',
    new Route(
        '/opinions/content-provider',
        array('_controller' => 'Backend:Controllers:OpinionsController:contentProvider')
    )
);

$adminRoutes->add(
    'admin_opinions_content_provider_related',
    new Route(
        '/opinions/content-provider-related',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:contentProviderRelated',
        )
    )
);

$adminRoutes->add(
    'admin_opinions_config',
    new Route(
        '/opinions/config',
        array(
            '_controller' => 'Backend:Controllers:OpinionsController:config',
        )
    )
);

// Opinion author controller routes
$adminRoutes->add(
    'admin_opinion_authors',
    new Route(
        '/opinion/authors',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:list',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_author_show',
    new Route(
        '/opinion/author/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:show',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_author_create',
    new Route(
        '/opinion/authors/create',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:create',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_author_delete',
    new Route(
        '/opinion/author/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:delete',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_author_update',
    new Route(
        '/opinion/author/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:update',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_author_delete',
    new Route(
        '/opinion/author/{id}/delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:delete',
        )
    )
);

$adminRoutes->add(
    'admin_opinion_author_batchdelete',
    new Route(
        '/opinion/authors/batch-delete',
        array(
            '_controller' => 'Backend:Controllers:OpinionAuthorsController:batchDelete',
        )
    )
);

// Comments controller routes
$adminRoutes->add(
    'admin_comments',
    new Route(
        '/comments',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:list',
        )
    )
);

$adminRoutes->add(
    'admin_comments_config',
    new Route(
        '/comments/config',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:config',
        )
    )
);

$adminRoutes->add(
    'admin_comments_show',
    new Route(
        '/comments/{id}/show',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:show',
        )
    )
);

$adminRoutes->add(
    'admin_comments_update',
    new Route(
        '/comments/{id}/update',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:update',
        )
    )
);

$adminRoutes->add(
    'admin_comments_delete',
    new Route(
        '/comments/{id}/delete',
        array('_controller' => 'Backend:Controllers:CommentsController:delete')
    )
);

$adminRoutes->add(
    'admin_comments_toggle_status',
    new Route(
        '/comments/{id}/toggle-status',
        array(
            '_controller' => 'Backend:Controllers:CommentsController:toggleStatus',
        )
    )
);

$adminRoutes->add(
    'admin_comments_batch_status',
    new Route(
        '/comments/batch-status',
        array('_controller' => 'Backend:Controllers:CommentsController:batchStatus')
    )
);

$adminRoutes->add(
    'admin_comments_batch_delete',
    new Route(
        '/comments/batch-delete',
        array('_controller' => 'Backend:Controllers:CommentsController:batchDelete')
    )
);

// Comments by Disqus controller routes
$adminRoutes->add(
    'admin_comments_disqus',
    new Route(
        '/comments/disqus',
        array('_controller' => 'Backend:Controllers:CommentsDisqusController:default')
    )
);

$adminRoutes->add(
    'admin_comments_disqus_config',
    new Route(
        '/comments/disqus/config',
        array('_controller' => 'Backend:Controllers:CommentsDisqusController:config')
    )
);

// Trash controller routes
$adminRoutes->add(
    'admin_trash',
    new Route(
        '/trash',
        array('_controller' => 'Backend:Controllers:TrashController:default')
    )
);

$adminRoutes->add(
    'admin_trash_delete',
    new Route(
        '/trash/{id}/delete',
        array('_controller' => 'Backend:Controllers:TrashController:delete')
    )
);

$adminRoutes->add(
    'admin_trash_restore',
    new Route(
        '/trash/{id}/restore',
        array('_controller' => 'Backend:Controllers:TrashController:restore')
    )
);

$adminRoutes->add(
    'admin_trash_batchdelete',
    new Route(
        '/trash/batchdelete',
        array('_controller' => 'Backend:Controllers:TrashController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_trash_batchrestore',
    new Route(
        '/trash/batchrestore',
        array('_controller' => 'Backend:Controllers:TrashController:batchRestore')
    )
);

// Paywall controller routes
$adminRoutes->add(
    'admin_paywall',
    new Route(
        '/paywall',
        array('_controller' => 'Backend:Controllers:PaywallController:default')
    )
);

$adminRoutes->add(
    'admin_paywall_users',
    new Route(
        '/paywall/users',
        array('_controller' => 'Backend:Controllers:PaywallController:users')
    )
);

$adminRoutes->add(
    'admin_paywall_purchases',
    new Route(
        '/paywall/purchases',
        array('_controller' => 'Backend:Controllers:PaywallController:purchases')
    )
);

$adminRoutes->add(
    'admin_paywall_settings',
    new Route(
        '/paywall/settings',
        array('_controller' => 'Backend:Controllers:PaywallController:settings')
    )
);

$adminRoutes->add(
    'admin_paywall_settings_save',
    new Route(
        '/paywall/settings/save',
        array('_controller' => 'Backend:Controllers:PaywallController:settingsSave')
    )
);

// Importer Efe controller routes
$adminRoutes->add(
    'admin_news_agency',
    new Route(
        '/news-agency',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:list')
    )
);

$adminRoutes->add(
    'admin_news_agency_servers',
    new Route(
        '/news-agency/servers',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:configListServers')
    )
);

$adminRoutes->add(
    'admin_news_agency_server_create',
    new Route(
        '/news-agency/servers/create',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:configCreateServer')
    )
);

$adminRoutes->add(
    'admin_news_agency_server_show',
    new Route(
        '/news-agency/servers/{id}/show',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:configShowServer')
    )
);


$adminRoutes->add(
    'admin_news_agency_server_update',
    new Route(
        '/news-agency/servers/{id}/update',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:configUpdateServer')
    )
);

$adminRoutes->add(
    'admin_news_agency_server_delete',
    new Route(
        '/news-agency/servers/{id}/delete',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:configDeleteServer')
    )
);

$adminRoutes->add(
    'admin_news_agency_server_clean_files',
    new Route(
        '/news-agency/servers/{id}/clean',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:removeServerFiles')
    )
);

$adminRoutes->add(
    'admin_news_agency_unlock',
    new Route(
        '/news-agency/unlock',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:unlock')
    )
);

$adminRoutes->add(
    'admin_news_agency_sync',
    new Route(
        '/news-agency/sync',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:sync')
    )
);

$adminRoutes->add(
    'admin_news_agency_import',
    new Route(
        '/news-agency/{source_id}/{id}/import',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:import')
    )
);

$adminRoutes->add(
    'admin_news_agency_show',
    new Route(
        '/news-agency/{source_id}/{id}/show',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:show')
    )
);

$adminRoutes->add(
    'admin_news_agency_showattachment',
    new Route(
        '/news-agency/{source_id}/{id}/attachment/{attachment_id}',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:showAttachment')
    )
);

$adminRoutes->add(
    'admin_news_agency_pickcategory',
    new Route(
        '/news-agency/{source_id}/{id}/pickcategory',
        array('_controller' => 'Backend:Controllers:NewsAgencyController:selectCategoryWhereToImport')
    )
);

// Importer XML file controller routes
$adminRoutes->add(
    'admin_importer_xmlfile',
    new Route(
        '/importer/xml-file',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:default')
    )
);
$adminRoutes->add(
    'admin_importer_xmlfile_config',
    new Route(
        '/importer/xml-file/config',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:config')
    )
);

$adminRoutes->add(
    'admin_importer_xmlfile_import',
    new Route(
        '/importer/xml-file/import',
        array('_controller' => 'Backend:Controllers:ImporterXmlfileController:import')
    )
);

// Template cache controller routes
$adminRoutes->add(
    'admin_tpl_manager',
    new Route(
        '/system/cachemanager',
        array('_controller' => 'Backend:Controllers:CacheManagerController:default')
    )
);

$adminRoutes->add(
    'admin_tpl_manager_config',
    new Route(
        '/system/cachemanager/config',
        array('_controller' => 'Backend:Controllers:CacheManagerController:config')
    )
);

$adminRoutes->add(
    'admin_tpl_manager_refresh',
    new Route(
        '/system/cachemanager/refresh',
        array('_controller' => 'Backend:Controllers:CacheManagerController:refresh')
    )
);

$adminRoutes->add(
    'admin_tpl_manager_update',
    new Route(
        '/system/cachemanager/update',
        array('_controller' => 'Backend:Controllers:CacheManagerController:update')
    )
);

$adminRoutes->add(
    'admin_tpl_manager_delete',
    new Route(
        '/system/cachemanager/delete',
        array('_controller' => 'Backend:Controllers:CacheManagerController:delete')
    )
);

$adminRoutes->add(
    'admin_tpl_manager_deleteall',
    new Route(
        '/system/cachemanager/deleteall',
        array('_controller' => 'Backend:Controllers:CacheManagerController:deleteAll')
    )
);

$adminRoutes->add(
    'admin_tpl_manager_cleanfrontpage',
    new Route(
        '/system/cachemanager/cleanfrontapge',
        array('_controller' => 'Backend:Controllers:CacheManagerController:cleanFrontpage')
    )
);

// Database error controller routes
$adminRoutes->add(
    'admin_databaseerrors',
    new Route(
        '/system/databaseerrors',
        array('_controller' => 'Backend:Controllers:DatabaseErrorsController:default')
    )
);
$adminRoutes->add(
    'admin_databaseerrors_purge',
    new Route(
        '/system/databaseerrors/purge',
        array('_controller' => 'Backend:Controllers:DatabaseErrorsController:purge')
    )
);

// User management routes
$adminRoutes->add(
    'admin_acl_user',
    new Route(
        '/acl/users',
        array('_controller' => 'Backend:Controllers:AclUserController:list')
    )
);

$adminRoutes->add(
    'admin_acl_user_create',
    new Route(
        '/acl/user/create',
        array('_controller' => 'Backend:Controllers:AclUserController:create')
    )
);

$adminRoutes->add(
    'admin_acl_user_show',
    new Route(
        '/acl/user/{id}/show',
        array('_controller' => 'Backend:Controllers:AclUserController:show')
    )
);

$adminRoutes->add(
    'admin_acl_user_update',
    new Route(
        '/acl/user/{id}/update',
        array('_controller' => 'Backend:Controllers:AclUserController:update')
    )
);

$adminRoutes->add(
    'admin_acl_user_delete',
    new Route(
        '/acl/user/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclUserController:delete')
    )
);

$adminRoutes->add(
    'admin_acl_user_batchdelete',
    new Route(
        '/acl/users/batchdelete',
        array('_controller' => 'Backend:Controllers:AclUserController:batchDelete')
    )
);

$adminRoutes->add(
    'admin_acl_user_connected_users',
    new Route(
        '/acl/users/connected-users',
        array('_controller' => 'Backend:Controllers:AclUserController:connectedUsers')
    )
);

$adminRoutes->add(
    'admin_acl_user_set_meta',
    new Route(
        '/acl/user/set-meta',
        array('_controller' => 'Backend:Controllers:AclUserController:setMeta')
    )
);

$adminRoutes->add(
    'admin_acl_user_toogle_enabled',
    new Route(
        '/acl/user/{id}/toogle-enabled',
        array('_controller' => 'Backend:Controllers:AclUserController:toogleEnabled')
    )
);

// Privilege management routes
$adminRoutes->add(
    'admin_acl_privileges',
    new Route(
        '/acl/privileges',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:list')
    )
);

$adminRoutes->add(
    'admin_acl_privileges_show',
    new Route(
        '/acl/privilege/show/{id}',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:show')
    )
);

$adminRoutes->add(
    'admin_acl_privileges_create',
    new Route(
        '/acl/privilege/create',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:create')
    )
);

$adminRoutes->add(
    'admin_acl_privileges_update',
    new Route(
        '/acl/privilege/{id}/update',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:update')
    )
);

$adminRoutes->add(
    'admin_acl_privileges_delete',
    new Route(
        '/acl/privilege/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclPrivilegesController:delete')
    )
);

// User groups managerment routes
$adminRoutes->add(
    'admin_acl_usergroups',
    new Route(
        '/acl/usergroups',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:list')
    )
);

$adminRoutes->add(
    'admin_acl_usergroups_show',
    new Route(
        '/acl/usergroup/{id}/show',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:show')
    )
);

$adminRoutes->add(
    'admin_acl_usergroups_create',
    new Route(
        '/acl/usergroup/create',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:create')
    )
);

$adminRoutes->add(
    'admin_acl_usergroups_update',
    new Route(
        '/acl/usergroup/{id}/update',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:update')
    )
);

$adminRoutes->add(
    'admin_acl_usergroups_delete',
    new Route(
        '/acl/usergroup/{id}/delete',
        array('_controller' => 'Backend:Controllers:AclUserGroupsController:delete')
    )
);

$adminRoutes->add(
    'admin_system_settings',
    new Route(
        '/system/settings',
        array('_controller' => 'Backend:Controllers:SystemSettingsController:default')
    )
);

$adminRoutes->add(
    'admin_system_settings_save',
    new Route(
        '/system/settings/save',
        array('_controller' => 'Backend:Controllers:SystemSettingsController:save'),
        array('_method' => 'POST')
    )
);

$adminRoutes->add(
    'admin_utils_calculate_tags',
    new Route(
        '/utils/calculate-tags',
        array('_controller' => 'Backend:Controllers:UtilsController:calculateTags')
    )
);

$adminRoutes->add(
    'admin_login_form',
    new Route(
        '/login',
        array('_controller' => 'Backend:Controllers:AuthenticationController:default')
    )
);
$adminRoutes->add(
    'admin_login_processform',
    new Route(
        '/login/process',
        array('_controller' => 'Backend:Controllers:AuthenticationController:processform'),
        array('_method' => 'POST')
    )
);
$adminRoutes->add(
    'admin_logout',
    new Route(
        '/logout',
        array('_controller' => 'Backend:Controllers:AuthenticationController:logout')
    )
);
$routes->add(
    'admin_welcome',
    new Route(
        '/admin',
        array('_controller' => 'Backend:Controllers:WelcomeController:default')
    )
);

$routes->addCollection($adminRoutes, '/admin');
