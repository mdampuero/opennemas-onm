<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require_once('core/method_cache_manager.class.php');


require_once('core/pc_content.class.php');
require_once('core/pc_content_manager.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_content_category_manager.class.php');

require_once('core/pc_photo.class.php');
require_once('core/pc_video.class.php');
require_once('core/pc_letter.class.php');
require_once('core/pc_opinion.class.php');
require_once('core/pc_user.class.php');
require_once('core/pc_poll.class.php');

//hemeroteca content_status=0 available=? favorite=0
// disponible available=1 content_status=?, favorite=0
//Favorito: available=1 content_status=1, favorite=1
/*********************************************************************************/


$tpl->assign('titulo_barra', 'Plan Conecta: Hemeroteca');

if (!isset($_REQUEST['mytype'])|| empty($_REQUEST['mytype'])) {$_REQUEST['mytype'] = 'pc_photo';} //Photo day


$_SESSION['pc_from']='pc_hemeroteca';
if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		
		case 'list':  //Buscar publicidad entre los content
                  
	 		$cc = new PC_ContentCategoryManager();
			$cm = new PC_ContentManager();
			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
                        //$contents = $cm->find($_REQUEST['mytype'], ' changed <"'.$ago24.'"', 'ORDER BY created DESC');
                        //Segun sea el tipo de contenidos contienen distitas secciones.
                        switch($_REQUEST['mytype']) {
                                case 'pc_photo':
                                        if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 1;} //Photo day
                                        $allcategorys = $cc->find_by_type('1', 'inmenu=1  and available=1', 'ORDER BY posmenu');
                                        $tpl->assign('allcategorys', $allcategorys);
                                        $datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);
                                        $tpl->assign('datos_cat', $datos_cat);
                                        $contents = $cm->find_by_category($_REQUEST['mytype'], $_REQUEST['category'], 'content_status=1', 'ORDER BY created DESC');

                                        break;
                                case 'pc_video':
                                        if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 3;} //Video day
                                        $allcategorys = $cc->find_by_type('2', 'inmenu=1 and available=1', 'ORDER BY posmenu');
                                        $tpl->assign('allcategorys', $allcategorys);
                                        $datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);
                                        $tpl->assign('datos_cat', $datos_cat);
                                        $contents = $cm->find_by_category($_REQUEST['mytype'], $_REQUEST['category'], 'content_status=1', 'ORDER BY created DESC');
                                        break;
                                case 'pc_poll':
                                        $contents = $cm->find($_REQUEST['mytype'], ' content_status=1', 'ORDER BY created DESC');
                                        break;
                                case 'pc_opinion':
                                        if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 6;} //Opinion
                                        $allcategorys = $cc->find_by_type('4', 'inmenu=1 and available=1', 'ORDER BY posmenu');
                                        $tpl->assign('allcategorys', $allcategorys);
                                        $datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);
                                        $tpl->assign('datos_cat', $datos_cat);
                                        $contents = $cm->find_by_category($_REQUEST['mytype'], $_REQUEST['category'], 'content_status=1', 'ORDER BY created DESC');
                                        break;
                                case 'pc_letter':
                                         if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 5;} //Cartas
                                        $allcategorys = $cc->find_by_type('3', 'inmenu=1 and available=1', 'ORDER BY posmenu');
                                        $tpl->assign('allcategorys', $allcategorys);
                                        $datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);
                                        $tpl->assign('datos_cat', $datos_cat);
                                        $contents = $cm->find_by_category($_REQUEST['mytype'], $_REQUEST['category'], 'content_status=1', 'ORDER BY created DESC');
                                        break;
                                        //$contents = $cm->find($_REQUEST['mytype'], ' content_status=1', 'ORDER BY created DESC');
                                			
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
		
		case 'read':
			
			Application::forward($_REQUEST['mytype'].'.php?action=read&id='.$_REQUEST['id'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
			
			break;
		case 'change_status':

			$contenido=new PC_Content($_REQUEST['id']);
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        $contenido->set_status($status);
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);

		break;
		case 'change_available':

			$contenido=new PC_Content($_REQUEST['id']);
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                        $contenido->set_available($status);
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
		break;		
	
		case 'delete':			
                        $contenido = new PC_Content($_REQUEST['id']);
                        $contenido->delete($_REQUEST['id']);
	     
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page'].'&category='.$_REQUEST['category'].'&mytype='.$_REQUEST['mytype']);
		break;

                //Recuperar multiples contents de la hemeroteca
		case 'm_restore':
   
                    if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
                    {
                        $fields = $_REQUEST['selected_fld'];
 
                        if(is_array($fields))
                        {
                            foreach($fields as $i )
                            {
                                    $contenido = new PC_Content($i);
                                    $contenido->set_status(0);
                            }
                        }
                    }
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;
		
		case 'mdelete':		
                        if($_REQUEST['id']==6){ //Eliminar todos
				$cm = new PC_ContentManager();
				$contents = $cm->find_by_category($_REQUEST['mytype'], $_REQUEST['category'], 'in_litter=1', 'ORDER BY created DESC ');
				foreach ($contents as $cont){		
					  $content = new PC_Content($cont->id);			        
					  $content->delete($cont->id);		
				}
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&page='.$_REQUEST['page'].'&category='.$_REQUEST['category']);
			}
					
			if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {			
			     $fields = $_REQUEST['selected_fld'];
 
                             if(is_array($fields)) {
                                  foreach($fields as $i ) {
                                            $contenido=new PC_Content($i);
                                            $contenido->delete($i);
                                     }
                              }
			  }
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&page='.$_REQUEST['page'].'&category='.$_REQUEST['category']);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}
    } else {
       Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
    }

$tpl->assign('MEDIA_CONECTA_WEB', MEDIA_CONECTA_WEB);
$tpl->display('pc_hemeroteca.tpl');
 