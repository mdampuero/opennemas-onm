<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('libs/utils.functions.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gesti&oacute;n de Secciones');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');


$ccm = new ContentCategoryManager();
//Los saca para los select.
// ContentCategoryManager::find( <CLAUSE_WHERE>, <CLAUSE_ORDER>);

$allcategorys = $ccm->find('internal_category != 0 AND internal_category != 4 AND fk_content_category =0', 'ORDER BY posmenu');
$tpl->assign('allcategorys', $allcategorys);
/*foreach( $allcategorys as $prima) {
    $subcat[] = $cc->find(' inmenu=1  AND internal_category=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');
}
$tpl->assign('subcat', $subcat);
 */

// ¿?¿?¿?¿?¿?¿?¿?¿?
if (!isset($resp)) {
    $resp = 1;
}


//if( !in_array('USR_ADMIN',$_SESSION['privileges']))
//if(!Acl::_('USR_ADMIN'))
//{
//    Application::forward($_SERVER['HTTP_REFERER'].'?action=list_pendientes');
//}

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':
			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
		//	$categorys = $ccm->find(' internal_category=1 AND fk_content_category = 0', 'ORDER BY inmenu DESC, posmenu ASC, pk_content_category');
			// FIXME: Set pagination
		
			$i=0;
			$num_contents=array();
            
                        // Contabilizar por grupos
                        $groups['articles'] = $ccm->count_content_by_type_group(1);
                        $groups['photos']   = $ccm->count_content_by_type_group(8);
                        $groups['advertisements'] = $ccm->count_content_by_type_group(2);
            
			foreach($allcategorys as $cate) {
                            if($cate->internal_category !=0 && $cate->internal_category !=4){
                                $num_contents[$i]['articles']       = $groups['articles'][$cate->pk_content_category];
				$num_contents[$i]['photos']         = $groups['photos'][$cate->pk_content_category];
				$num_contents[$i]['advertisements'] = $groups['advertisements'][$cate->pk_content_category];
				$categorys[$i]=$cate;

                                $resul = $ccm->find('internal_category=1 AND fk_content_category ='.$cate->pk_content_category, 'ORDER BY  inmenu DESC, posmenu ASC');
				$j=0;
				foreach($resul as $cate) {						
                                    $num_sub_contents[$i][$j]['articles']       = $groups['articles'][$cate->pk_content_category];
                                    $num_sub_contents[$i][$j]['photos']         = $groups['photos'][$cate->pk_content_category];
                                    $num_sub_contents[$i][$j]['advertisements'] = $groups['advertisements'][$cate->pk_content_category];

                                    $j++;
				}
				$subcategorys[$i]=$resul;                              
                                $i++;
                            }
			}

                        $tpl->assign('categorys', $categorys);
			$tpl->assign('num_contents', $num_contents);
			$tpl->assign('num_sub_contents', $num_sub_contents);
			$tpl->assign('subcategorys', $subcategorys);
			$i=0;			
                       
			$tpl->assign('ordercategorys', $allcategorys);
                        
		break;

		case 'new':
                        $tpl->assign('formAttrs', 'enctype="multipart/form-data"');
			// Nada
		break;

		case 'read': //habrá que tener en cuenta el tipo
                        $tpl->assign('formAttrs', 'enctype="multipart/form-data"');
			$category = new ContentCategory( $_REQUEST['id'] );
			$tpl->assign('category', $category);
			$subcategorys = $ccm->find('fk_content_category ='.$_REQUEST['id'], 'ORDER BY fk_content_category,posmenu');
			$tpl->assign('subcategorys', $subcategorys);
		break;

		case 'update':
			$category = new ContentCategory();
                        $nameFile = $_FILES['logo_path']['name'];
                        if(!empty($nameFile)){
                            $uploaddir="../media/sections/".$nameFile;
                            if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                                $_REQUEST['logo_path'] = $nameFile;
                            }else{
                                 $_REQUEST['logo_path'] ='';
                            }
                        }
			$category->update( $_REQUEST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;

		case 'create':
			$category = new ContentCategory();
                        $nameFile = $_FILES['logo_path']['name'];
                        $uploaddir="../media/sections/".$nameFile;
                        if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                            $_POST['logo_path'] = $nameFile;
                        }else{
                             $_POST['logo_path'] ='';
                        }

			if($men=$category->create( $_POST )) {				
			 	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$men);
			} else {
                              $tpl->assign('errors', $category->errors);
			}
		break;

		case 'delete':
			$category = new ContentCategory();
			$resp=$category->delete( $_POST['id'] );
			
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$resp);
		break;
		
		case 'set_inmenu':           
			$category = new ContentCategory($_REQUEST['id']);
			// FIXME: evitar otros valores erróneos
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			$category->set_inmenu($status);

		 	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;

		case 'validate':
			if(empty($_POST["id"])) {
				$category = new ContentCategory();
				if(!$category->create( $_POST ))		
						$tpl->assign('errors', $category->errors);	
			} else {
				$category = new ContentCategory();
				$category->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$category->pk_content_category);
		break;

		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('category.tpl');