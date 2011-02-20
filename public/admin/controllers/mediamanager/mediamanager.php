<?php
/* -*- Mode: PHP; tab-width: 4 -*- */
/**
 * OpenNeMas project
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   OpenNeMas
 * @package    OpenNeMas
 * @copyright  Copyright (c) 2009 Openhost S.L. (http://openhost.es)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

require_once(SITE_CORE_PATH.'privileges_check.class.php');

require('../../mediamanagerController.php');

//????? Parche pq no pasa el action, lo borra en algun sitio
if (!empty($_REQUEST['acti']) && $_REQUEST['acti']=='searchResult') { $_REQUEST['action'] = "searchResult";
    unset($_SESSION['where']);
}

if (!isset($_REQUEST['page'])) {
    $_REQUEST['page'] = 1;
}
if (!isset($_REQUEST['where'])) {
    $_REQUEST['where '] = '';
}

if (!isset($_REQUEST['category'])
    || ($_REQUEST['category'] == 'GLOBAL'
    && empty($_REQUEST['action'])))
{
    $_REQUEST['category'] = 'GLOBAL';
    $_REQUEST['action'] = "list_categorys";
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

        $name=$mmc->action_searchResult();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/mediamanager.tpl');

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
                             '&alerta=' . $mmc->alert . '&category=' . $mmc->category . '&page=' . $page);
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

    default: {

        unset($_SESSION['where']);

        $mmc->action_list_today();

        $tpl = $mmc->tpl;
        $tpl->assign('action', $action);
        $tpl->assign('category', $mmc->category);
        $tpl->display('mediamanager/mediamanager.tpl');

    } break;

}
