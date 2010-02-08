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
require_once('core/pc_poll.class.php');
require_once('core/pc_user.class.php');

/*********************************************************************************/


if (!isset($_REQUEST['mytype'])) {$_REQUEST['mytype'] = 'pc_photo';} //Photo day
 


$tpl->assign('titulo_barra', 'Plan Conecta: Papelera');
$cm = new PC_ContentManager();
if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {		
		case 'list': 
			$cc = new PC_ContentCategoryManager();
			if( isset($_REQUEST['mytype']) ) {
				switch($_REQUEST['mytype']) {
					case 'pc_photo':
						if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 1;} //Photo day												
						$allcategorys = $cc->find_by_type('1', 'inmenu=1', 'ORDER BY posmenu');					
						$tpl->assign('allcategorys', $allcategorys);
						$datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);	
						$tpl->assign('datos_cat', $datos_cat);
						// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
						$contents = $cm->find_by_category('PC_Photo', $_REQUEST['category'], 'in_litter=1', 'ORDER BY created DESC');

						break;
						
					case 'pc_video':
						if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 3;} //Video day
						
						$allcategorys = $cc->find_by_type('2', 'inmenu=1', 'ORDER BY posmenu');					
						$tpl->assign('allcategorys', $allcategorys);
						$datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);	
						$tpl->assign('datos_cat', $datos_cat);
						$contents = $cm->find_by_category('PC_Video', $_REQUEST['category'], 'in_litter=1', 'ORDER BY fk_pc_content_category, created DESC');
					break;
					
					case 'pc_opinion':
						$opinions = $cm->find('PC_Opinion', 'in_litter=1', 'ORDER BY created DESC');
					break;
					
					case 'pc_letter':
						$letters = $cm->find('PC_Letter', 'in_litter=1', 'ORDER BY created DESC');										
					break;
					case 'pc_poll':					
						// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
						$polls = $cm->find('PC_Poll',  'in_litter=1', 'ORDER BY created DESC');						
						$tpl->assign('polls', $polls);
			
					break;
				}
			}	
			if(!empty($contents)){
				$contents = $cm->paginate_num($contents,10);
				$tpl->assign('paginacion', $cm->pager);
			}
                        $tpl->assign('contents', $contents);
                        
                        $users = PC_User::get_instance();
                        $conecta_users = $users->get_all_authors();
                        $tpl->assign('conecta_users', $conecta_users);

			$tpl->assign('datos_cat', $datos_cat);
			$tpl->assign('category', $_REQUEST['category']);
			$tpl->assign('mytype', $_REQUEST['mytype']);
                break;
		
		case 'no_in_litter':		   			
                    $contenido=new PC_Content($_REQUEST['id']);
                    $contenido->no_delete($_REQUEST['id']);

                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
		break;
		
		case 'remove':			
                    $contenido = new PC_Content($_REQUEST['id']);
                    $contenido->remove($_REQUEST['id']);

                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
		
		case 'm_no_in_litter':  
                    if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
                     $fields = $_REQUEST['selected_fld'];

                     if(is_array($fields)) {
                         foreach($fields as $i ) {
                            $contenido=new PC_Content($i);
                            $contenido->no_delete($i);
                         }
                     }
                    }
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
		break;
		
		case 'mremove':		
                    if($_REQUEST['id']==6){ //Eliminar todos
                        $cm = new PC_ContentManager();
                        $contents = $cm->find_by_category($_REQUEST['mytype'], $_REQUEST['category'], 'in_litter=1', 'ORDER BY created DESC ');
                        foreach ($contents as $cont){
                            $content = new PC_Content($cont->id);
                            $content->remove($cont->id);
                        }
                        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
                    }
					
                    if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
                        $fields = $_REQUEST['selected_fld'];
                        if(is_array($fields)) {
                          foreach($fields as $i ) {
                              $contenido=new PC_Content($i);
                              $contenido->remove($i);
                          }
                        }
                    }
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
		break;
	
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}
} else {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->assign('MEDIA_CONECTA_WEB', MEDIA_CONECTA_WEB);
$tpl->display('pc_litter.tpl');
	

