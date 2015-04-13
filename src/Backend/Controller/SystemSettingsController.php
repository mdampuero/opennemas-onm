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
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles all the request for Welcome actions
 *
 * @package Backend_Controllers
 **/
class SystemSettingsController extends Controller
{
    /**
     * Gets all the settings and displays the form
     *
     * @return void
     *
     * @Security("has_role('ONM_SETTINGS')")
     *
     * @CheckModuleAccess(module="SETTINGS_MANAGER")
     **/
    public function defaultAction()
    {
        $configurations = array();

        $configurationsKeys = [
            'site_title', 'site_logo', 'site_description','site_keywords',
            'site_agency', 'site_footer', 'mobile_logo', 'favico',
            'youtube_page', 'contact_email', 'site_color', 'site_name',
            'time_zone','site_language','site_footer', 'recaptcha',
            'google_maps_api_key','google_custom_search_api_key', 'vimeo_page',
            'facebook','facebook_page','facebook_id','twitter_page',
            'googleplus_page', 'google_analytics','piwik', 'ojd', 'comscore',
            'section_settings', 'paypal_mail', 'items_per_page',
            'refresh_interval','items_in_blog', 'google_news_name',
            'google_page', 'webmastertools_google', 'webmastertools_bing',
            'max_session_lifetime', 'onm_digest_user', 'onm_digest_pass',
            'cookies_hint_enabled', 'cookies_hint_url', 'linkedin_page'
        ];

        foreach ($configurationsKeys as $value) {
            $configurations[$value] = s::get($value);
        }

        return $this->render(
            'system_settings/system_settings.tpl',
            array(
                'configs'   => $configurations,
                'timezones' => \DateTimeZone::listIdentifiers(),
                'languages' => array('en_US' => _("English"), 'es_ES' => _("Spanish"), 'gl_ES' => _("Galician")),
            )
        );
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @param Request $request the resquest object
     *
     * @return Response the response object
     *
     * @Security("has_role('ONM_SETTINGS')")
     *
     * @CheckModuleAccess(module="SETTINGS_MANAGER")
     **/
    public function saveAction(Request $request)
    {
        // Get files from symfony request
        $siteLogo   = $request->files->get('site_logo');
        $favico     = $request->files->get('favico');
        $mobileLogo = $request->files->get('mobile_logo');

        // Get settings from section (array)
        $sectionSettings = $request->request->filter('section_settings');

        // Generate upload path
        $uploadDirectory = MEDIA_PATH.'/sections/';

        // Check if upload directory is already created
        if ($sectionSettings['allowLogo'] == 1 && !is_dir($uploadDirectory)) {
            \Onm\FilesManager::createDirectory($uploadDirectory);
        }

        if (!is_null($siteLogo)) {
            // Get file original name
            $siteLogoName = $siteLogo->getClientOriginalName();

            // Check max height for site logo
            $size = getimagesize($_FILES['site_logo']['tmp_name']);
            if ($size[1] > 120) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('The maximum height for the "Site Logo" is 120px. Please adjust your image size.')
                );

                // Send the user back to the form
                return $this->redirect($this->generateUrl('admin_system_settings'));
            }

            // Move uploaded file
            $siteLogo->move($uploadDirectory, $siteLogoName);
            // Save name on settings
            s::set('site_logo', $siteLogoName);
        }


        if (!is_null($favico)) {
            // Get file original name
            $favicoName = $favico->getClientOriginalName();
            // Move uploaded file
            $favico->move($uploadDirectory, $favicoName);
            // Save name on settings
            s::set('favico', $favicoName);
        }

        if (!is_null($mobileLogo)) {
            // Get file original name
            $mobileLogoName = $mobileLogo->getClientOriginalName();
            // Move uploaded file
            $mobileLogo->move($uploadDirectory, $mobileLogoName);
            // Save name on settings
            s::set('mobile_logo', $mobileLogoName);
        }


        if (!$request->request->getDigits('cookies_hint_enabled', 0)) {
            $request->request->set('cookies_hint_enabled', 0);
        }

        foreach ($request->request as $key => $value) {
            // Strip html tags for SEO settings
            if ($key == 'site_title' ||
                $key == 'site_description' ||
                $key == 'site_keywords'
            ) {
                $value = trim(strip_tags($value));
            }

            // Save settings
            s::set($key, $value);
        }

        // Delete caches for custom_css and frontpages
        $this->dispatchEvent('frontpage.save_position', array('category' => 0));

        $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

        // Send the user back to the form
        return $this->redirect($this->generateUrl('admin_system_settings'));
    }
}
