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

//Obtenemos los articulos
$cm = new ContentManager();

if (isset($_GET['category_name'])) {
    $category_name = $_GET['category_name'];
}else{
 
     $category_data = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 AND (internal_category =1 OR internal_category = 5)', 'ORDER BY internal_category DESC, posmenu LIMIT 0,1');
     $category_name = $category_data[0]->name;

}

$actual_category = $category_name;

if (isset ($_GET['subcategory_name'])) {
    $subcategory_name = $_GET['subcategory_name'];
    $actual_category = $_GET['subcategory_name'];
}
 
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


/**************************************   VIDEOS  ***********************************************/

 
if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

        case 'list':
	        //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
            $video = NULL;

            $cm = new ContentManager();

            // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);

            $videos = $cm->find_by_category_name('Video',$actual_category, NULL, 'ORDER BY content_status DESC, created DESC LIMIT 0 , 4 ');
 var_dump($actual_category);
var_dump($videos);
           // 	$videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 0 , 6');

            $others_videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 4, 12');

            $tpl->assign('videos', $videos);
            $tpl->assign('others_videos', $others_videos);
            
	break;

	case 'inner':
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
            $tpl->assign('action', 'inner');
	break;

        default:
                Application::forward301('/');
        break;
    }
}else{
    Application::forward301('/');
}



 /********************************* ADVERTISEMENTS  *********************************************/
//require_once ("gallery_advertisement.php");
/********************************* ADVERTISEMENTS  *********************************************/

/******************************************************************************************************/
// Visualizar
$tpl->display('video.tpl');