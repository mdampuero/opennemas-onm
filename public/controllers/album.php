<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;
// Start up and setup the app
require_once '../bootstrap.php';

// Setup view
$tpl = new Template(TEMPLATE_USER);

// Setting up available categories for menu.
$ccm = new ContentCategoryManager();

$category_name = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = null;
$action = $request->query->filter('action', 'frontpage', FILTER_SANITIZE_STRING);
$page = $request->query->filter('page', 1, FILTER_VALIDATE_INT);

if (!empty($category_name)) {
    $category = $ccm->get_id($category_name);
    $actual_category_id = $category; // FOR WIDGETS
    $category_real_name = $ccm->get_title($category_name); //used in title
    $tpl->assign(
        array(
            'category_name'         => $category_name ,
            'category'              => $category ,
            'actual_category_id'    => $actual_category_id ,
            'actual_category_title' => $category_real_name,
            'category_real_name'    => $category_real_name ,
        )
    );
} else {
    //$category_name = 'Portada';
    $category_real_name = 'Portada';
    $tpl->assign(
        array(
            'actual_category_title' => $category_real_name,
            'category_real_name'    => $category_real_name,
        )
    );
}
$tpl->assign('actual_category', $category_name);

$cm = new ContentManager();

if (!is_null($action) ) {

    switch ($action) {

        case 'frontpage':
            //Setup caching system
            $tpl->setConfig('gallery-frontpage');
            $cacheID = $tpl->generateCacheId($category_name, '', $page);

            // Don't execute the action logic if was cached before
            if (($tpl->caching == 0)
               || (!$tpl->isCached('gallery/gallery-frontpage.tpl', $cacheID))
            ) {

                $albumSettings = s::get('album_settings');
                $total = isset($albumSettings['total_front'])?$albumSettings['total_front']:2;
                $days  = isset($albumSettings['time_last'])?$albumSettings['time_last']:4;
                $order = isset($albumSettings['orderFrontpage'])?$albumSettings['orderFrontpage']:'views';


                if ( isset($category) && !empty($category) ) {
                    $albums = $cm->find_by_category(
                        'Album',
                        $category, 'fk_content_type=7 AND available=1',
                        'ORDER BY  created DESC LIMIT 2'
                    );
                } else {
                    if ($order == 'favorite') {
                        $albums = $cm->find(
                            'Album',
                            'fk_content_type=7 AND available=1 ',
                            ' ORDER BY favorite DESC,  created DESC LIMIT '.$total
                        );
                    } else {
                        $albums = $cm->find(
                            'Album',
                            'fk_content_type=7 AND available=1 AND '.
                            'created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                            ' ORDER BY views DESC,  created DESC LIMIT '.$total
                        );
                    }
                }

                foreach ($albums as &$album) {
                    $album->cover_image = new Photo($album->cover_id);
                    $album->cover       = $album->cover_image->path_file.$album->cover_image->name;
                }

                $tpl->assign('albums', $albums);

            }

            require_once "album_front_ads.php";

            // Send the response to the user
            $tpl->display('album/album_frontpage.tpl', $cacheID);
            break;

        case 'show':
            // Items_page refers to the widget
            $dirtyID    = $request->query->filter('album_id', null, FILTER_SANITIZE_STRING);
            $albumID    = Content::resolveID($dirtyID);
            $items_page = 8;
            // Redirect to album frontpage if id_album wasn't provided
            if (is_null($albumID)) {
                Application::forward301('/albumes/');
            }

            $tpl->setConfig('gallery-inner');
            $cacheID = $tpl->generateCacheId($category_name, null, $albumID);

            // Increment views for this content
            Content::setNumViews($albumID);

            $tpl->assign('contentId', $albumID);

            include_once "album_inner_ads.php";

            if (($tpl->caching == 0)
                || (!$tpl->isCached('gallery/gallery.tpl', $cacheID))
            ) {

                // Get the album from the id and increment the numviews for it
                $album = new Album($albumID);
                $tpl->assign('album', $album);

                // Get the other albums for the albums widget
                $settings = s::get('album_settings');
                $total = isset($settings['total_front'])?($settings['total_front']):2;
                $days = isset($settings['time_last'])?($settings['time_last']):4;

                $otherAlbums = $cm->find(
                    'Album',
                    'available=1 AND pk_content !='.$albumID
                    .' AND created >=DATE_SUB(CURDATE(), INTERVAL '
                    . $days . ' DAY) ',
                    ' ORDER BY views DESC,  created DESC LIMIT '.$total
                );

                foreach ($otherAlbums as &$content) {
                    $content->cover_image    = new Photo($content->cover_id);
                    $content->cover          = $content->cover_image->path_file.$content->cover_image->name;
                    $content->category_name  = $content->loadCategoryName($content->id);
                    $content->category_title = $content->loadCategoryTitle($content->id);
                }

                $tpl->assign('gallerys', $otherAlbums);

                $album->category_name  = $album->loadCategoryName($album->id);
                $album->category_title = $album->loadCategoryTitle($album->id);
                $_albumArray           = $album->_getAttachedPhotos($album->id);
                $_albumArrayPaged      = $album->getAttachedPhotosPaged($album->id, 8, $page);

                if ( count($_albumArrayPaged) > $items_page ) {
                    array_pop($_albumArrayPaged);
                }

                $tpl->assign(
                    array(
                        'album_photos'       => $_albumArray,
                        'album_photos_paged' => $_albumArrayPaged,
                        'page'               => $page,
                        'items_page'         => $items_page,
                    )
                );

            } // END iscached

            $tpl->display('album/album.tpl', $cacheID);
            break;

        case 'thumbs_paginate':
            // Items_page refers to the widget
            $albumID    = $request->query->filter('album_id', null, FILTER_SANITIZE_STRING);
            $page = $request->query->filter('page', 1, FILTER_VALIDATE_INT);
            $items_page = 8;
            if ($page == 0) {
                $page = 1;
            }
            // Redirect to album frontpage if id_album wasn't provided
            if (is_null($albumID)) {
                Application::forward301('/albumes/');
            }

            // Get the album from the id and increment the numviews for it
            $album = new Album($albumID);
            $tpl->assign('album', $album);

            $album->category_name  = $album->loadCategoryName($album->id);
            $album->category_title = $album->loadCategoryTitle($album->id);
            $_albumArray           = $album->_getAttachedPhotos($album->id);
            $_albumArrayPaged      = $album->getAttachedPhotosPaged($album->id, 8, $page);

            if ( count($_albumArrayPaged) > $items_page ) {
                array_pop($_albumArrayPaged);
            }

            $tpl->assign(
                array(
                    'album_photos'       => $_albumArray,
                    'album_photos_paged' => $_albumArrayPaged,
                    'page'               => $page,
                    'items_page'         => $items_page,
                )
            );

            $html = $tpl->fetch('widgets/widget_gallery_thumbs.tpl');
            echo $html;

            break;

        default:
            Application::forward301('/');
            break;
    }

} else {
    Application::forward301('/');
}
