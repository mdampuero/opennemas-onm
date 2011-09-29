<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m,
    Onm\Module\ModuleManager;
/**
 * Setup app
*/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

// Check ACL
require_once(SITE_CORE_PATH.'privileges_check.class.php');
Acl::checkorForward('ONM_SETTINGS');

ModuleManager::checkActivatedOrForward('VIDEO_MANAGER');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch($action) {

    case 'list':

        $configurationsKeys = array(
                                    'site_title', 'site_description','site_keywords','site_agency',
                                    'time_zone','site_language','mail_server',
                                    'mail_username','mail_password','google_maps_api_key',
                                    'google_custom_search_api_key','facebook',
                                    'google_analytics','piwik',
                                    'recaptcha',
                                    'items_per_page',
                                    'refresh_interval',
                                    'advertisements_enabled',
                                    'log_enabled', 'log_db_enabled', 'log_level',
                                    'activated_modules'
                                    );

        $configurations = s::get($configurationsKeys);

        $tpl->assign(
                     array(
                            'available_modules'          => ModuleManager::getAvailableModules(),
                            'configs'   => $configurations,
                            'timezones' => \DateTimeZone::listIdentifiers(),
                            'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
                            'logLevels' => array('normal' => _('Normal'), 'verbose' => _('Verbose'), 'all' => _('All (Paranoic mode)') ),
                        )
                    );

        $tpl->display('system_settings/system_settings.tpl');
        break;

    case 'save':

        unset($_POST['action']);
        unset($_POST['submit']);

        $_POST['advertisements_enabled'] = array_key_exists('advertisements_enabled',$_POST);
        $_POST['log_enabled'] = array_key_exists('log_enabled',$_POST);
        $_POST['log_db_enabled'] = array_key_exists('log_db_enabled',$_POST);


        foreach ($_POST as $key => $value ) {
            s::set($key, $value);
        }

        m::add(_('Settings saved.'), m::SUCCESS);
        $httpParams = array(
                            array('action'=>'list'),
                            );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));
        break;


    default: {
        $httpParams = array(
                            array('action','list'),
                            );
        Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($params));
    } break;
}
