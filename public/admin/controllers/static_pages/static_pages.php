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
/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

// Check ACL
require_once(SITE_CORE_PATH.'privileges_check.class.php');
if(!Acl::check('STATIC_ADMIN')) {
    Acl::deny();
}

/**
 * Setup view
 */
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);


// Build redirect with filter params
$_redirect = '';
if(isset($_REQUEST['filter'])) {
    $_redirect = '&filter[title]=' . $_REQUEST['filter']['title'];
}

$action = filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING);
if (is_null($action)) {
    $action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);
}

switch($action) {

    case 'list':

        $_REQUEST['action'] = 'list';

        $filter = null;
        if (isset($_REQUEST['filter'])
            && !empty($_REQUEST['filter']['title']))
        {
            $filter = '`title` LIKE "%' . $_REQUEST['filter']['title'] . '%"';
        }

        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;

        $cm = new ContentManager();
        list($pages, $pager) = $cm->find_pages('Static_Page', $filter, 'ORDER BY created DESC ', $page, 10);

        $tpl->assign( array('pages' => $pages,'pager' => $pager));
        $tpl->display('static_pages/index.tpl');

    break;

    case 'read':

        $page = new Static_Page();
        $page->read($_REQUEST['id']);

        $tpl->assign('id', $page->id);
        $tpl->assign('page', $page);
        $tpl->display('static_pages/read.tpl');

    break;

    case 'new':

        $tpl->display('static_pages/read.tpl');

    break;

    case 'delete':

        $page = new Static_Page();
        $page->delete($_REQUEST['id']);

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list' . $_redirect);

    break;

    case 'save':

        $page = new Static_Page();

        $data = $_POST;

        $data['slug'] = $page->buildSlug($data['slug'], $data['id'], $data['title']);
        $data['metadata']  = String_Utils::normalize_metadata($data['metadata']);
        $page->save($data);

        Application::forward( $_SERVER['SCRIPT_NAME'].'?action=list' . $_redirect);

    break;

    case 'validate':

        $page = new Static_Page();

        $data = $_POST;

        $data['slug'] = $page->buildSlug($data['slug'], $data['id'], $data['title']);
        $data['metadata']  = String_Utils::normalize_metadata($data['metadata']);
        $page->save($data);

        Application::forward( $_SERVER['SCRIPT_NAME'].'?action=read&id='.$data['id']);

    break;

    case 'build_slug':

        /**
         * If the action is an Ajax request handle it, if not redirect to list
         */
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
           && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
        {
            $page = new Static_Page();
            $slug = $page->buildSlug($_POST['slug'], $_POST['id'], $_POST['title']);

            Application::ajax_out($slug);
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list' . $_redirect);

    break;

    case 'clean_metadata':

        /**
         * If the action is an Ajax request handle it, if not redirect to list
         */
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
        {
            $output  = String_Utils::normalize_metadata($_POST['metadata']);
            Application::ajax_out($output);
        }

        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list' . $_redirect);

    break;

    case 'chg_status':

        /**
         * If the action is an Ajax request handle it, if not redirect to list
         */
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'))
        {
            $page = new Static_Page();
            $page->read($_REQUEST['id']);

            $available = ($page->available+1) % 2;
            $page->set_available($available, $_SESSION['userid']);

            list($img, $text)  = ($available)? array('g', 'PUBLICADO'): array('r', 'PENDIENTE');
            $output = '<img src="' . $tpl->image_dir . 'publish_' . $img . '.png" border="0" title="' . $text . '" />';
            Application::ajax_out($output);
        }

        Application::forward( $_SERVER['SCRIPT_NAME'].'?action=list' . $_redirect );

    break;

    default:
        Application::forward( $_SERVER['SCRIPT_NAME'].'?action=list'. $_redirect);
    break;

}
