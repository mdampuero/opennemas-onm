<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'FILE MANAGEMENT');

require_once('../../attachments_events.php');

\Onm\Module\ModuleManager::checkActivatedOrForward('FILE_MANAGER');

Acl::checkOrForward('FILE_ADMIN');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT, array('options' => array('default' => 1)));


/******************* GESTION CATEGORIAS  *****************************/
$contentType = Content::getIDContentType('attachment');

$category = filter_input(INPUT_GET,'category');
if(empty($category)) {
    $category = filter_input(INPUT_POST,'category',FILTER_VALIDATE_INT, array('options' => array('default' => 0 )));
}

$ccm = ContentCategoryManager::get_instance();
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($category, $contentType);

$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);
 

$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

 
switch($action) {
    case 'list':  
        if($category == 0) {
            $nameCategory = 'GLOBAL';  
            $cm = new ContentManager();
            $total_num_photos=0;
            $files = array();
            $size = array();
            $sub_size = array();
            $num_photos = array();
            $fullcat = $ccm->order_by_posmenu($ccm->categories);
            
            foreach($parentCategories as $k => $v) {
                $num_photos[$k]= $ccm->count_content_by_type($v->pk_content_category, $contentType);
                $total_num_photos += $num_photos[$k];
                $files[$v->pk_content_category] = $cm->find_all('Attachment',
                                         'fk_content_type = 3 AND category = '.$v->pk_content_category ,
                                         'ORDER BY created DESC' );
                if(!empty($fullcat)){
                    foreach($fullcat as $child) {
                        if($v->pk_content_category == $child->fk_content_category) {
                            $num_sub_photos[$k][$child->pk_content_category] = $ccm->count_content_by_type($child->pk_content_category, 3);
                            $total_num_photos += $num_sub_photos[$k][$child->pk_content_category];
                            $sub_files[$child->pk_content_category][] = $cm->find_all('Attachment',
                                             'fk_content_type = 3 AND category = '.$child->pk_content_category ,
                                             'ORDER BY created DESC' );
                            $aux_categories[] = $child->pk_content_category;
                            $sub_size[$k][$child->pk_content_category] = 0;
                            $tpl->assign('num_sub_photos', $num_sub_photos);
                        }
                    }
                }
            }

            //Calculo del tamaño de los ficheros por categoria/subcategoria
            $i = 0;
            $total_size = 0;
            foreach ($files as $categories => $contenido) {
                $size[$i] = 0;
                if (!empty($contenido)) {
                    foreach ($contenido as $value) {
                        if ($categories == $value->category) {
                            if (file_exists(MEDIA_PATH.'/'.FILE_DIR.'/'.$value->path)) {
                                $size[$i] += filesize(MEDIA_PATH.'/'.FILE_DIR.'/'.$value->path);

                            }
                        }
                    }
                }$total_size += $size[$i];
                $i++;
            }
            if(!empty($parentCategories) && !empty ($aux_categories)) {
                foreach($parentCategories as $k => $v) {                                  
                    foreach ($aux_categories as $ind) {
                        if (!empty ($sub_files[$ind][0])) {
                            foreach ($sub_files[$ind][0] as $value) {                                
                                if ($v->pk_content_category == $ccm->get_id($ccm->get_father($value->catName))) {
                                    if ($ccm->get_id($ccm->get_father($value->catName)) ) {
                                        $sub_size[$k][$ind] += filesize(MEDIA_PATH.'/'.FILE_DIR.'/'.$value->path);     

                                    }
                                }
                            }
                        }
                        if (isset($sub_size[$k][$ind])) {
                            $total_size += $sub_size[$k][$ind];
                        }
                    }
                }
            }
            
            $tpl->assign('total_img', $total_num_photos);
            $tpl->assign('total_size', $total_size);
            $tpl->assign('size', $size);
            $tpl->assign('sub_size', $sub_size);

            $tpl->assign('num_photos', $num_photos);
            $tpl->assign('categorys', $parentCategories);
            $tpl->assign('subcategorys', $subcat);

        } else {
            $cm = new ContentManager();
           
            list($attaches, $pager)= $cm->find_pages('Attachment', 'fk_content_type=3 ',
                                                     'ORDER BY  created DESC ',$page, ITEMS_PAGE,  $category);
            $tpl->assign('paginacion', $pager);

            $i = 0;

            $status = array();
            if($attaches) {
                foreach($attaches as $archivo) {
                      $dir_date =preg_replace("/\-/", '/', substr($archivo->created, 0, ITEMS_PAGE));
                      $ruta = MEDIA_PATH.'/'.FILE_DIR.'/'.$archivo->path ;

                    if (is_file($ruta)) {
                        $status[$i]='1'; //Si existe
                        $archivo->set_available(1, $_SESSION['userid']);
                    } else {
                        $status[$i]='0';
                        $archivo->set_available(0, $_SESSION['userid']);
                    }
                    $i++;
                }
            }

            $alert = (isset($_REQUEST['alerta']))? $_REQUEST['alerta'] : null;

            $tpl->assign('status', $status);
            $tpl->assign('attaches', $attaches);
            $tpl->assign('alerta', $alert);
        }

        $tpl->assign('category', $category);

        $tpl->display('files/list.tpl');
    break;

    case 'read':
        Acl::checkOrForward('FILE_UPDATE');
        $attaches = new Attachment($_REQUEST['id']);
        $tpl->assign('attaches', $attaches);

        $tpl->display('files/form.tpl');
    break;

    case 'update':
        Acl::checkOrForward('FILE_UPDATE');
        $att = new Attachment($_REQUEST['id']);
        $att->update(  $_REQUEST );
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    break;

    case 'delete':
         Acl::checkOrForward('FILE_DELETE');
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
            $msg="¿Está seguro que desea eliminar '".$att->title."' ?";
        //   $msg.='<br /><a href="'.$_SERVER['SCRIPT_NAME'].'?action=yesdel&id='.$_REQUEST['id'].'">  <img src="themes/default/images/ok.png" title="SI">  </a> ';
        //   $msg.='   <a href="#" onClick="hideMsgContainer(\'msgBox\');"> <img src="themes/default/images/no.png" title="NO">  </a></p>';
           echo $msg;
           exit(0);
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$att->category.'&message='.$msg.'&page='.$page);
     break;

    case 'yesdel':
        Acl::checkOrForward('FILE_DELETE');
        if($_REQUEST['id']){
            $att = new Attachment($_REQUEST['id']);
            //Delete relations
            $rel= new Related_content();
            $rel->delete_all($_REQUEST['id']);
            $att->delete( $_REQUEST['id'],$_SESSION['userid'] );
        }
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$att->category.'&page='.$page);
     break;


    case 'upload':

        Acl::checkOrForward('FILE_CREATE');
        set_time_limit(0);
        ini_set('upload_max_filesize',  20 * 1024 * 1024 );
        ini_set('post_max_size',        20 * 1024 * 1024 );
        ini_set('file_uploads',         'On'  );

        require_once('../../attachments_events.php');

        $tpl->assign('filterRegexp', '/^[a-z0-9\-_]+\.[a-z0-9]{2,4}$/i');


        if(isset($_FILES['path']['name'])
           && !empty($_FILES['path']['name'])) {

            $category = (isset($category)) ? $category : 0;

            $dateStamp = date('Ymd');
            $directoryDate =date("/Y/m/d/");
            $basePath = MEDIA_PATH.'/'.FILE_DIR.$directoryDate ;
            
            $fileName = $_FILES['path']['name'];
            $fileType   = $_FILES['path']['type'];
            $fileSize = $_FILES['path']['size'];
            $fileName = preg_replace('/[^a-z0-9_\-\.]/i', '-', strtolower($fileName));

            $data['title'] = $_POST['title'];
            $data['path'] = $directoryDate.$fileName;
            $data['category'] = $category;
            $data['available'] = 1;
            $data['description'] = $_POST['title'];
            $data['metadata'] = String_Utils::get_tags($_POST['title']);
            $data['fk_publisher'] = $_SESSION['userid'];

            // Create folder if it doesn't exist

            if( !file_exists($basePath) ) {
                mkdir($basePath, 0777, true);
            }
            
            // Move uploaded file
            $uploadStatus = move_uploaded_file($_FILES['path']['tmp_name'], $basePath.$fileName);

            if ($uploadStatus !== false) {

                $attachment = new Attachment();
                if ($attachment->create($data)) {
                    $msg = _("File created successfuly.");
                    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$category.'&page='.$page);
                } else {
                    $tpl->assign('message', _('A file with the same name already exists.') );
                }

            } else {
                $tpl->assign('message', _('There was an error while uploading the file. <br />Please, contact your system administrator.'));
            }

        } elseif (!isset($_GET['op'])) {
            $tpl->assign('message', _('Please select a file before send the form') );
        }

        $tpl->assign('category', $category);

        $tpl->display('files/new.tpl');

    break;

    default: {
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$category.'&page='.$page);
    } break;
}
