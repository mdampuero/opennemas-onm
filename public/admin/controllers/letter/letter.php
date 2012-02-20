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
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

// Check MODULE
\Onm\Module\ModuleManager::checkActivatedOrForward('LETTER_MANAGER');
// Check ACL
Acl::checkOrForward('LETTER_ADMIN');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$page = filter_input ( INPUT_GET, 'page' , FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)) );

$action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING );
if( empty($action) ) {
    $action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

$letterStatus = filter_input( INPUT_GET, 'letterStatus' , FILTER_SANITIZE_STRING );
if( empty($letterStatus) ) {
    $letterStatus = filter_input ( INPUT_POST, 'letterStatus' , FILTER_SANITIZE_NUMBER_INT,
                                    array('options' => array('default' => 0)) );
}
$tpl->assign('letterStatus', $letterStatus);

switch($action) {

    case 'list':

        $cm = new ContentManager();

        $filter = "content_status = ".$letterStatus;
        $items_page = s::get('items_per_page') ?: 20;
 
        // ContentManager::find_pages(<TIPO>, <WHERE>, <ORDER>, <PAGE>, <ITEMS_PER_PAGE>, <CATEGORY>);
        list($letters, $pager)= $cm->find_pages('Letter', $filter.' ',
                                                 'ORDER BY  created DESC ', $page, $items_page);

        $params = array(
            'page'=>$page, 'items'=>ITEMS_PAGE,
            'total' => count($letters),
            'url'=>$_SERVER['SCRIPT_NAME'].'?action=list&letterStatus=' . $letterStatus
        );

        $pagination = \Onm\Pager\SimplePager::getPagerUrl($params);

        $tpl->assign(array(
            'pagination' => $pagination,
            'letters' => $letters
        ));

        $tpl->display('letter/list.tpl');

    break;

    case 'new':
        Acl::checkOrForward('LETTER_CREATE');
        $tpl->display('letter/read.tpl');

    break;

    case 'read':

        Acl::checkOrForward('LETTER_UPDATE');
        $contentID = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT);

        $letter = new Letter( $contentID );
        $tpl->assign('letter', $letter);

        $tpl->display('letter/read.tpl');

    break;

    case 'create':
        Acl::checkOrForward('LETTER_CREATE');
        $letter = new Letter();

        if($letter->create( $_POST )) {

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus='
                    . $letterStatus . '&page=' . $page);
        } else {
            $tpl->assign('errors', $letter->errors);
        }

    break;

    case 'update':

        Acl::checkOrForward('LETTER_UPDATE');
        $letter = new Letter($_REQUEST['id']);

        if(!is_null($letter->pk_letter)) {
            $letter->update( $_REQUEST );
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus='
                . $letterStatus .   '&page=' . $page);
    break;

    case 'validate':
        if(!empty($_REQUEST['id'])) {
            Acl::checkOrForward('LETTER_UPDATE');
            $letter = new Letter($_REQUEST['id']);
            if(!is_null($letter->pk_letter)) {
                $letter->update( $_REQUEST );
            }
        } else {
            Acl::checkOrForward('LETTER_CREATE');
            $letter = new Letter();
            $letter->create( $_POST );
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus='
                . $letterStatus .   '&page=' . $page);
    break;

    case 'delete':

        Acl::checkOrForward('LETTER_DELETE');
        $id = filter_input ( INPUT_POST, 'id' , FILTER_SANITIZE_NUMBER_INT );
        $letter = new Letter();
        $letter->delete($id, $_SESSION['userid']);

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus='
                . $letterStatus . '&page=' . $page );
    break;

    case 'change_status':
        Acl::checkOrForward('LETTER_AVAILABLE');

        $id = filter_input ( INPUT_GET, 'id' , FILTER_SANITIZE_NUMBER_INT );
        $status = filter_input ( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT );
        if((!empty($id)) && (!empty($status))) {
            $letter = new Letter($id);
            $letter->set_available($status, $_SESSION['userid']);
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus='
                . $letterStatus . '&page=' . $page);
    break;

    case 'batchFrontpage':

        Acl::checkOrForward('LETTER_AVAILABLE');

        $status = filter_input ( INPUT_GET, 'status' , FILTER_SANITIZE_NUMBER_INT );
        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];
            if(is_array($fields)) {
                foreach($fields as $i ) {

                    $letter = new Letter($i);

                    if(!is_null($letter->pk_letter)) {
                         $letter->set_available($status, $_SESSION['userid']);
                    }
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus='
                . $status .'&page=' . $page);
    break;

    case 'batchDelete':
        Acl::checkOrForward('LETTER_DELETE');

        if(isset($_GET['selected_fld']) && count($_GET['selected_fld']) > 0) {
            $fields = $_GET['selected_fld'];

            if(is_array($fields)) {
                foreach($fields as $i ) {
                    $letter = new Letter($i);
                    $letter->delete( $i, $_SESSION['userid'] );
                }
            }
        }

        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&letterStatus=' .
                    $letterStatus . '&page=' . $page);
    break;

    default:
        Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list&page=' . $page);
    break;
}

