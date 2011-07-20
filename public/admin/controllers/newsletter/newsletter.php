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
use Onm\Settings as s;

require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

error_reporting(E_ALL);

// Check ACL
require_once(SITE_CORE_PATH.'privileges_check.class.php');
if(!Acl::check('NEWSLETTER_ADMIN')) {
    Acl::deny();
}

require_once(SITE_CORE_PATH.'string_utils.class.php');
String_Utils::disabled_magic_quotes();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('application_name', 'Boletín de Noticias');

$newsletter = new Newsletter(array('namespace' => 'PConecta_'));
$ccm = ContentCategoryManager::get_instance();

function buildFilter($filters)
{
    if(!isset($filters) || is_null($filters)) {
        return array(array('in_home=1'), 'home_placeholder ASC, home_pos ASC, created DESC');
    }

    $fltr = array('`available`=1');
    $order_by = 'created DESC';


    switch($filters['options']) {
        case 'in_home': {
            $fltr[] = '`in_home`=1';
            $fltr[] = '`frontpage`=1';
            $fltr[] = '`content_status`=1';
            $fltr[] = '`home_placeholder` IS NOT NULL';
            $order_by = 'home_placeholder ASC, home_pos ASC, created DESC';
        } break;

        case 'frontpage': {
            $fltr[] = '`frontpage`=1';
            $fltr[] = '`content_status`=1';
            $order_by = 'placeholder ASC, position ASC, created DESC';
        } break;

        case 'hemeroteca': {
            $fltr[] = '`content_status`=0';
        } break;
    }

    if(isset($filters['q']) && !empty($filters['q'])) {
        $fltr[] = 'MATCH (title,metadata) AGAINST ("'.addslashes($filters['q']).'" IN BOOLEAN MODE)';
    }

    if(isset($filters['category']) && !empty($filters['category']) && ($filters['category']>0)) {
        $fltr['category'] = $filters['category'];
    } elseif(isset($filters['category'])) {
        //$order_by = '`content_categories`.`name` ASC, ' . $order_by; // ???
    }

    if(isset($filters['author']) && !empty($filters['author']) && ($filters['author']>0)) {
        $fltr[] = '`opinions`.`fk_author`='.$filters['author'];
    }

    return array($fltr, $order_by);
}

// Initialize request parameters
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'listArticles')) );
}

switch($action) {


    /**
     * Step 1: list all articles available in frontpage
     */
    case 'listArticles':

        $items = $newsletter->getItemsProvider();

        // Get articles in frontpage
        $articles = $items->getItems('Article',
                                     array('in_home=1',
                                           'content_status=1',
                                           'contents.available=1',
                                           'contents.frontpage=1'),
                                     'home_placeholder ASC, home_pos ASC, created DESC',
                                     '0, ' . Newsletter::ITEMS_MAX_LIMIT);

        $message = filter_input ( INPUT_GET, 'message' , FILTER_SANITIZE_STRING, array('options' => array('default' => null)) );

        $tpl->assign(array(
            'content_categories'    => $ccm->getCategoriesTreeMenu(),
            'items'                 => $articles,
            'message'               => $message,
        ));

        $tpl->display('newsletter/listArticles.tpl');

        break;

    /**
     * Step 2: list all opinions available
     */
    case 'listOpinions':

        $items = $newsletter->getItemsProvider();

        // Opinions
        $opinions = $items->getItems('Opinion', array('in_home=1', 'content_status=1'),
                                     'created DESC',
                                     '0, ' . Newsletter::ITEMS_MAX_LIMIT);
        $tpl->assign('items', $opinions);

        // Authors
        $author = new Author();
        $authors = $author->all_authors(NULL,'ORDER BY name');
        $tpl->assign('authors', $authors);

        // Postmaster
        if(isset($_REQUEST['postmaster']) && !empty($_REQUEST['postmaster'])) {
            $tpl->assign('postmaster', json_decode($_REQUEST['postmaster']));
        }

        $tpl->display('newsletter/listOpinions.tpl');

        break;

    /**
     * Step 3: list Accounts available for send this message
     * TODO: must be improved to allow custom destinations or edit existing.
     */
    case 'listAccounts':
        $account = $newsletter->getAccountsProvider();
        $accounts = $account->getAccounts();

        // Ajax request
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            header('Content-type: application/json');
            echo json_encode($accounts);
            exit(0);
        }

        $tpl->assign('items', $accounts);

        $tpl->display('newsletter/listAccounts.tpl');

        break;

    /**
     * Step 4: preview the message
     */
    case 'preview':

        $htmlContent = $newsletter->render();
        $tpl->assign('htmlContent', $htmlContent);
        $tpl->display('newsletter/preview.tpl');

        break;

    /**
     * Step 5: send the message to destinations and see send report.
     */
    case 'send':

        // save newsletter
        $postmaster = $_REQUEST['postmaster'];
        $newsletter->create(array('data' => $postmaster));

        // Ignore user abort and life time to infinite
        $newsletter->setConfigMailing();
        $htmlContent = $newsletter->render();

        $params = array(
            'subject'   => $_REQUEST['subject'],
            'mail_host' => MAIL_HOST,
            'mail_user' => MAIL_USER,
            'mail_pass' => MAIL_PASS,
            'mail_from' => MAIL_FROM,
            'mail_from_name' => SITE_FULLNAME,
        );

        $data = json_decode($postmaster);

        $htmlFinal = "";

        // Mail user by user
        foreach($data->accounts as $mailbox) {

            // Replace name destination
            $emailHtmlContent = str_replace('###DESTINATARIO###', $mailbox->name, $htmlContent);

            //if($newsletter->sendToUser($mailbox, $emailHtmlContent, $params)) {
                $htmlFinal .= '<tr><td width=50% align=right><strong class="ok">OK</strong>&nbsp;&nbsp;</td><td>'. $mailbox->name . ' &lt;' . $mailbox->email . '&gt;</td></tr>';
            //} else {
            //    $htmlFinal .= '<tr><td width=50% ><strong class="failed">FAILED</strong>&nbsp;&nbsp;</td><td>'. $mailbox->name . ' &lt;' . $mailbox->email. '&gt;</td></tr>';
            //}
        }

        $tpl->assign(array(
            'html_final' => $htmlFinal,
            'postmaster' => $postmaster,
        ));
        $tpl->display('newsletter/send.tpl');

    break;


    case 'search':

        $items = $newsletter->getItemsProvider();

        $source = (in_array($_REQUEST['source'], array('Article', 'Opinion')))? $_REQUEST['source']: 'Article';
        list($filter, $order_by) = buildFilter($_REQUEST['filters']);

        $articles = $items->getItems($source, $filter, $order_by, '0, 50');

        // Ajax request
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            header('Content-type: application/json');
            echo json_encode($articles);
            exit(0);
        }

        $tpl->assign('content_categories', $ccm->getCategoriesTreeMenu());
        $tpl->assign('items', $articles);

        $tpl->display('newsletter/listArticles.tpl');

    break;



    case 'config':

        $configurationsKeys = array(
                                    'newsletter_maillist',
                                    );

        $configurations = s::get($configurationsKeys);

        $message = filter_input ( INPUT_GET, 'message' , FILTER_SANITIZE_STRING, array('options' => array('default' => null)) );

        $tpl->assign(
                     array(
                            'message'  => $message,
                            'configs'   => $configurations,
                        )
                    );

        $tpl->display('newsletter/config.tpl');

    break;

    case 'save_config':

        unset($_POST['action']);
        unset($_POST['submit']);

        foreach ($_POST as $key => $value ) {
            s::set($key, $value);
        }

        $httpParams = array(
                            array('action'=>'listArticles'),
                            array('message'=> _('Settings saved.')),
                            );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

    break;

}
