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
            
            $albums = $cm->find('Album', 'available=1', 'ORDER BY created DESC LIMIT 0 , 11');
            $tpl->assign('firstalbum',array_shift($albums));
            $tpl->assign('albums', $albums);
            
            require_once('widget_headlines_past.php');
            // Get the needed variables to show the Tab widget
            require_once ("widget_gallerys_lastest.php");

             /**
             * Fetch information for Static Pages
            */
            require_once("widget_static_pages.php");
        
            $tpl->display('gallery/gallery-frontpage.tpl');
            
            break;
        
        case 'foto':
            
            if ( isset($_REQUEST['id_album']) && !empty($_REQUEST['id_album'])){
                $albums = $cm->find_by_category('Album', $actual_category_id,  'available=1 and pk_content !='.$_REQUEST['id_album'], 'ORDER BY created DESC LIMIT 0 , 5');
                $thisalbum = new Album( $_REQUEST['id_album'] );
    
            } else {
                $albums = $cm->find_by_category('Album', $actual_category_id,  'available=1', 'ORDER BY created DESC LIMIT 0 , 6');
                $thisalbum = array_shift($albums);  //Extrae el primero
                $_REQUEST['id_album'] = $thisalbum->id;
            }
            if(!empty($thisalbum->id)){
                
              //  $thisalbum->setNumViews($thisalbum->id);
                 Content::setNumViews($thisalbum->id);

                $thisalbum->category_name = $thisalbum->loadCategoryName($thisalbum->id);
                $thisalbum->category_title = $thisalbum->loadCategoryTitle($thisalbum->id);
                $_albumArray = $thisalbum->get_album($thisalbum->id);
                $i=0;
    
                foreach($_albumArray as $ph){
                    $albumPhotos[$i]['photo'] = new Photo($ph[0]);
                    $albumPhotos[$i]['description']=$ph[2];
                    $i++;
                 }
                 $tpl->assign('album', $thisalbum);
                 $tpl->assign('albumPhotos2', $albumPhotos);
            
    
                 $tpl->assign('gallerys', $albums);
    
                require_once ("widget_gallerys_lastest.php");
                require_once("widget_static_pages.php");
                
                require_once ("gallery_advertisement.php");
                
                // Visualizar
                require_once('widget_headlines_past.php');

                    /**
                 * Fetch information for Static Pages
                */
                require_once("widget_static_pages.php");
                 
                $tpl->display('gallery/gallery.tpl');
                 
            } else {
               Application::forward301('/albumes/');
            }
            
        break;
     
        default:
                Application::forward301('/');
        break;

    }
    
}else{
    Application::forward301('/');
}
