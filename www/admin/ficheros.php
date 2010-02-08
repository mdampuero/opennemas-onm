<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Gesti&oacute;n de Ficheros Adjuntos');

require_once('attachments_events.php');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/attach_content.class.php');
require_once('core/attachment.class.php');

require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

if(!isset($_REQUEST['category'])) {
    $_REQUEST['category'] = 0; // GLOBAL
}

$tpl->assign('category', $_REQUEST['category']);

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu();

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);

$name_cat = $ccm->get_name($_REQUEST['category']);
$ruta = "../media/files/".$name_cat."/";

//if( !in_array('MUL_ADMIN', $_SESSION['privileges'])) {
//    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes');
//}

// TODO: revisar validez de este código
require_once('core/privileges_check.class.php');
if( !Privileges_check::CheckPrivileges('NOT_ADMIN')) {
    Privileges_check::AccessDeniedAction();
}

//if (!isset($_REQUEST['alerta'])) {$_REQUEST['alerta'] = 0;}
if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list': {
            if($_REQUEST['category'] == 0) {
                $nameCat = 'GLOBAL'; //Se mete en litter pq category 0
                
                $fullcat = $ccm->order_by_posmenu($ccm->categories);
                foreach($parentCategories as $k => $v) {
                    $num_photos[$k]= $ccm->count_content_by_type($v->pk_content_category, 3);
                
                    foreach($fullcat as $child) {
                        if($v->pk_content_category == $child->fk_content_category) {
                            $num_sub_photos[$k][] = $ccm->count_content_by_type($child->pk_content_category, 3);
                        }
                    }
                }
                
                //Especiales
                $j = 0;
                $especials = array( 8 => 'portadas' );
                foreach($especials as $key => $cat) {
                    $num_especials[$j]['title'] = $cat;
                    $num_especials[$j]['num']   = $ccm->count_content_by_type($key,3);
                    $j++;
                }
                
                $tpl->assign('especials', $especials);
                $tpl->assign('num_especials', $num_especials);
                $tpl->assign('num_photos', $num_photos);
                $tpl->assign('num_sub_photos', $num_sub_photos);
                $tpl->assign('categorys', $parentCategories);
                $tpl->assign('subcategorys', $subcat);
                
            } else {
                $cm = new ContentManager();
                //    $attaches = $cm->find_by_category('Attachment', $_REQUEST['category'] , 'fk_content_type=3 ', 'ORDER BY created DESC');
                //    $attaches = $cm->paginate_num($attaches,12);
                //ContentManager::find_pages(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>,<PAGE>,<ITEMS_PER_PAGE>,<CATEGORY>);
                list($attaches, $pager)= $cm->find_pages('Attachment', 'fk_content_type=3 ',
                                                         'ORDER BY  created DESC ',$_REQUEST['page'],10,  $_REQUEST['category']);
                $tpl->assign('paginacion', $pager);
                
                $i = 0;
                
                $status = array();
                if($attaches) {
                    foreach($attaches as $archivo) {
                        if (is_file($ruta.$archivo->path)) {
                            $status[$i]='1'; //Si existe
                            $archivo->set_available(1, $_SESSION['userid']);
                        } else {
                            $status[$i]='0';
                            $archivo->set_available(0, $_SESSION['userid']);
                        }
                        $i++;
                    }
                }
                
                $tpl->assign('status', $status);
                $tpl->assign('attaches', $attaches);
                $tpl->assign('alerta', $_REQUEST['alerta']);
            }
            
            $tpl->assign('category', $_REQUEST['category']);
        } break;

        case 'read':
            $att = new Attachment();
            $attaches = new Attachment($_REQUEST['id']);
            $tpl->assign('attaches', $attaches);
        break;
        
        case 'update':
            $att = new Attachment();
            $att->update(  $_REQUEST );
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        case 'delete': 
            //Mirar si tiene relacion
            $att = new Attachment($_REQUEST['id']);
            $rel= new Related_content();
            $relationes=array();
            $relationes = $rel->get_content_relations( $_REQUEST['id'] );//de portada             
            if(!empty($relationes)){
                $msg = "El fichero  '".$att->title."' , está relacionado con los siguientes articulos:  ";
                $cm= new ContentManager();
                $relat = $cm->getContents($relationes);
                foreach($relat as $contents) {
                      $msg.="\n - ".strtoupper($contents->category_name).": ".$contents->title;
                }
                $msg.="\n \n ¡Ojo! Si borra el fichero se eliminaran las relaciones del fichero con los articulos";
                $msg.="\n ¿Desea eliminarlo igualmente?";
            //    $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
            //    $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
                echo $msg;
                exit(0);
            }else{
               $msg.="¿Está seguro que desea eliminar '".$att->title."' ?";
            //   $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
            //   $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
               echo $msg;
               exit(0);
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$att->category.'&message='.$msg.'&page='.$_REQUEST['page']);
         break;

        case 'yesdel':
            if($_REQUEST['id']){
                $att = new Attachment($_REQUEST['id']);
                //Delete relations
                $rel= new Related_content();
                $rel->delete_all($_REQUEST['id']);
                $att->delete( $_REQUEST['id'],$_SESSION['userid'] );
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$att->category.'&page='.$_REQUEST['page']);
         break;

        default: {
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        } break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
}

$tpl->display('ficheros.tpl');

