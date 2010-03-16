<?php
require_once('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');

require_once('core/advertisement.class.php');

require_once('core/comment.class.php');

 
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

        case 'list':
	        //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
            $video = NULL;

            $cm = new ContentManager();

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

	case 'video':
	        //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
            $video = NULL;
            
            $cm = new ContentManager();
			
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



 /********************************* ADVERTISEMENTS  *********************************************/
require_once ("gallery_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

/******************************************************************************************************/
// Visualizar
$tpl->display('video-frontpage.tpl');