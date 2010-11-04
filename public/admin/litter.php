<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once(SITE_CORE_PATH.'application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Papelera de elementos');
require_once(SITE_CORE_PATH.'img_galery.class.php');
require_once(SITE_CORE_PATH.'album_photo.class.php');

if (!isset($_REQUEST['page']) || empty($_REQUEST['page'])) {$_REQUEST['page'] = 1;}
if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 1;}
$tpl->assign('category', $_REQUEST['category']);


$cc = new ContentCategoryManager();
// ContentCategoryManager::find( <CLAUSE_WHERE>, <CLAUSE_ORDER>);
			$allcategorys = $cc->find('inmenu=1', 'ORDER BY posmenu');			
			// FIXME: Set pagination
			$tpl->assign('allcategorys', $allcategorys);
			$datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);	
			$tpl->assign('datos_cat', $datos_cat);


if (!isset($_REQUEST['mytype'])) {$_REQUEST['mytype'] = 'article';}
$tpl->assign('mytype', $_REQUEST['mytype']);


//if( !in_array('USR_ADMIN',$_SESSION['privileges']))
//{
//    Application::forward($_SERVER['HTTP_REFERER'].'?action=list_pendientes');
//}

if(isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':
			$cm = new ContentManager();
			
			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
		
			$types_content = $cm->get_types();
		
			$tpl->assign('types_content', $types_content);
		
			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
            $secciones=array();
			//$litterelems= $cm->find($_REQUEST['mytype'], 'in_litter=1', 'ORDER BY archive DESC ');
            list($litterelems, $pager)= $cm->find_pages($_REQUEST['mytype'], 'in_litter=1', 'ORDER BY changed DESC ',$_REQUEST['page'],20);
            $content = new Content();
            foreach($litterelems as $elem ) {

                            $category = $content->loadCategoryName($elem->id);
                            $nameCat = $content->loadCategoryTitle($elem->id);
			     		  
			     		 	$secciones[]= $nameCat;
			     
                        }
                      
                        
                      //  $litterelems = $cm->paginate($litterelems);
                        $tpl->assign('paginacion', $pager);
                        $tpl->assign('secciones', $secciones);
                        //$litterelems = $cm->paginate($litterelems);
                        $tpl->assign('litterelems', $litterelems);
                         /* Ponemos en la plantilla la referencia al objeto pager */
                        
	
		break;

		case 'm_no_in_litter':
   
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {			
			     $fields = $_REQUEST['selected_fld'];
 
		         if(is_array($fields)) {
			      foreach($fields as $i ) {			     		       		
			     		 	$contenido=new content($i);
			     		 	$contenido->no_delete($i,$_SESSION['userid']);      
			         } 
        		    }
			  }
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&page='.$_REQUEST['page']);
		break;
		
		case 'no_in_litter':		   			
                        $contenido=new Content($_REQUEST['id']);
                        $contenido->no_delete($_REQUEST['id'],$_SESSION['userid']);

                        if($_REQUEST['desde']=='search'){
                            $name = $GLOBALS['application']->conn->GetOne('SELECT name FROM `content_types` WHERE pk_content_type = "'. $contenido->content_type.'"');
                            $archive_php=strtolower($name).'.php'; //Nombre de la clase
                            $action='list';
                            if($name=='article'){$action='list_pendientes';}
                            Application::forward($archive_php.'?action='.$action.'&category='.$_REQUEST['category']);
                        }else{
                            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&page='.$_REQUEST['page']);
                        }
		break;
		
		case 'remove':
			$contenido = new Content($_POST['id']);
					     	
	     		$type=$contenido->content_type;
	     		$name = $GLOBALS['application']->conn->
   				 	GetOne('SELECT name FROM `content_types` WHERE pk_content_type = "'. $type.'"');
	     
	     		$name_type=ucwords($name); //Nombre de la clase			     	
		     	$eleto=new $name_type($_POST['id']); //Llamamos a la clase
		     	
		     	$eleto->remove($_POST['id']); // eliminamos

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
		
		
		case 'mremove':		
		       if($_REQUEST['id']==6){ //Eliminar todos
				$cm = new ContentManager();
				$contents = $cm->find($_REQUEST['mytype'], 'in_litter=1', 'ORDER BY created DESC ');
				foreach ($contents as $cont){		
					  $content = new Content($cont->id);			        
					  $content->remove($cont->id);		
				}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&page='.$_REQUEST['page']);
							}
					
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {			
			     $fields = $_REQUEST['selected_fld'];
 
		         if(is_array($fields)) {
			      foreach($fields as $i ) {
			     	 
			     		$contenido=new Content($i);
			     			     	
			     		$type=$contenido->content_type;
			     		
			     		 $name = $GLOBALS['application']->conn->
       					 	GetOne('SELECT name FROM `content_types` WHERE pk_content_type = "'. $type.'"');
			     	
			     		$name_type=ucwords($name); //Nombre de la clase			     	
			     		$eleto=new $name_type($i); //Llamamos a la clase
			     	//	print_r($eleto);
			     		$eleto->remove($i); // eliminamos
			         } 
        		    }
			  }
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&mytype='.$_REQUEST['mytype'].'&page='.$_REQUEST['page']);
		break;
		

		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
}

$tpl->display('litter.tpl');
?>
