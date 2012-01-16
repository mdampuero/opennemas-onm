<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
/**
 * Setting up available categories for menu.
*/
$ccm = new ContentCategoryManager();

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if(empty($category_name)) {
    $category_name = filter_input(INPUT_POST,'category_name',FILTER_SANITIZE_STRING);
}

$menuFrontpage = Menu::renderMenu('album');
$tpl->assign('menuFrontpage',$menuFrontpage->items);

if(!empty($category_name)) {
    $category = $ccm->get_id($category_name);
    $actual_category_id = $category; // FOR WIDGETS
    $category_real_name = $ccm->get_title($category_name); //used in title
    $tpl->assign(array( 'category_name' => $category_name ,
                        'category' => $category ,
                        'actual_category_id' => $actual_category_id ,
                        'category_real_name' => $category_real_name ,
                ) );
} else {
     //$category_name = 'Portada';
     $category_real_name = 'Portada';
     $tpl->assign(array(
                        'category_real_name' => $category_real_name ,
                ) );
}

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

//Getting articles
$cm = new ContentManager();

/**
 * Route to the proper action
 */
$action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);

if (!is_null($action) ) {

    switch ($action) {

        case 'frontpage':

			$page = filter_input(INPUT_GET,'page',FILTER_SANITIZE_STRING,
								 array('options'=> array('default' => 0)));

			/**
			 * Setup caching system
			 **/
			$tpl->setConfig('gallery-frontpage');

			$cacheID = $tpl->generateCacheId('gallery-frontpage'.$category_name, '', $page);

			/**
			 * Don't execute action logic if was cached before
			 */
            if ( ($tpl->caching == 0)
			   || (!$tpl->isCached('gallery/gallery-frontpage.tpl', $cacheID)) ) {

                $albumSettings = s::get('album_settings');
                $total = isset($albumSettings['total_front'])?$albumSettings['total_front']:2;
                $days = isset( $albumSettings['time_last'])?$albumSettings['time_last']:4;
                $order = isset( $albumSettings['orderFrontpage'])?$albumSettings['orderFrontpage']:'views';


                if ( isset($category) && !empty($category) ) {
                    $albums = $cm->find_by_category('Album',
                                        $category, 'fk_content_type=7 AND available=1',
                                        'ORDER BY  created DESC LIMIT 2');
                } else {
                    if($order == 'favorite') {
                        $albums = $cm->find('Album',
                                        'fk_content_type=7 AND available=1 ',
                                        ' ORDER BY favorite DESC,  created DESC LIMIT '.$total);
                    }else {
                        $albums = $cm->find('Album',
                                        'fk_content_type=7 AND available=1 AND '.
                                        'created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                                        ' ORDER BY views DESC,  created DESC LIMIT '.$total);
                    }
                }

				$tpl->assign('albums', $albums);

			}

            require_once ("album_front_ads.php");

			/**
			 * Send the response to the user
			 */
            $tpl->display('album/album_frontpage.tpl');

        break;

        case 'show':

            $dirtyID = filter_input(INPUT_GET,'album_id',FILTER_SANITIZE_STRING);

            if(empty($dirtyID)) {
                $dirtyID = filter_input(INPUT_POST,'album_id',FILTER_SANITIZE_STRING);
            }

            $albumID = Content::resolveID($dirtyID);
			/**
			 * Redirect to album frontpage if id_album wasn't provided
			 */
			if (is_null($albumID)) { Application::forward301('/albumes/'); }

			$tpl->setConfig('gallery-inner');
			$cacheID = $tpl->generateCacheId('gallery-inner', null, $albumID);

			/**
			 * Increment views for this content
			 */
			Content::setNumViews($albumID);

			$tpl->assign('contentId', $albumID);

			require_once("album_inner_ads.php");

			if (($tpl->caching == 0)
				|| (!$tpl->isCached('gallery/gallery.tpl', $cacheID))){

				/**
				 * Get the album from the id and increment the numviews for it
				 **/
				$album = new Album( $albumID );
				$tpl->assign('album', $album);

				/**
				 * Get the other albums for the albums widget
				 **/
                $configurations = s::get('album_settings');
                $total = isset($configurations['total_front'])?($configurations['total_front']):2;
                $days = isset( $configurations['time_last'])?($configurations['time_last']):4;

				$otherAlbums = $cm->find('Album',
												$category,
												'available=1 and pk_content !='.$albumID.
												' AND created >=DATE_SUB(CURDATE(), INTERVAL ' . $days . ' DAY)  ',
                                                ' ORDER BY views DESC,  created DESC LIMIT '.$total);
				$tpl->assign('gallerys', $otherAlbums);

				$album->category_name = $album->loadCategoryName($album->id);
				$album->category_title = $album->loadCategoryTitle($album->id);
				$_albumArray = $album->get_album($album->id);

				/**
				 * Get the album photos
				 **/
				$i=0;
				$albumPhotos = array();
                if(!empty($_albumArray)) {
                    foreach($_albumArray as $ph){
                       $albumPhotos[$i]['photo'] = new Photo($ph[0]);
                       $albumPhotos[$i]['description']=$ph[2];
                       $i++;
                    }
                }
				$tpl->assign('album_photos', $albumPhotos);

			} // END iscached

			$tpl->display('album/album.tpl', $cacheID);

        break;

        default:
            Application::forward301('/');
        break;

    }

} else{
    Application::forward301('/');
}
