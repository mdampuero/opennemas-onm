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

error_reporting(E_ALL);
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('./core/application.class.php');

require_once('./libs/Pager/Pager.php');
require_once('./core/string_utils.class.php');
require_once('./core/method_cache_manager.class.php');

require_once('./core/content_manager.class.php');
require_once('./core/content.class.php');
require_once('./core/static_page.class.php');

// Check ACL
require_once('./core/privileges_check.class.php');
if(!Acl::_('STATIC_ADMIN')) {    
    Acl::deny();
}


Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gestión de Páginas Estáticas');
$tpl->addScript('tiny_mce/tiny_mce_gzip.js');



// Build redirect with filter params
$_redirect = '';
if(isset($_REQUEST['filter'])) {
    $_redirect = '&filter[title]=' . $_REQUEST['filter']['title'];
}

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;
switch($action) {        
    
    case 'read': {
        $page = new Static_Page();
        
        $page->read($_REQUEST['id']);
        
        $tpl->assign('id', $page->id);
        $tpl->assign('page', $page);
    }
    
    case 'new': {
        // Nothing
    } break;
    
    case 'delete': {
        $page = new Static_Page();
        
        $page->delete($_REQUEST['id']);
        
        Application::forward('?action=list' . $_redirect);
    } break;
    
    case 'save': {        
        $page = new Static_Page();
        
        $data = $_POST;
        $data['slug'] = $page->buildSlug($data['slug'], $data['id'], $data['title']);
        $data['metadata']  = String_Utils::normalize_metadata($data['metadata']);        
        
        $page->save($data);
        
        Application::forward('?action=list' . $_redirect);
    } break;
    
    case 'build_slug': {
        // Control ajax request
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            $page = new Static_Page();                
            $slug = $page->buildSlug($_POST['slug'], $_POST['id'], $_POST['title']);
                
            echo $slug;
            exit(0);
        } else {
            Application::forward('?action=list' . $_redirect);
        }
    } break;
    
    case 'clean_metadata': {
        // Control ajax request
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            $metadata  = String_Utils::normalize_metadata($_POST['metadata']);        
                
            echo $metadata;
            exit(0);
        } else {
            Application::forward('?action=list' . $_redirect);
        }
    } break;
    
    case 'chg_status': {
        $page = new Static_Page();
        $page->read($_REQUEST['id']);
        
        $available = ($page->available+1) % 2;
        $page->set_available($available, $_SESSION['userid']);
        
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            list($img, $text)  = ($available)? array('g', 'PUBLICADO'): array('r', 'PENDIENTE');
            
            echo '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            exit(0);
        }
        
        Application::forward('?action=list' . $_redirect);
    } break;
    
    case 'list':
    default: {
        $_REQUEST['action'] = 'list';
        
        $filter = null;
        if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter']['title'])) {
            $filter = '`title` LIKE "%' . $_REQUEST['filter']['title'] . '%"';
        }
        
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        
        $cm = new ContentManager();
        list($pages, $pager) = $cm->find_pages('Static_Page', $filter, 'ORDER BY created DESC ', $page, 40);        
        
        $tpl->assign('pages', $pages);
        $tpl->assign('pager', $pager);
    } break;
}



$tpl->display('static_pages.tpl');