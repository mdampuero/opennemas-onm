<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Plan Conecta: Gesti&oacute;n de Usuarios');

require_once('libs/phpmailer/class.phpmailer.php');

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/pc_user.class.php');
require_once('core/pc_user_group.class.php');

function compileFilter($filters)
{    
    $order_by = 'nick, firstname, lastname, name';
    
    $fltr = array();
    if(isset($filters['text']) && !empty($filters['text'])) {
        $fltr[] = 'MATCH (nick,name,firstname,lastname,email) AGAINST ("'.addslashes($filters['text']).'" IN BOOLEAN MODE)';
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

// Filters
list($where, $order_by) = compileFilter($_REQUEST['filters']);

if( isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list': {
			$user = new PC_User();
            
            $itemsPerPage = 40;
            $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 1;            
            
			$users = $user->get_users($where, ($itemsPerPage*($page-1)) . ',' . $itemsPerPage, $order_by);
            $tpl->assign('users', $users);
            
            $pager = $user->getPager($itemsPerPage, $total=$user->countUsers($where));
            $tpl->assign('pager', $pager);
        } break;
    
        case 'existsNick': {
            $exists = (PC_User::exists_nick( $_REQUEST['nickDA'] ))? '1': '0';
            header('Content-type: application/json');
            echo '{exists: '.$exists.'}';            
            
            exit(0);
        } break;
    
        case 'existsEmail': {
            $exists = (PC_User::exists_email( $_REQUEST['emailDA'] ))? '1': '0';
            header('Content-type: application/json');
            echo '{exists: '.$exists.'}';            
            
            exit(0);
        } break;     

		// Crear un nuevo artículo
		case 'new': {
			$user = new PC_User( $_REQUEST['id'] );
			$user_group = new PCUser_group();
			$tpl->assign('user', $user);
			$tpl->assign('user_groups', $user_group->get_user_groups());
        } break;

		case 'read': {
			$user = new PC_User( $_REQUEST['id'] );
			$user_group = new PCUser_group();
			$tpl->assign('user', $user);
			$tpl->assign('user_groups', $user_group->get_user_groups());
        } break;

		case 'update': {
			// TODO: validar datos -> FIXED mirar PC_User::update y PC_User::validate
			$user = new PC_User();
			$isOk = $user->update( $_REQUEST, $isBackend=TRUE );
            if( $isOk ) {
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
            } else {
                $_REQUEST['action'] = 'read';
                
                $user = new PC_User( $_REQUEST['id'] );
                $tpl->assign('user', $user);
                
                $tpl->assign('message', 'Revise los datos proporcionados');
            }
        } break;

		case 'create': {
			$user = new PC_User();
			if($user->create( $_POST )) {                
                // Establecer tamén o status
                if(isset( $_POST['status'] )) {
                    $user->set_status($user->id, $_POST['status'] );
                }
                
				Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
			} else {
				$tpl->assign('errors', $user->errors);
			}
        } break;

		case 'validate': {
			$user = null;
			if(empty($_POST["id"])) {
				$user = new PC_User();
				if(!$user->create( $_POST ))
					$tpl->assign('errors', $user->errors);		
			} else {
				$user = new PC_User();
				$user->update( $_REQUEST );
			}
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$user->id);
		} break;
		
		case 'delete': {
			$user = new PC_User();
			$user->delete( $_POST['id'] );
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        } break;
        
		case 'mdelete': {
			$user = new PC_User();
            
            if(isset($_REQUEST['cid']) && count($_REQUEST['cid'])>0) {
                foreach($_REQUEST['cid'] as $id) {
                    $user->delete( $id );
                }
            }
			
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        } break;        
		
		case 'change_status': {
			$user = new PC_User($_REQUEST['id']);
            
			$status = ($user->status == 2)? 3: 2;  
			$user->set_status($_REQUEST['id'], $status);
            
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                list($img, $text)  = ($status == 2)? array('g', 'Deshabilitar'): array('r', 'Habilitar');
                
                echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
                exit(0);
            }
            
			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        } break;
    
        case 'subscribe': {
            $user = new PC_User($_REQUEST['id']);
            
            $subscription = ($user->subscription + 1) % 2;
            $user->mUpdateProperty($user->id, 'subscription', $subscription);
            
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                list($img, $text)  = (!$subscription)? array('0', 'Suscribir') :
                                                       array('1', 'Anular suscripción');                
                
                echo '<img src="' . $tpl->image_dir . 'subscription_' . $img . '-16x16.png" border="0" title="' . $text . '" />';
                exit(0); // Ajax request
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        } break;
    
        case 'munsubscribe': {
            $user = new PC_User();
            
            $data = array();
            if(isset($_REQUEST['cid']) && count($_REQUEST['cid'])>0) {
                foreach($_REQUEST['cid'] as $id) {
                    $data[] = array('id' => $id, 'value' => 0);
                }
                $user->mUpdateProperty($data, 'subscription');
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        } break;
        
        case 'msubscribe': {
            $user = new PC_User();
            
            $data = array();
            if(isset($_REQUEST['cid']) && count($_REQUEST['cid'])>0) {
                foreach($_REQUEST['cid'] as $id) {
                    $data[] = array('id' => $id, 'value' => 1);
                }
                $user->mUpdateProperty($data, 'subscription');
            }
            
            Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
        } break;
		
		default: {
			Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
		} break;
	}
} else {
	Application::forward($_SERVER['SCRIPT_NAME'] . '?action=list');
}

$tpl->display('pc_user.tpl');
?>