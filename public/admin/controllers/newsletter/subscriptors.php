<?php
use Onm\Settings as s,
    Onm\Message  as m;

require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

Acl::checkOrForward('NEWSLETTER_ADMIN');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Newsletter Subscriptor');

function compileFilter($filters)
{
    $order_by = 'name, email';

    $fltr = array();
    if(isset($filters['text']) && !empty($filters['text'])) {
        $fltr[] = 'MATCH (name, email) AGAINST ("'.addslashes($filters['text']).'" IN BOOLEAN MODE)';
    }

    if(isset($filters['subscription']) && ($filters['subscription']>=0)) {
        $fltr[] = '`subscription`=' . $filters['subscription'];
    }

    if(isset($filters['status']) && ($filters['status']>=0)) {
        $fltr[] = '`status`=' . $filters['status'];
    }

    $where = null;
    if(count($fltr) > 0) {
        $where = implode(' AND ', $fltr);
    }

    return array($where, $order_by);
}


$page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT);


if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list':

            // Filters
            $filters = (isset($_REQUEST['filters']))? $_REQUEST['filters']: null;
            list($where, $order_by) = compileFilter($filters);

            $user = new Subscriptor();

            $itemsPerPage = 40;
            $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 1;

            $users = $user->get_users($where, ($itemsPerPage*($page-1)) . ',' . $itemsPerPage, $order_by);
            $tpl->assign('users', $users);

            $pager = $user->getPager($itemsPerPage, $total=$user->countUsers($where));
            $tpl->assign('pager', $pager);

            $tpl->display('newsletter/subscriptors.tpl');
        break;
       
        case 'existsEmail':
            $exists = (Subscriptor::exists_email( $_REQUEST['emailDA'] ))? '1': '0';
            header('Content-type: application/json');
            echo '{exists: '.$exists.'}';

            exit(0);
        break;

        // Crear un nuevo artículo
        case 'new':
             
            $tpl->display('newsletter/newSubscriptor.tpl');

        break;

        case 'read':
            $id = filter_input(INPUT_POST,'id',FILTER_DEFAULT);
            if (empty($id)) { //because forwards
                $id = filter_input(INPUT_GET,'id',FILTER_DEFAULT);
            }
            
            $user = new Subscriptor( $id );
            
            $tpl->assign('user', $user);

            $tpl->display('newsletter/newSubscriptor.tpl');

        break;

        case 'update':
            // TODO: validar datos -> FIXED mirar Subscriptor::update y Subscriptor::validate
            $user = new Subscriptor();
            $isOk = $user->update( $_REQUEST, $isBackend=TRUE );
            if( $isOk ) {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
            } else {
                $_REQUEST['action'] = 'read';

                $user = new Subscriptor( $_REQUEST['id'] );
                $tpl->assign('user', $user);

                $tpl->assign('message', 'Revise los datos proporcionados');
            }
        break;

        case 'create':
            $user = new Subscriptor();
            if($user->create( $_POST )) {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
            } else {
                $tpl->assign('message', '<ul><li>' . implode('</li><li>', $user->_errors) . '</li></ul>');              
                $_REQUEST['action'] = 'new';
            }
        break;

        case 'validate':
            $user = new Subscriptor();

            if(empty($_POST["id"])) {
                // New
                if(!$user->create( $_POST )) {
                    $tpl->assign('message', '<ul><li>' . implode('</li><li>', $user->_errors) . '</li></ul>');
                    $_REQUEST['action'] = 'new';
                } else {
                    $user->read( $user->id );
                    $_REQUEST['action'] = 'read';
                }
            } else {
                // Update
                $isOk = $user->update( $_POST, $isBackend=true );
                if( !$isOk ) {
                    $tpl->assign('message', '<ul><li>' . implode('</li><li>', $user->_errors) . '</li></ul>');
                }
                $_REQUEST['action'] = 'read';
                $user = new Subscriptor( $_REQUEST['id'] );
            }

            $tpl->assign('user', $user);
            $tpl->display('newsletter/newSubscriptor.tpl');
        break;

        case 'delete':
            $user = new Subscriptor();
            $user->delete( $_POST['id'] );
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;

        case 'mdelete':
            $user = new Subscriptor();

            if(isset($_REQUEST['cid']) && count($_REQUEST['cid'])>0) {
                foreach($_REQUEST['cid'] as $id) {
                    $user->delete( $id );
                }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;

        case 'change_status':
            $user = new Subscriptor($_REQUEST['id']);

            $status = ($user->status == 2)? 3: 2;
            $user->set_status($_REQUEST['id'], $status);

            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                list($img, $text)  = ($status == 2)? array('g', 'Deshabilitar'): array('r', 'Habilitar');

                echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
                exit(0);
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;

        case 'subscribe':
            $user = new Subscriptor($_REQUEST['id']);

            $subscription = ($user->subscription + 1) % 2;
            $user->mUpdateProperty($user->id, 'subscription', $subscription);

            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                list($img, $text)  = (!$subscription)? array('0', 'Suscribir') :
                                                       array('1', 'Anular suscripción');

                echo '<img src="' . $tpl->image_dir . 'subscription_' . $img . '-16x16.png" border="0" title="' . $text . '" />';
                exit(0); // Ajax request
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        break;

        case 'munsubscribe': 
            $user = new Subscriptor();

            $data = array();
            if(isset($_REQUEST['cid']) && count($_REQUEST['cid'])>0) {
                foreach($_REQUEST['cid'] as $id) {
                    $data[] = array('id' => $id, 'value' => 0);
                }
                $user->mUpdateProperty($data, 'subscription');
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        break;

        case 'msubscribe': 
            $user = new Subscriptor();

            $data = array();
            if(isset($_REQUEST['cid']) && count($_REQUEST['cid'])>0) {
                foreach($_REQUEST['cid'] as $id) {
                    $data[] = array('id' => $id, 'value' => 1);
                }
                $user->mUpdateProperty($data, 'subscription');
            }

            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        break;

        default:
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        break;
    }
} else {
    Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
}


