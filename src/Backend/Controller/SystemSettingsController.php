<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Displays and saves system settings.
 */
class SystemSettingsController extends Controller
{
    /**
     * Gets all the settings and displays the form
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('ONM_SETTINGS')")
     */
    public function defaultAction()
    {
        $keys = [
            'comscore', 'contact_email', 'cookies_hint_enabled',
            'cookies_hint_url', 'facebook', 'facebook_id', 'facebook_page',
            'favico', 'google_analytics', 'google_analytics_others',
            'google_custom_search_api_key', 'google_maps_api_key', 'google_tags_id',
            'google_news_name', 'google_page', 'googleplus_page',
            'instagram_page', 'items_in_blog', 'items_per_page',
            'linkedin_page', 'max_session_lifetime', 'mobile_logo', 'ojd',
            'onm_digest_pass', 'onm_digest_user', 'paypal_mail',
            'pinterest_page', 'piwik', 'recaptcha', 'refresh_interval',
            'section_settings', 'site_agency', 'site_color', 'site_color_secondary',
            'site_description', 'site_footer', 'site_footer', 'site_keywords',
            'site_language', 'site_logo', 'site_name', 'site_title',
            'time_zone', 'twitter_page', 'vimeo_page', 'webmastertools_bing',
            'webmastertools_google', 'youtube_page',
            'robots_txt_rules', 'chartbeat',
            'body_end_script', 'body_start_script','header_script',
            'elements_in_rss', 'redirection', 'rtb_files'
        ];

        $configurations = $this->get('setting_repository')->get($keys);

        if (array_key_exists('google_analytics', $configurations) &&
            is_array($configurations['google_analytics'])
        ) {
            // Keep compatibility with old analytics store format
            if (array_key_exists('api_key', $configurations['google_analytics'])) {
                $oldConfig = $configurations['google_analytics'];
                $configurations['google_analytics'] = [];
                $configurations['google_analytics'][]= $oldConfig;
            }
            // Decode base64 custom code for analytics
            foreach ($configurations['google_analytics'] as &$value) {
                if (array_key_exists('custom_var', $value) && !empty($value['custom_var'])) {
                    $value['custom_var'] = base64_decode($value['custom_var']);
                }
            }
        }

        return $this->render(
            'system_settings/system_settings.tpl',
            array(
                'countries' => $this->get('core.geo')->getCountries(),
                'country'   => $this->get('core.instance')->country,
                'configs'   => $configurations,
                'timezones' => \DateTimeZone::listIdentifiers(),
                'languages' => $this->get('core.locale')->getLocales(),
            )
        );
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('ONM_SETTINGS')")
     */
    public function saveAction(Request $request)
    {
        // Get files from symfony request
        $siteLogo   = $request->files->get('site_logo');
        $favico     = $request->files->get('favico');
        $mobileLogo = $request->files->get('mobile_logo');

        // Get settings from section (array)
        $sectionSettings = $request->request->filter('section_settings', ['allowLogo' => 0]);

        // Generate upload path
        $uploadDirectory = MEDIA_PATH.'/sections/';

        // Check if upload directory is already created
        if (array_key_exists('allowLogo', $sectionSettings) &&
            $sectionSettings['allowLogo'] == 1 &&
            !is_dir($uploadDirectory)
        ) {
            \Onm\FilesManager::createDirectory($uploadDirectory);
        }
        $this->get('setting_repository')->set('section_settings', ['allowLogo' => $sectionSettings['allowLogo']]);

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
            $this->get('setting_repository')->set('site_logo', $siteLogoName);
        }


        if (!is_null($favico)) {
            // Get file original name
            $favicoName = $favico->getClientOriginalName();
            // Move uploaded file
            $favico->move($uploadDirectory, $favicoName);
            // Save name on settings
            $this->get('setting_repository')->set('favico', $favicoName);
        }

        if (!is_null($mobileLogo)) {
            // Get file original name
            $mobileLogoName = $mobileLogo->getClientOriginalName();
            // Move uploaded file
            $mobileLogo->move($uploadDirectory, $mobileLogoName);
            // Save name on settings
            $this->get('setting_repository')->set('mobile_logo', $mobileLogoName);
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

            if (in_array($key, ['header_script', 'body_end_script', 'body_start_script'])) {
                if (!$this->getUser()->isMaster()) {
                    continue;
                }
                $value = base64_encode($value);
            }

            if ($key == 'google_analytics' && is_array($value)) {
                foreach ($value as &$element) {
                    if (array_key_exists('custom_var', $element) &&
                        !empty($element['custom_var'])
                    ) {
                        $element['custom_var'] = base64_encode($element['custom_var']);
                    }
                }
            }

            if ($key == 'rtb_files')  {
                $value = json_decode($value);
            }

            // Save settings
            $this->get('setting_repository')->set($key, $value);
        }

        // Save country for instance
        $instance = $this->get('core.instance');
        $instance->country = $request->request->get('country', '');
        $this->get('orm.manager')->persist($instance);

        if (empty($request->request->get('redirection'))) {
            $this->get('setting_repository')->set('redirection', 0);
        }

        // Delete caches for custom_css and frontpages
        $this->get('core.dispatcher')->dispatch('setting.update');

        // TODO: Remove when using new ORM features
        $keys  = [ 'max_mailing', 'pass_level', 'piwik', 'time_zone' ];
        $cache = $this->get('cache.manager')->getConnection('instance');
        foreach ($keys as $key) {
            $cache->remove($key);
        }

        $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

        // Send the user back to the form
        return $this->redirect($this->generateUrl('admin_system_settings'));
    }
}
