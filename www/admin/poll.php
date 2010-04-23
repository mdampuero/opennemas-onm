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

//error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Poll Manager');

require_once('core/method_cache_manager.class.php');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');
require_once('core/poll.class.php');
 
if (!isset($_GET['page']) || empty($_GET['page'])) {$_GET['page'] = 1;}

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list':  //Buscar publicidad entre los content
            $cm = new ContentManager();

            list($polls, $pager)= $cm->find_pages('Poll', 'content_status = 0 ', 'ORDER BY  created DESC ',$_GET['page'],10);

            $tpl->assign('paginacion', $pager);

            $tpl->assign('polls', $polls);
            $_SESSION['from']='poll';
            break;

        case 'new':
            // Nada
            break;

        case 'read': //habrá que tener en cuenta el tipo
            $poll = new Poll( $_REQUEST['id'] );
            $tpl->assign('poll', $poll);

            $items=$poll->get_items($_REQUEST['id']);
            $tpl->assign('items', $items);
        break;

        case 'create':
            $poll = new Poll();
            $_POST['publisher'] = $_SESSION['userid'];
            $_POST['category']= $_POST['fk_content_category'];
            if($poll->create( $_POST )) {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
            } else {
                $tpl->assign('errors', $poll->errors);
            }
        break;

        case 'update':
            $poll = new Poll();

            $poll->update( $_REQUEST );
            if($_SESSION['from']=='hemeroteca'){
                Application::forward('hemeroteca.php?action=list&category='.$_REQUEST['category'].'&mtype=poll');
            }else{
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
            }
        break;

        case 'validate':
            $poll = null;
            $_POST['category']= $_POST['fk_content_category'];
            if(empty($_POST["id"])) {
                $poll = new Poll();
                if(!$poll->create( $_POST ))
                    $tpl->assign('errors', $poll->errors);
            } else {
                $poll = new Poll();
                $poll->update( $_REQUEST );
            }
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$poll->id);
        break;

        case 'delete':
            $poll = new Poll();
            $poll->delete( $_POST['id'] );

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
        break;

        case 'change_status':
            $poll = new Poll($_REQUEST['id']);
            //Publicar o no,
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $poll->set_status($status);

            if($_GET['from']=='index'){
                Application::forward('index.php?action=list');
            }else{
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
            }
        break;

        case 'set_view_column':
            $cm = new ContentManager();
            $polls = $cm->find('Poll', 'content_status=1 and view_column=1', 'ORDER BY created DESC');
            if((count($polls)<2) OR $_REQUEST['status']!=1){
                $poll = new Poll($_REQUEST['id']);
                //Publicar o no,
                $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
                $poll->set_view_column($status);
            }else{
                $msg="Ya hay dos encuestas en portada.";
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&msg='.$msg.'&category='.$_REQUEST['category']);

        break;

        case 'change_favorite':
            $poll = new Poll($_REQUEST['id']);
            //Publicar o no,
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $poll->set_favorite($status);

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);

        break;

        case 'change_available':
            $poll = new Poll($_REQUEST['id']);
            //Publicar o no,
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $poll->set_available($status);
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

        break;

        case 'mfrontpage':
            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
                $fields = $_REQUEST['selected_fld'];
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $poll = new Poll($i);
                        $poll->set_status($_REQUEST['id']);   //Se reutiliza el id para pasar el estatus
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category']);
        break;

        case 'mdelete':

            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0){
                $fields = $_REQUEST['selected_fld'];
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $poll = new Poll($i);
                        $poll->delete( $i);
                    }
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&category='.$_REQUEST['category'].'&page='.$_REQUEST['page']);
        break;

        default:
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
        break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&page='.$_REQUEST['page']);
}

$tpl->display('poll.tpl');

