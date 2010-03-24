<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Gesti&oacute;n de Videos');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/video.class.php');
require_once('core/author.class.php');
require_once('core/privileges_check.class.php');



$fk_publisher=$_SESSION['userid'];
$tpl->assign('fk_publisher', $fk_publisher);

$publisher = new Author( $fk_publisher );	
$tpl->assign('author', $publisher->name);

if( !Privileges_check::CheckPrivileges('MUL_ADMIN'))
{
    Privileges_check::AccessDeniedAction();
}
/* GESTION CATEGORIAS */
$ccm = new ContentCategoryManager();
if (!isset($_REQUEST['category'])) {
    $this_category_data = $ccm->cache->find(' fk_content_category=0 AND inmenu=1 AND (internal_category =1 OR internal_category = 5)', 'ORDER BY internal_category DESC, posmenu ASC LIMIT 0,1');
    $_REQUEST['category'] = $this_category_data[0]->pk_content_category;
}

$allcategorys = $ccm->find('(internal_category=1 OR internal_category=5) AND fk_content_category=0', 'ORDER BY internal_category DESC, posmenu');
//var_dump($allcategorys);
$i=0;
foreach( $allcategorys as $prima) {
    $subcat[$i]=$ccm->find(' inmenu=1  AND internal_category=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');
      $i++;
}

$datos_cat = $ccm->find('pk_content_category='.$_REQUEST['category'], NULL);

$tpl->assign('category', $_REQUEST['category']);
$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $allcategorys);
$tpl->assign('datos_cat', $datos_cat);


/* GESTION CATEGORIAS  */

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':  //Buscar publicidad entre los content
			$cm = new ContentManager();
		     	// ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
			list($videos, $pager)= $cm->find_pages('Video', 'fk_content_type=9 ', 'ORDER BY  created DESC ',$_REQUEST['page'],10, $_REQUEST['category']);
			$i=0;
			foreach($videos as $video){
			
				$authors[$i] = new Author($video->fk_user);
                $video->category_name = $video->loadCategoryName($video->pk_content);
                
				$i++;
			}
		
			/* Ponemos en la plantilla la referencia al objeto pager */
			$tpl->assign('paginacion', $pager);
			$tpl->assign('videos', $videos);
			$tpl->assign('authors', $authors);

             
		break;

		case 'new':
			// Nada
				$aut=new Author();
				$todos=$aut->all_authors();
				$tpl->assign('todos', $todos);
		break;

		case 'read': //habrá que tener en cuenta el tipo
			$video = new Video( $_REQUEST['id'] );
			
			$elauthor = new Author( $video->fk_user );
			$tpl->assign('video', $video);
			$tpl->assign('elauthor', $elauthor->name);
			$aut=new Author();
			$todos=$aut->all_authors();
			$tpl->assign('todos', $todos);
				
		break;

		case 'update':
			$video = new Video();			
			$video->update( $_REQUEST );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;

		case 'create':
			$video = new Video();
			if($video->create( $_POST )) {
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
			} else {
				$tpl->assign('errors', $video->errors);
			}
		break;
		
		case 'validate':
			$video = null;
			if(empty($_POST["id"])) {
				$video = new Video();
				//Estamos creando un nuevo artículo
				if(!$video->create( $_POST ))
					$tpl->assign('errors', $video->errors);		
			} else {
				$video = new Video($_POST["id"]);
				//Estamos atualizando un artículo
				$video->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$video->id);
		break;
 
                case 'delete':
			$video = new Video($_REQUEST['id']);
                        $rel= new Related_content();
                        $relationes=array();
                        $relationes = $rel->get_content_relations( $_REQUEST['id'] );//de portada
                        if(!empty($relationes)){
                             $msg = "El video '".$video->title."', está relacionado con los siguientes articulos:  ";
                             $cm= new ContentManager();
                             $relat = $cm->getContents($relationes);
                             foreach($relat as $contents) {
                                   $msg.="\n - ".strtoupper($contents->category_name).": ".$contents->title;
                             }
                             $msg.="\n \n ¡Ojo! Si lo borra, se eliminaran las relaciones del video con los articulos";
                             $msg.="\n ¿Desea eliminarlo igualmente?";
                          //   $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                           //  $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                             echo $msg;
                             exit(0);
                        }else{
                            $msg.="¿Está seguro que desea eliminar '".$video->title."' ?";
                           // $msg.='\n <a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
                          //  $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                            echo $msg;
                            exit(0);
                        }
		break;

                case 'yesdel':
                    if($_REQUEST['id']){
                       $video = new Video($_REQUEST['id']);
                        //Delete relations
                        $rel= new Related_content();
                        $rel->delete_all($_REQUEST['id']);
                        $video->delete( $_REQUEST['id'],$_SESSION['userid'] );
                    }
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$video->category.'&page='.$_REQUEST['page']);
                 break;
		
		case 'set_position':
			$video = new Video($_REQUEST['id']);
			$video->set_position($_REQUEST['posicion'],$_SESSION['userid']);
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;

		case 'change_status':
			$video = new Video($_REQUEST['id']);
			//Publicar o no, 
			$status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			//$video->set_status($status,$_SESSION['userid']);
			$video->set_available($status, $_SESSION['userid']);
			
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
	
		case 'mfrontpage':		
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			    	$fields = $_REQUEST['selected_fld'];
					$status = ($_REQUEST['status']==1)? 1: 0; 
		            if(is_array($fields)) {
					      foreach($fields as $i ) {
					        $video = new Video($i);
					        //$video->set_status($_REQUEST['id'],$_SESSION['userid']);   //Se reutiliza el id para pasar el estatus
					        $video->set_available($status, $_SESSION['userid']); 
					        }
        		    }
			  }

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
		break;
		
		case 'mdelete':
		
			  if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
			  {
			    $fields = $_REQUEST['selected_fld'];
                            if(is_array($fields)) {
                                  $msg="Los videos ";
                                 $alert='';
				       foreach($fields as $i ) {
					        $video = new Video($i);	
                                                $rel= new Related_content();
                                                $relationes=array();

                                                $relationes = $rel->get_content_relations( $i );//de portada

                                                if(!empty($relationes)){
                                                     $nodels[] =$i;
                                                     $alert='ok';
                                                     $msg .= " \"".$video->title."\", ";

                                                }else{
                                                   $video->delete( $i,$_SESSION['userid'] );
                                                 }
                                       }
                             }
			  }
                               $msg.=" tiene relacionados.  !Eliminelos uno a uno!";
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&alert='.$alert.'&msgdel='.$msg.'&page='.$_REQUEST['page']);
		break;
		
		default:
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
		break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
}

$tpl->display('video.tpl');
?>
