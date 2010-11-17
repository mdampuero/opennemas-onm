<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

require_once(SITE_LIBS_PATH.'phpmailer/class.phpmailer.php');

$tpl = new Template(TEMPLATE_USER);

$ccm = new ContentCategoryManager();
  /**************************************  CATEGORY DEFAULT mientras no hay home de gallery  *******************************************/

if (isset($_GET['category_name'])) {
    $category_name = $_GET['category_name'];
}else{
     $the_categorys = $ccm->find(' fk_content_category=0 AND inmenu=1 AND (internal_category =1 OR internal_category = 5)', 'ORDER BY internal_category DESC, inmenu DESC, posmenu ASC LIMIT 0,6');

     foreach($the_categorys as $categ){
         if(!$ccm->isEmpty($categ->name)){
             $this_category_data = $categ;
             break;
         }
     }
     $category_name = $this_category_data->name;
     $category_title = $this_category_data->title;
     $category = $this_category_data->pk_content_category;


      $_GET['category_name']=$category_name;
}

$actual_category = $category_name;

if (isset($_GET['subcategory_name'])) {
    $subcategory_name = $_GET['subcategory_name'];
    $actual_category = $_GET['subcategory_name'];

}
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");

if (!isset ($_GET['subcategory_name'])) {
    $actual_category = $_GET['category_name'];
} else {
    $actual_category = $_GET['subcategory_name'];
}

$tpl->assign('actual_category',$actual_category);
$actual_category_id=$ccm->get_id($actual_category);
$tpl->assign('actual_category_id',$actual_category_id);
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

//Getting articles
$cm = new ContentManager();

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

        case 'frontpage':

            if(($tpl->caching == 0)
			   && (!$tpl->isCached('gallery/gallery-frontpage.tpl'))){

				$albums = $cm->find('Album', 'available=1', 'ORDER BY created DESC LIMIT 0 , 11');
				$tpl->assign('firstalbum',array_shift($albums));
				$tpl->assign('albums', $albums);

				/**
				 * Get info for widgets
				 **/
				require_once('widget_headlines_past.php');
				require_once("widget_gallerys_lastest.php");
				require_once("widget_static_pages.php");
			}
            $tpl->display('gallery/gallery-frontpage.tpl');

            break;

        case 'foto':

			/**
			 * Redirect to album frontpage if id_album wasn't provided
			 **/
			$albumID = $_REQUEST['id_album'];
			if (empty($albumID)) { Application::forward301('/albumes/'); }

			require_once ("gallery_advertisement.php");

			Content::setNumViews($albumID);

			$cacheID = $albumID;
			$tpl->assign('contentId', $albumID);

			//if(($tpl->caching == 0) && (!$tpl->isCached('gallery/gallery.tpl', $cacheID))){

				/**
				 * Get the album from the id and increment the numviews for it
				 **/
				$album = new Album( $albumID );
				$tpl->assign('album', $album);

				/**
				 * Get the other albums for the albums widget
				 **/
				$otherAlbums = $cm->find_by_category('Album',
												$actual_category_id,
												'available=1 and pk_content !='.$albumID,
												'ORDER BY created DESC LIMIT 0 , 5');
				$tpl->assign('gallerys', $otherAlbums);

				$album->category_name = $album->loadCategoryName($album->id);
				$album->category_title = $album->loadCategoryTitle($album->id);
				$_albumArray = $album->get_album($album->id);

				/**
				 * Get the photos for the album
				 **/
				$i=0;
				foreach($_albumArray as $ph){
				   $albumPhotos[$i]['photo'] = new Photo($ph[0]);
				   $albumPhotos[$i]['description']=$ph[2];
				   $i++;
				}
				$tpl->assign('albumPhotos2', $albumPhotos);

				require_once ("widget_gallerys_lastest.php");
				require_once("widget_static_pages.php");
				require_once('widget_headlines_past.php');
				require_once("widget_static_pages.php");

			//} // end iscached

			$tpl->display('gallery/gallery-inner.tpl', $cacheID);

        break;

        default:
                Application::forward301('/');
        break;

    }

}else{
    Application::forward301('/');
}
