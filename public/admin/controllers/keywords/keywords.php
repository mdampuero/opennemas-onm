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

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

// Check ACL
if(!Acl::check('PCLAVE_ADMIN')) { Acl::deny(); }

// Build redirect with filter params
$_redirect = '';
if(isset($_REQUEST['filter'])) {
    $_redirect = '&filter[pclave]=' . $_REQUEST['filter']['pclave'];
}

$pclave = new PClave();

$action = (isset($_REQUEST['action']))? $_REQUEST['action']: null;
switch($action) {

    case 'list': {

        $filter = null;
        if(isset($_REQUEST['filter']) && !empty($_REQUEST['filter']['pclave'])) {
            $filter = '`pclave` LIKE "%' . $_REQUEST['filter']['pclave'] . '%"';
        }

        $terms = $pclave->getList($filter);

        $items_page = 25;
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
    } break;

    case 'search': {
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
    } break;

    case 'read': {
        $id = $_REQUEST['id'];
        $pclave->read($id);

        $tpl->assign('id', $id);
        $tpl->assign('pclave', $pclave);
        $tpl->assign('tipos', array('url' => _('URL'), 'intsearch' => _('Internal search'), 'email' => _('Email')));
        $tpl->display('keywords/new.tpl');

    } break;

    case 'new': {
        // Show form
        $tpl->assign('tipos', array('url' => _('URL'), 'intsearch' => _('Internal search'), 'email' => _('Email')));
        $tpl->display('keywords/new.tpl');
    } break;

    case 'delete': {
        $id = intval($_REQUEST['id']);
        $pclave->delete($id);

        Application::forward('?action=list' . $_redirect);
    } break;

    case 'save': {
        $pclave->save($_POST);

        Application::forward('?action=list' . $_redirect);
    } break;

    case 'autolink': {
        $content = json_decode($HTTP_RAW_POST_DATA)->content;
        if(!empty($content)) {
            // Terms was cached, no problem
            $terms = $pclave->getList();

            // Return to editor
            echo $pclave->replaceTerms($content, $terms);
        }
    } break;

    default: {
        Application::forward($_SERVER['PHP_SELF'] . '?action=list');
    } break;
}
