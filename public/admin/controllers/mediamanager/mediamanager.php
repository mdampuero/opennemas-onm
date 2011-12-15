<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;

/**
 * Setup app
*/

require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');
 
require_once(SITE_CORE_PATH.'privileges_check.class.php');

require(dirname(__FILE__).'/mediamanagerController.php');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

//????? Parche pq no pasa el action, lo borra en algun sitio
if (!empty($_REQUEST['acti']) && $_REQUEST['acti']=='searchResult'){ 
    $_REQUEST['action'] = "searchResult";
    unset($_SESSION['where']);
}

if(isset($_REQUEST['listmode']) && $_REQUEST['listmode']==''){
    if(isset($_REQUEST['category'])){
        $_SESSION['cat']=$_REQUEST['category'];
    }
    
}
if (!isset($_REQUEST['page'])) {
    $_REQUEST['page'] = 1;
}
if (!isset($_REQUEST['where'])) {
    $_REQUEST['where '] = '';
}
//TODO - ???????????????????????????? what is this?
if (!empty($_REQUEST['categ'])){ //&& $_REQUEST['categ']=='todas') {
    if($_REQUEST['categ']=='todas'){
        $_REQUEST['category'] = 'GLOBAL';
    }else{
        $_REQUEST['category'] = $_REQUEST['categ'];
    }
}

if (!isset($_REQUEST['category']) 
        || ($_REQUEST['category'] == 'GLOBAL' && empty($_REQUEST['action'])))
{
    $_REQUEST['category'] = 'GLOBAL';
    if(!isset ($_REQUEST['action'])){
        $_REQUEST['action'] = "list_categorys";
    }
}

if (!Acl::check('IMAGE_ADMIN')) {
    Acl::deny();
}

//$mm = new MediaManager();
$mmc = new MediaManagerController();
$mmc->action_init();

$action = (!isset($_REQUEST['action']))? 'list_today': $_REQUEST['action'];


switch($action) {
    case 'list_categorys': {

        unset($_SESSION['where']);
        $mmc->action_list_categorys();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/index.tpl');

    } break;

    case 'list_today': {

        unset($_SESSION['where']);

        $mmc->action_list_today();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/list_today.tpl');

    } break;

    case 'list_all': {

        if (isset($_REQUEST['where']) && $_SESSION['where']) {
            unset($_SESSION['where']);
        }

        $mmc->action_list_all();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/list_all_in_category.tpl');

    } break;

    case 'results': {//despues del add foto muestra results.

        $mmc->action_view_results();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/results.tpl');

    } break;

    case 'searchResult': {
        $mmc->action_searchResult();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/list_all_in_category.tpl');

    } break;

    case 'read':
    case 'image_data': {

        $html_out = $mmc->action_read_image_data();
        $tpl = $mmc->tpl;
        $tpl->display('mediamanager/image_data.tpl');
        //Application::ajax_out($html_out);

    } break;

    case 'search': {

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/search.tpl');

    } break;

    case 'upload': {

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/upload.tpl');

    } break;


    case 'save_data': {
        $mmc->action_save_data();
        if($_SESSION['desde']!='searchResult') {
          $_SESSION['where']='';
        }
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=' . $_SESSION['desde'] . '&category=' .
                             $mmc->category . '&page=' . $page);
    } break;

    
    case 'validate': {
        $mmc->action_updateDatasPhotos();
        if(isset($_REQUEST['where']) && $_SESSION['where']) {
            unset($_SESSION['where']);
        }
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;        
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=image_data&category=' .
                             $_REQUEST['category']. '&id=' . $_REQUEST['id']);
    } break;
    
    case 'addPhoto': {
        if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {
            //Mirar el tema de mensajes en los fallos que deberia devolver.
            $uploads = $mmc->action_addPhoto();
            if($uploads){
                Application::forward($_SERVER['SCRIPT_NAME'] . '?action=results&category=' . $mmc->category .
                                     '&uploads=' . $uploads . '&mensaje=' . $mmc->alert);
            }
        }

        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list_today&category=' . $mmc->category .
                             '&page=' . $page . '&mensaje=' . $mmc->alert);
    } break;

    case 'updateDatasPhotos': {
        $mmc->action_updateDatasPhotos();
        if(isset($_REQUEST['where']) && $_SESSION['where']) {
            unset($_SESSION['where']);
        }
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list_today&category=' .
                             $mmc->category . '&page=' . $page);
    } break;

    case 'delFile': {
        $name=$mmc->action_delPhoto();

        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        if($_SESSION['desde']!='searchResult') {
          unset($_SESSION['where']);
        }
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=' . $_SESSION['desde'] . '&name=' . $name .
                             '&alerta=' . str_replace('"','\'', $mmc->alert). '&category=' . $mmc->category . '&page=' . $page);
    } break;

    case 'mdelete': {
        $msg="Las photos ";
        if($_REQUEST['id']==6 && isset($_SESSION['cat'])){ //Eliminar todos
            $cm = new ContentManager();
            $photos = $cm->find_by_category('Photo', $_SESSION['cat'] , 'fk_content_type=8 AND   photos.media_type="image"', 'ORDER BY created DESC');

            if(count($photos)>0){
                foreach ($photos as $art){
                    $photo = new Photo($art->id);
                    $photo->delete($art->id,$_SESSION['userid'] );
                }
            }


            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['cat'].'&page='.$_REQUEST['page']);
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

        Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$photo->category.'&alert='.$alert.'&msg='.$msg.'&page='.$_REQUEST['page']);

    } break;

    case 'config':

        $configurationsKeys = array(
            'image_thumb_size',
            'image_inner_thumb_size',
            'image_front_thumb_size',
        );

        $configurations = s::get($configurationsKeys);

        $tpl->assign(array(
            'configs'   => $configurations,
        ));

        $tpl->display('mediamanager/config.tpl');

        break;

    case 'save_config':

        unset($_POST['action']);
        unset($_POST['submit']);
        

        foreach ($_POST as $key => $value ) {
            s::set($key, $value);
        }

        m::add(_('Settings saved.'), m::SUCCESS);

        Application::forward($_SERVER['SCRIPT_NAME']);
        break;

    default: {

        unset($_SESSION['where']);

        $mmc->action_list_today();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/mediamanager.tpl');

    } break;

}
