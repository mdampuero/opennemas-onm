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
require_once '../bootstrap.php';
require_once './session_bootstrap.php';

// Check ACL
Acl::checkorForward('ONM_SETTINGS');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

// Initialize request parameters
$action = filter_input( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if (!isset($action)) {
    $action = filter_input( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array('options' => array('default' => 'list')) );
}

switch ($action) {

    case 'list':

        $configurationsKeys = array(
            'site_title', 'site_logo', 'site_description','site_keywords','site_agency', 'site_footer',
            'site_color', 'site_name', 'time_zone','site_language','site_footer',
            'recaptcha', 'google_maps_api_key','google_custom_search_api_key',
            'facebook','facebook_page','facebook_id','twitter_page',
            'google_analytics','piwik', 'section_settings',
            'items_per_page','refresh_interval',
            'webmastertools_google', 'webmastertools_bing'
        );

        $configurations = s::get($configurationsKeys);

        $tpl->assign(array(
            'configs'   => $configurations,
            'timezones' => \DateTimeZone::listIdentifiers(),
            'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
        ));

        $tpl->display('system_settings/system_settings.tpl');
        break;

    case 'save':

        unset($_POST['action']);
        unset($_POST['submit']);

        if(!empty($_FILES) && isset($_FILES['site_logo'])) {
            $nameFile = $_FILES['site_logo']['name'];
            $uploaddir= MEDIA_PATH.'/sections/'.$nameFile;

            if (move_uploaded_file($_FILES["site_logo"]["tmp_name"], $uploaddir)) {
               $_POST['site_logo'] = $nameFile;
            }
        }
        if($_POST['section_settings']['allowLogo'] == 1){
            $path = MEDIA_PATH.'/sections';
            FilesManager::createDirectory($path);
        }

        foreach ($_POST as $key => $value ) {
            s::set($key, $value);
        }

        m::add(_('Settings saved.'), m::SUCCESS);
        Application::forward(url('admin_system_settings', array(), true));
        break;
}
