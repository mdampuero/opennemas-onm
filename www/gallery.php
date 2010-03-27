<?php
require_once('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/article.class.php');
require_once('core/advertisement.class.php');
require_once('core/related_content.class.php');
require_once('core/attachment.class.php');
require_once('core/attach_content.class.php');
require_once('core/opinion.class.php');
require_once('core/comment.class.php');
require_once('core/author.class.php');

require_once('core/media.manager.class.php');
require_once('core/img_galery.class.php');
require_once('core/photo.class.php');
require_once('core/album.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');
require_once('core/video.class.php');

require_once('libs/phpmailer/class.phpmailer.php');

$tpl = new Template(TEMPLATE_USER);

$ccm = new ContentCategoryManager();
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

//Obtenemos los articulos
$cm = new ContentManager();
 

/**************************************  PHOTOS - VIDEOS  ***********************************************/

// Se borrara cuando se contemple action en la url (comprobar
if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

	case 'foto':
        
            if ( isset ($_REQUEST['id_album']) && !empty($_REQUEST['id_album'])){
		$albums = $cm->find('Album', 'available=1 and pk_content !='.$_REQUEST['id'], 'ORDER BY created DESC LIMIT 0 , 5');
                $thisalbum = new Album( $_REQUEST['id_album'] );

            } else {
            	$albums = $cm->find('Album', 'available=1', 'ORDER BY created DESC LIMIT 0 , 6');
            	$thisalbum = array_shift($albums);  //Extrae el primero
                $_REQUEST['id_album']=$thisalbum->id;
            }

            $thisalbum->category_name = $thisalbum->loadCategoryName($thisvideo->id);
            $thisalbum->category_title = $thisalbum->loadCategoryTitle($thisvideo->id);
            $_albumArray = $thisalbum->get_album($thisalbum->id);
             $i=0;           

             foreach($_albumArray as $ph){
                      $albumPhotos[$i]['photo'] = new Photo($ph[0]);
                      $albumPhotos[$i]['description']=$ph[2];
	            
                    $i++;
                    }
             $tpl->assign('album', $thisalbum);
             $tpl->assign('albumPhotos2', $albumPhotos);

            //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
      /*      if ( isset ($_REQUEST['id_album']) && !empty($_REQUEST['id_album'])) {
                
                $category_name = $ccm->get_father($cm->get_categoryName_by_contentId($_REQUEST['id_album']));
                $tpl->assign('category_name', $category_name);
                
                $album = new Album( $_REQUEST['id_album'] );
                Content::set_numviews($_REQUEST['id_album']);
                if($album->available){
                    $tpl->assign('album', $album);
                    
	            $albumArray=array();
	            $_albumArray = $album->get_album($_REQUEST['id_album']);
                    
	            $tpl->assign('_albumArray', $_albumArray);
                    
                    // FIXME: evitar ne Photo
	            foreach($_albumArray as $ph){
                        $albumArray[] = new Photo($ph[0]);
                        $albumDescrip[]=$ph[2];
	            }
	            $tpl->assign('albumArray', $albumArray);
	            $tpl->assign('albumDescrip', $albumDescrip);
                }
               
                
             	if ($category_name != 'humor-grafico') {					  
                    $list_albums = $cm->find_by_category('Album', 3, 'available=1', 'ORDER BY created DESC LIMIT 0 , 30');
             	} else {
                   $list_albums = $cm->find('Album', 'available=1', 'ORDER BY pk_album DESC LIMIT 0 , 30');
             	}
                
             	$list_albums = $cm->paginate_num_js($list_albums, 5, 1, 'get_paginate_articles',"'albums',''");
                $tpl->assign('list_albums', $list_albums);
                $tpl->assign('pages', $cm->pager);
       * } else {
           //     Application::forward301('/');
            } */
	break;


	case 'video':
	        //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
            $video = NULL;
            

			
            // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
			
            if ( isset ($_REQUEST['id']) && !empty($_REQUEST['id'])){
		$videos = $cm->find('Video', 'available=1 and pk_content !='.$_REQUEST['id'], 'ORDER BY created DESC LIMIT 0 , 5');			            	
                $video = new Video( $_REQUEST['id'] );
            } else {
            	$videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 0 , 6');			            	            	          
            	$video = array_shift($videos);  //Extrae el primero            
            }
            
            $others_videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 6, 39');
	    $others_videos= $cm->paginate_num_js($others_videos,5, 1, 'get_paginate_articles',"'videos',''");
            $tpl->assign('video', $video);
            $tpl->assign('videos', $videos);            
            $tpl->assign('others_videos', $others_videos);
            $tpl->assign('pages', $cm->pager);
	break;

        default:
                Application::forward301('/');
        break;
    }
}else{
    Application::forward301('/');
}

$tpl->assign('MEDIA_IMG_PATH_WEB', MEDIA_IMG_PATH_WEB);

/**********************************  CONECTA COLUMN3  ******************************************/
// require_once("conecta_cuadro.php");
/**********************************  CONECTA COLUMN3  ******************************************/

/********************************* ADVERTISEMENTS  *********************************************/
//require_once ("gallery_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

/******************************************************************************************************/
// Visualizar
require_once('widget_headlines_past.php');
 
$tpl->display('gallery.tpl');