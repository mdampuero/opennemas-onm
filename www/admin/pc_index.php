<?php
//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require_once('core/method_cache_manager.class.php');
require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_content_category_manager.class.php');
require_once('core/pc_photo.class.php');
require_once('core/pc_video.class.php');
require_once('core/pc_letter.class.php');
require_once('core/pc_opinion.class.php');
require_once('core/pc_user.class.php');
require_once('core/pc_poll.class.php');

/*********************************************************************************/
 // $alltypes=array(1=>'foto',2=>'video',3=>'carta',4=>'opinion',6=>'enquisa');

$tpl->assign('titulo_barra', 'Plan Conecta: Secciones y Contenidos de la portada');

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
	case 'list':
            $cc = new PC_ContentCategoryManager();
            $cm = new PC_ContentManager();

            //'pc_photo':
            $allcategorys = $cc->find_by_type('1', 'inmenu=1 and available=1', 'ORDER BY posmenu');
            $photo_categorys= array();
            foreach($allcategorys as $category){             
                $id=$category->pk_content_category;
                $photo_categorys[$id]->title= $category->title;
                $photo_categorys[$id]->contents = $cm->find_by_category('pc_photo', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC   LIMIT 0, 6');
            }
            $tpl->assign('photo_categorys', $photo_categorys);

            // 'pc_video':
            $allcategorys = $cc->find_by_type('2', 'inmenu=1  and available=1', 'ORDER BY posmenu');
            $video_categorys= array();
            foreach($allcategorys as $category){
                $id=$category->pk_content_category;
                $video_categorys[$id]->title= $category->title;
                $video_categorys[$id]->contents = $cm->find_by_category('pc_video', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC   LIMIT 0, 6');
            }
            $tpl->assign('video_categorys', $video_categorys);

            //'pc_opinion':
            $allcategorys = $cc->find_by_type('4', 'inmenu=1 and available=1', 'ORDER BY posmenu');
            $opinion_categorys= array();
            foreach($allcategorys as $category){
                $id=$category->pk_content_category;
                $opinion_categorys[$id]->title= $category->title;
                $opinion_categorys[$id]->contents = $cm->find_by_category('pc_opinion', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC  LIMIT 0, 6');
            }
            $tpl->assign('opinion_categorys', $opinion_categorys);

            //'pc_letter':
            $allcategorys = $cc->find_by_type('3', 'inmenu=1 and available=1', 'ORDER BY posmenu');
            $letter_categorys= array();
            foreach($allcategorys as $category){
                $id=$category->pk_content_category;
                $letter_categorys[$id]->title= $category->title;
                $letter_categorys[$id]->contents = $cm->find_by_category('pc_letter', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC  LIMIT 0, 6');
            }
            $tpl->assign('letter_categorys', $letter_categorys);

            //'pc_poll': //category 7 enquisa
            $polls = $cm->find('PC_Poll', 'content_status=1 and available=1', 'ORDER BY changed DESC LIMIT 0,9');
            $tpl->assign('polls', $polls);

            $users = PC_User::get_instance();
            $conecta_users = $users->get_all_authors();
            $tpl->assign('conecta_users', $conecta_users);
 
 
		/*	$cm = new PC_ContentManager();
			// Visualiza la favotita y las 6 siguientes
			$photoDia = $cm->find_by_category_name('PC_Photo', 'foto-dia', 'content_status=1 and available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0, 1');
                        $photoDenuncia = $cm->find_by_category_name('PC_Photo', 'foto-denuncia', 'content_status=1 and available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0, 1');
			$tpl->assign('photoDia', $photoDia[0]);
                        $tpl->assign('photoDenuncia', $photoDenuncia[0]);
	 		if(!$photoDia[0]->pk_pc_photo){$photoDia[0]->pk_pc_photo=0;}
                        if(!$photoDenuncia[0]->pk_pc_photo){$photoDenuncia[0]->pk_pc_photo=0;}
			$photos = $cm->find('PC_Photo', 'content_status=1 and available=1 and pk_pc_photo <> '.$photoDia[0]->pk_pc_photo.'  and fk_pc_content_category=1', 'ORDER BY changed DESC LIMIT 0,6');
			$photosde = $cm->find('PC_Photo', 'content_status=1 and available=1 and pk_pc_photo <> '.$photoDenuncia[0]->pk_pc_photo.' and fk_pc_content_category=2', 'ORDER BY changed DESC LIMIT 0,6');		
			$tpl->assign('photos', $photos);
			$tpl->assign('photosde', $photosde);
			
			
			$videoDenuncia = $cm->find_by_category_name('PC_Video', 'video-denuncia', 'content_status=1 and available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
                        $videoDia = $cm->find_by_category_name('PC_Video', 'video-dia', 'content_status=1 and available=1 and favorite=1', 'ORDER BY created DESC LIMIT 0, 1');
			$tpl->assign('videoDenuncia', $videoDenuncia[0]);
			$tpl->assign('videoDia', $videoDia[0]);
			if(!$videoDia[0]->pk_pc_video){$videoDia[0]->pk_pc_video=0;}
                        if(!$videoDenuncia[0]->pk_pc_video){$videoDenuncia[0]->pk_pc_video=0;}
			$videos = $cm->find('PC_Video', 'content_status=1  and available=1 and  pk_pc_video <> '.$videoDia[0]->pk_pc_video.' and fk_pc_content_category=3', 'ORDER BY fk_pc_content_category, favorite DESC, changed DESC LIMIT 0,6');
			$videosde = $cm->find('PC_Video', 'content_status=1  and available=1 and  pk_pc_video <> '.$videoDenuncia[0]->pk_pc_video.' and fk_pc_content_category=4', 'ORDER BY fk_pc_content_category, favorite DESC, changed DESC LIMIT 0,6');			
			
			$tpl->assign('videos', $videos);
			$tpl->assign('videosde', $videosde);
			
			$opinion_fav = $cm->find('PC_Opinion', 'content_status=1 and available=1  and favorite=1', 'ORDER BY changed DESC LIMIT 0,1');
			$tpl->assign('opinion_fav', $opinion_fav[0]);
			$letter_fav = $cm->find('PC_Letter', 'content_status=1 and available=1 and favorite=1', 'ORDER BY changed DESC LIMIT 0,1');
			$tpl->assign('letter_fav', $letter_fav[0]);
			if(!$opinion_fav[0]->pk_pc_opinion){$opinion_fav[0]->pk_pc_opinion=0;}
                        if(!$letter_fav[0]->pk_pc_letter){$letter_fav[0]->pk_pc_letter=0;}
			
                        $opinions = $cm->find('PC_Opinion', 'content_status=1 and available=1 and pk_pc_opinion <> '.$opinion_fav[0]->pk_pc_opinion.' ', 'ORDER BY changed DESC LIMIT 0,6');
			$tpl->assign('opinions', $opinions);
			
			$letters = $cm->find('PC_Letter', 'content_status=1 and available=1 and pk_pc_letter <> '.$letter_fav[0]->pk_pc_letter.' ', 'ORDER BY changed DESC LIMIT 0,6');				
			$tpl->assign('letters', $letters);
			
			$polls = $cm->find('PC_Poll', 'content_status=1 and available=1', 'ORDER BY changed DESC LIMIT 0,9');
			$tpl->assign('polls', $polls);/
                 * 
                 * */

        break;


        default:
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}


$tpl->display('pc_index.tpl');
	
