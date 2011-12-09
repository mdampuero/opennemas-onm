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
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

//Check if module is activated in this onm instance
\Onm\Module\ModuleManager::checkActivatedOrForward('KEYWORD_MANAGER');

 // Check if the user can admin album
Acl::checkOrForward('PCLAVE_ADMIN');
 
/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

 

// Build redirect with filter params
$_redirect = '';
if(isset($_REQUEST['filter'])) {
    $_redirect = '&filter[pclave]=' . $_REQUEST['filter']['pclave'];
}

$pclave = new PClave();

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;
switch($action) {

    case 'list': 

        $filter = null;
        if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter']['pclave'])) {
            $filter = '`pclave` LIKE "%' . $_REQUEST['filter']['pclave'] . '%"';
        }

        $terms = $pclave->getList($filter);

        $items_page = ITEMS_PAGE;
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

        $terms = array_slice($terms, ($page-1)*$items_page, $items_page);

        $tpl->assign('pclaves', $terms);
        $tpl->assign('pager', $pager);
        $tpl->display('keywords/list.tpl');
    break;

    case 'search': 
        $id    = intval($_REQUEST['id']);
        $terms = $pclave->getList();

        $matches = array();

        foreach($terms as $term) {
            if($term->id != $id) {
                if(preg_match('/^' . preg_quote($_REQUEST['q']) . '/', $term->pclave)) {
                    $matches[] = $term;
                }
            }
        }

        $tpl->assign('terms', $matches);
        $tpl->display('keywords/search.tpl');
    break;

    case 'read': 
        Acl::checkOrForward('PCLAVE_UPDATE');
        
        $id = $_REQUEST['id'];
        $pclave->read($id);

        $tpl->assign('id', $id);
        $tpl->assign('pclave', $pclave);
        $tpl->assign('tipos', array('url' => _('URL'), 'intsearch' => _('Internal search'), 'email' => _('Email')));
        $tpl->display('keywords/new.tpl');

    break;

    case 'new': 
        Acl::checkOrForward('PCLAVE_CREATE');
        // Show form
        $tpl->assign('tipos', array('url' => _('URL'), 'intsearch' => _('Internal search'), 'email' => _('Email')));
        $tpl->display('keywords/new.tpl');
    break;

    case 'delete': 
        Acl::checkOrForward('PCLAVE_DELETE');
        $id = intval($_REQUEST['id']);
        $pclave->delete($id);

        Application::forward('?action=list' . $_redirect);
    break;

    case 'save': 
        Acl::checkOrForward('PCLAVE_CREATE');
       
        $pclave->save($_POST);

        Application::forward('?action=list' . $_redirect);
    break;

    case 'autolink': 
        $content = json_decode($HTTP_RAW_POST_DATA)->content;
        if(!empty($content)) {
            // Terms was cached, no problem
            $terms = $pclave->getList();

            // Return to editor
            echo $pclave->replaceTerms($content, $terms);
        }
    break;

    default: 
        Application::forward($_SERVER['PHP_SELF'] . '?action=list');
    break;
}
