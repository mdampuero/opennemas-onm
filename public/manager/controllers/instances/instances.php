<?php
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');

use \Onm\Instance\InstanceManager as im;

/**
 * Setup view
*/
$tpl = new \TemplateManager(TEMPLATE_ADMIN);

// Widget instance
$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;

switch($action) {
    
    case 'edit': {
        $id = $_REQUEST['id'];
        $instance = im::read($id);
        
        $tpl->assign('instance', $instance);
        $tpl->display('instances/edit.tpl');
        break;
    }

    case 'new': {
        
        $tpl->display('instances/edit.tpl');
        break;
    }

    case 'delete': {
        $id = $_REQUEST['id'];
        $deletion = im::delete($id);

        Application::forward('?action=list');
        break;
    }

    case 'save': {
        $data = array(
                'id' => filter_input(INPUT_POST, 'id' , FILTER_SANITIZE_STRING),
                'name' => filter_input(INPUT_POST, 'name' , FILTER_SANITIZE_STRING),
                'internal_name' => filter_input(INPUT_POST, 'internal_name' , FILTER_SANITIZE_STRING),
                'domains' => filter_input(INPUT_POST, 'domains' , FILTER_SANITIZE_STRING),
                'activated' => filter_input(INPUT_POST, 'activated' , FILTER_SANITIZE_NUMBER_INT),
                'settings' => serialize($_POST['settings'])
            );
            
        if (intval($data['id']) > 0) {
            
            $instance = im::update($data);
            
        } else {
            $instance = im::create($data);
        }

        Application::forward('?action=list');

        break;
    }

    case 'changeactivated': {
        $instance = im::read($_REQUEST['id']);

        $available = ($instance->activated+1) % 2;
        im::changeActivated($instance->id, $available);

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            list($img, $text)  = ($available)? array('g', 'PUBLICADO'): array('r', 'PENDIENTE');

            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }

        Application::forward($_SERVER['PHP_SELF'].'?action=list');
        break;
    }

    case 'list':
    default: {
        //$widgets = $cm->find_by_category('Widget', 3, 'fk_content_type=12 ', 'ORDER BY created DESC');
        
        $instances = im::getListOfInstances();
        
        $_SESSION['desde'] = 'instances';

        $tpl->assign('instances', $instances);
        $tpl->display('instances/list.tpl');
        break;
    }
}
