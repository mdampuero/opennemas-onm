<?php
/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class SystemSettingsController extends Controller
{

    /**
     * Common actions for all the actions
     *
     * @return void
     **/
    public function init()
    {
        // Check ACL
        $this->checkAclOrForward('ONM_SETTINGS');

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
    }

    /**
     * Gets all the settings and displays the form
     *
     * @param Request $request the resquest object
     *
     * @return Response the response object
     **/
    public function defaultAction(Request $request)
    {
        $configurationsKeys = array(
            'site_title', 'site_logo', 'site_description','site_keywords','site_agency',
            'site_footer', 'mobile_logo', 'favico', 'youtube_page',
            'site_color', 'site_name', 'time_zone','site_language','site_footer',
            'recaptcha', 'google_maps_api_key','google_custom_search_api_key',
            'facebook','facebook_page','facebook_id','twitter_page', 'googleplus_page',
            'google_analytics','piwik', 'ojd', 'comscore', 'section_settings', 'paypal_mail',
            'items_per_page','refresh_interval', 'google_news_name', 'google_page',
            'webmastertools_google', 'webmastertools_bing',
            'max_session_lifetime',
        );

        $configurations = s::get($configurationsKeys);

        return $this->render(
            'system_settings/system_settings.tpl',
            array(
                'configs'   => $configurations,
                'timezones' => \DateTimeZone::listIdentifiers(),
                'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
            )
        );
    }

    // TODO: use symfony request instead of $_POST and $_FILES variables
    /**
     * Performs the action of saving the configuration settings
     *
     * @param Request $request the resquest object
     *
     * @return Response the response object
     **/
    public function saveAction(Request $request)
    {
        unset($_POST['action']);
        unset($_POST['submit']);

        if (!empty($_FILES) && isset($_FILES['site_logo'])) {
            $nameFile = $_FILES['site_logo']['name'];
            $uploaddir= MEDIA_PATH.'/sections/'.$nameFile;

            if (move_uploaded_file($_FILES["site_logo"]["tmp_name"], $uploaddir)) {
                $_POST['site_logo'] = $nameFile;
            }
        }
        if (!empty($_FILES) && isset($_FILES['favico'])) {
            $nameFile = $_FILES['favico']['name'];
            $uploaddir= MEDIA_PATH.'/sections/'.$nameFile;

            if (move_uploaded_file($_FILES["favico"]["tmp_name"], $uploaddir)) {
                $_POST['favico'] = $nameFile;
            }
        }
        if (!empty($_FILES) && isset($_FILES['mobile_logo'])) {
            $nameFile = $_FILES['mobile_logo']['name'];
            $uploaddir= MEDIA_PATH.'/sections/'.$nameFile;

            if (move_uploaded_file($_FILES["mobile_logo"]["tmp_name"], $uploaddir)) {
                $_POST['mobile_logo'] = $nameFile;
            }
        }
        if ($_POST['section_settings']['allowLogo'] == 1) {
            $path = MEDIA_PATH.'/sections';
            \FilesManager::createDirectory($path);
        }

        foreach ($_POST as $key => $value) {
            s::set($key, $value);
        }

        m::add(_('Settings saved.'), m::SUCCESS);

        // Send the user back to the form
        return $this->redirect($this->generateUrl('admin_system_settings'));
    }
}
