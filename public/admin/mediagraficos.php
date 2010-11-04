<?php
/* 
 * Change mediamanager to aproximate - used controller mediamanagerController
 */

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');
require_once(SITE_LIBS_PATH.'utils.functions.php');
require(SITE_CORE_PATH.'media.manager.class.php');

require('./mediamanagerController.php');
require('./mediagraficosController.php');


//????? Parche pq no pasa el action, lo borra en algun sitio
if (!empty($_REQUEST['acti']) && $_REQUEST['acti']=='searchResult') { $_REQUEST['action'] = "searchResult";}
if (!isset($_REQUEST['category']) || ($_REQUEST['category'] == 'GLOBAL' && $_REQUEST['action'] !='search' && $_REQUEST['action'] != "searchResult")) {
    $_REQUEST['category'] = 'GLOBAL';
     $_REQUEST['action'] = "list_categorys";
}

if( !Acl::_('IMAGE_ADMIN')){
    Acl::deny();
}

//$mm = new MediaManager();
$mmc = new MediagraficosController();
$mmc->action_init();

$action = (!isset($_REQUEST['action']))? 'list_today': $_REQUEST['action'];
///////////////?????
switch($action) {
     case 'list_categorys':
        $mmc->action_list_categorys();
        break;

     case 'list_today':
        $mmc->action_list_today();
        break;

     case 'list_all':
        $mmc->action_list_all();
        break;

     case 'save_data':
        $mmc->action_save_data();
        Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$mmc->category.'&page='.$_REQUEST['page']);
        break;

    case 'read':
    case 'image_data':
         $html_out=$mmc->action_read_image_data();
         $tpl=$mmc->tpl;
         $html_out= $tpl->fetch('image_data.tpl');
         Application::ajax_out($html_out);
        break;

    case 'results': //despues del add foto muestra results.
        $mmc->action_view_results();
        break;

    case 'addPhoto':
        if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {
            //Mriar el tema de mensajes en los fallos que deberia devolver.           
            $uploads=$mmc->action_addPhoto();
         if($uploads){
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=results&category='.$mmc->category.'&uploads='.$uploads.'&mensaje='.$mmc->alert);
            }
        } //if
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_today&category='.$mmc->category.'&page='.$_REQUEST['page'].'&mensaje='.$mmc->alert);
       break;

    case 'updateDatasPhotos':
        $mmc->action_updateDatasPhotos();
        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_today&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

    case 'delFile':
        $mmc->action_delPhoto($_REQUEST['id'],$_SESSION['userid']);
        Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&alerta='.$mmc->msg.'&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
       break;

    case 'search': {

    } break;

     case 'searchResult': {

        $name=$mmc->action_searchResult();


    } break;


    case 'mdelete': {
            $msg="Las photos ";
            if($_REQUEST['id']==6){ //Eliminar todos
                $cm = new ContentManager();
                $photos = $cm->find_by_category('Photo', $mmc->category , 'fk_content_type=8 AND   photos.media_type="image"', 'ORDER BY created DESC');

                if(count($photos)>0){
                    foreach ($photos as $art){
                        $photo = new Photo($art->id);
                        $photo->delete($art->id,$_SESSION['userid'] );
                    }
                }


                Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$mmc->category.'&page='.$_REQUEST['page']);
            }else{
                $fields = $_REQUEST['selected_fld'];
                if(isset($fields) && count($fields)>0) {
                    $nodels=array();
                    $alert='';
                    if(is_array($fields)) {
                        foreach($fields as $i ) {
                            $photo = new Photo($i);
                            $photo->delete($i,$_SESSION['userid'] );

                        }

                    }
                }
            }

            $msg.=" tiene relacionados.  !Eliminelos uno a uno!";

            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$mmc->category.'&alert='.$alert.'&msg='.$msg.'&page='.$_REQUEST['page']);

        } break;
}

$tpl=$mmc->tpl;
$tpl->assign('category', $mmc->category);
$tpl->assign('action', $action);
$tpl->display('mediamanager.tpl');
