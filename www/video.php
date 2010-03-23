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
 
     $this_category_data = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 AND (internal_category =1 OR internal_category = 5)', 'ORDER BY internal_category DESC, posmenu ASC LIMIT 0,1');
     $category_name = $this_category_data[0]->name;

}

$actual_category = $category_name;

if (isset ($_GET['subcategory_name'])) {
    $subcategory_name = $_GET['subcategory_name'];
    $actual_category = $_GET['subcategory_name'];
}
 
require_once ("index_sections.php");
/******************************  CATEGORIES & SUBCATEGORIES  *********************************/


/**************************************   VIDEOS  ***********************************************/
//$_REQUEST['action']='inner';

 
if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {

        case 'list':
	        //FIXED: check if there is the album 'id_album' otherwise exit()
            //SECURITY REASONS
            $video = NULL;

            $cm = new ContentManager();

            // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);

            $videos = $cm->find_by_category_name('Video',$actual_category, 'contents.available = 1 ', 'ORDER BY content_status DESC, created DESC LIMIT 0 , 3 ');
            foreach($videos as $video){
            //$videos_authors[] = new Author($video->fk_user);ç
            //miramos el fuente youtube o vimeo
                if($video->author_name =='vimeo'){
                    $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                    $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                    $return = curl_exec($curl);
                    $return = unserialize($return);
                    curl_close($curl);
                    $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                    $video->thumbnail_small = $return[0]['thumbnail_small'];
                }
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }

           
            $others_videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 0, 6');
            foreach($others_videos as $video){
            //$videos_authors[] = new Author($video->fk_user);ç
            //miramos el fuente youtube o vimeo
                 if($video->author_name =='vimeo'){
                    $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                    $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                    $return = curl_exec($curl);
                    $return = unserialize($return);
                    curl_close($curl);
                    $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                    $video->thumbnail_small = $return[0]['thumbnail_small'];                   
                 }
                 $video->category_name = $video->loadCategoryName($video->id);
                 $video->category_title = $video->loadCategoryTitle($video->id);
            }

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
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
            
            $others_videos = $cm->find('Video', 'available=1', 'ORDER BY created DESC LIMIT 6, 39');
            foreach($others_videos as $video){
                if($video->author_name =='vimeo'){
                    $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
                    $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
                    $return = curl_exec($curl);
                    $return = unserialize($return);
                    curl_close($curl);
                    $video->thumbnail_medium = $return[0]['thumbnail_medium'];
                    $video->thumbnail_small = $return[0]['thumbnail_small'];
                }
                $video->category_name = $video->loadCategoryName($video->id);
                $video->category_title = $video->loadCategoryTitle($video->id);
            }
	   // $others_videos= $cm->paginate_num_js($others_videos,5, 1, 'get_paginate_articles',"'videos',''");
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