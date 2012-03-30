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
use Onm\Settings as s,
    Onm\Message  as m;
/**
 * Setup app
 */
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

// Check ACL
//Acl::checkOrForward('SYNC_ADMIN');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

/**
 * Check if module is configured, if not redirect to configuration form
*/
//TODO: implement

switch($action) {

    case 'config':

        $tpl->display('web_services/config.tpl');
    break;

    case 'sync':


    break;

    default:

        $url = array(
//            'http://retrincos.local/ws.php/articlerest/id/3949.xml',
//            'http://idealgallego.local/ws.php/articlerest/id/1031.xml',
            'http://idealgallego.local/ws.php/articlerest/dayrange/80.xml',
        );

        $articles = array();
        foreach ($url as $value) {
            $xmlString = file_get_contents($value);
            $articles = simplexml_load_string($xmlString);
        }

        var_dump($articles);die();

        $tpl->assign('elements',$articles);
        $tpl->display('web_services/client.tpl');

    break;
}
