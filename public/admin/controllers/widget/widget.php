<?php

/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(dirname(__FILE__).'/../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gestor de Widgets');

require_once(SITE_LIBS_PATH.'Pager/Pager.php');
require_once(SITE_CORE_PATH.'string_utils.class.php');
require_once(SITE_CORE_PATH.'privileges_check.class.php');


// Widget instance
$widget = new Widget();
$cm = new ContentManager();
$ccm = ContentCategoryManager::get_instance();

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;


switch($action) {
    case 'edit': {
        $id = $_REQUEST['id'];                
        $widget->read($id);
        
        $tpl->assign('id', $id);
        $tpl->assign('widget', $widget);
        $tpl->display('widget/edit.tpl');
        break;
    } // Executa tamén new
    
    case 'new': {
        $tpl->display('widget/edit.tpl');
        break;
    } 
    
    case 'delete': {
        $id = $_REQUEST['id'];
        $widget->delete($id);
        
        
        Application::forward('?action=list');
        break;
    }
    
    case 'save': {
        $data = $_POST;        
        
        if(intval($data['id']) > 0) {
            $widget->update($data);
        } else {            
            $widget->create($data);
        }        
        
        Application::forward('?action=list');
        break;
    } 
    
    case 'changeavailable': {
        $widget->read($_REQUEST['id']);
        
        $available = ($widget->available+1) % 2;
        $widget->set_available($available, $_SESSION['userid']);
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            list($img, $text)  = ($available)? array('g', _('PUBLICADO')): array('r', _('PENDIENTE'));
            
            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }
        
        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
        break;
    }

    case 'unpublish': {
        //$widget->read($_REQUEST['id']);
        $cm->unpublishFromHomePage($_REQUEST['id']);

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
        break;
    }

    case 'archive': {
        //$widget->read($_REQUEST['id']);
        $cm->dropFromHomePageOfCategory($_REQUEST['category'],$_REQUEST['id']);

        Application::forward(SITE_URL_ADMIN.'/article.php?action=list&category='.$_REQUEST['category']);
        break;
    }
    
    case 'list':
    default: {
        //$widgets = $cm->find_by_category('Widget', 3, 'fk_content_type=12 ', 'ORDER BY created DESC');
        $widgets = $cm->find('Widget', 'fk_content_type=12', 'ORDER BY created DESC ');
        
        /*$items_page = 25;
        $page = (!isset($_REQUEST['page']))? 1: intval($_REQUEST['page']);
        
        // Pager
        $pager_options = array(
            'mode'        => 'Sliding',
            'perPage'     => $items_page,
            'delta'       => 4,
            'clearIfVoid' => true,
            'urlVar'      => 'page',
            'totalItems'  => count($terms),
        );        
        $pager = Pager::factory($pager_options);
        
        $terms = array_slice($terms, ($page-1)*$items_page, $items_page);*/
        
        $tpl->assign('widgets', $widgets);
        //$tpl->assign('pager', $pager);
        $tpl->display('widget/index.tpl');
        break;
    } 
}