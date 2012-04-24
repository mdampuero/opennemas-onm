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

// Check MODULE
\Onm\Module\ModuleManager::checkActivatedOrForward('COMMENT_DISQUS_MANAGER');
// Check ACL
Acl::checkOrForward('COMMENT_ADMIN');

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
if (
    is_null(s::get('efe_server_auth'))
    && $action != 'config'
) {
    m::add(_('Please provide your EFE auth credentials to start to use your EFE Importer module'));
    $httpParams [] = array(
                        'action'=>'config',
                    );
    Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($httpParams));
}

switch($action) {

    case 'config':

        if (count($_POST) <= 0) {
            if ($disqusConfig = s::get('disqus_shortname')) {

                $message    = filter_input( INPUT_GET, 'message' , FILTER_SANITIZE_STRING );

                $tpl->assign(array(
                    'shortname'    => $disqusConfig,
                ));

            }

            $tpl->display('disqus/config.tpl');

        } else {

            $shortname     = filter_input( INPUT_POST, 'shortname' , FILTER_SANITIZE_STRING );

            if (!isset($shortname)) {
                Application::forward(SITE_URL_ADMIN.'/controllers/disqus/disqus.php' . '?action=config');
            }

            if (s::set('disqus_shortname', $shortname)) {
                m::add(_('Disqus configuration saved successfully'), m::SUCCESS);
            } else {
                m::add(_('There was an error while saving the Disqus module configuration'), m::ERROR);
            }

            Application::forward(SITE_URL_ADMIN.'/controllers/disqus/disqus.php' . '?action=config');
        }

        break;

    case 'list':
        $disqusConfig = s::get('disqus_shortname');

        $tpl->assign('disqus_shortname', $disqusConfig);
        $tpl->display('disqus/list.tpl');
    break;


    default: {
        $httpParams = array(
            array('action','list'),
            array('page',$page),
        );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.StringUtils::toHttpParams($params));
    } break;
}
