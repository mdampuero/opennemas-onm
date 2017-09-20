<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Displays and saves system settings.
 */
class SettingController extends Controller
{
    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'comscore', 'contact_email', 'cookies_hint_enabled',
        'cookies_hint_url', 'facebook', 'facebook_id', 'facebook_page',
        'favico', 'google_analytics', 'google_analytics_others',
        'google_custom_search_api_key', 'google_maps_api_key', 'google_tags_id',
        'google_news_name', 'google_page', 'googleplus_page',
        'instagram_page', 'items_in_blog', 'items_per_page',
        'linkedin_page', 'max_session_lifetime', 'mobile_logo', 'ojd',
        'onm_digest_pass', 'onm_digest_user', 'paypal_mail',
        'pinterest_page', 'piwik', 'recaptcha', 'refresh_interval',
        'logo_enabled', 'section_settings', 'site_agency', 'site_color',
        'site_color_secondary', 'site_description', 'site_footer', 'site_footer',
        'site_keywords', 'site_language', 'site_logo', 'site_name', 'site_title',
        'twitter_page', 'time_zone', 'vimeo_page', 'webmastertools_bing',
        'webmastertools_google', 'youtube_page', 'robots_txt_rules', 'chartbeat',
        'body_end_script', 'body_start_script','header_script',
        'elements_in_rss', 'redirection', 'locale', 'rtb_files'
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [
        'body_end_script', 'body_start_script', 'custom_css', 'header_script',
        'robots_txt_rules'
    ];

    /**
     * Returns a list of available locales by name.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listLocaleAction(Request $request)
    {
        $query   = $request->get('q');
        $locales = $this->get('core.locale')->setContext('frontend')
            ->getSupportedLocales();

        if (!empty($query)) {
            $locales = array_filter($locales, function ($a) use ($query) {
                return strpos(strtolower($a), strtolower($query)) !== false;
            });
        }

        $keys    = array_keys($locales);
        $values  = array_values($locales);
        $locales = [];

        for ($i = 0; $i < count($keys); $i++) {
            $locales[] = [
                'code' => $keys[$i],
                'name' => "$values[$i] ($keys[$i])"
            ];
        }

        return new JsonResponse($locales);
    }

    /**
     * Returns the list of settings.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('ONM_SETTINGS')")
     */
    public function listAction()
    {
        $settings = $this->get('setting_repository')->get($this->keys);
        $locale   = $this->get('core.locale');

        if (array_key_exists('google_analytics', $settings)) {
            $settings['google_analytics'] = $this->get('data.manager.adapter')
                ->adapt('google_analytics', $settings['google_analytics']);

            // Decode base64 custom code for analytics
            foreach ($settings['google_analytics'] as &$value) {
                if (array_key_exists('custom_var', $value)
                    && !empty($value['custom_var'])
                ) {
                    $value['custom_var'] = base64_decode($value['custom_var']);
                }
            }
        }

        // Decode scripts
        foreach ([ 'body_end_script', 'body_start_script', 'header_script' ] as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = base64_decode($settings[$key]);
            }
        }

        foreach ([ 'logo_enabled' ] as $key) {
            $settings[$key] = $this->get('data.manager.adapter')
                ->adapt($key, $settings[$key]);
        }

        $toint = [ 'items_in_blog', 'items_per_page', 'elements_in_rss', 'refresh_interval' ];
        foreach ($toint as $key) {
            $settings[$key] = (int) $settings[$key];
        }

        $settings = array_filter($settings, function ($a) {
            return !empty($a);
        });

        return new JsonResponse([
            'instance' => [
                'country' => $this->get('core.instance')->country
            ],
            'extra'    => [
                'countries' => $this->get('core.geo')->getCountries(),
                'locales'   => [
                    'backend'  => $locale->getAvailableLocales(),
                    'frontend' => $locale->setContext('frontend')->getAvailableLocales()
                ],
                'timezones' => \DateTimeZone::listIdentifiers(),
                'prefix'    => $this->get('core.instance')->getMediaShortPath()
                    . '/sections/'
            ],
            'settings' => $settings,
        ]);
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
        $defaults = array_fill_keys($this->keys, null);
        $country  = $request->get('instance');
        $files    = $request->files->get('settings');
        $settings = $request->get('settings');
        $msg      = $this->get('core.messenger');

        // Save country for instance
        $instance = $this->get('core.instance');
        $instance->merge($country);
        $this->get('orm.manager')->persist($instance);

        // Save files
        if (!empty($files)) {
            $settings = array_merge($settings, $this->saveFiles($files));
        }

        $settings = array_merge($defaults, $settings);

        // Remove settings for only masters
        if (!$this->getUser()->isMaster()) {
            foreach ($this->onlyMasters as $key) {
                unset($settings[$key]);
            }
        }

        // Strip tags
        foreach ([ 'site_description', 'site_title', 'site_keywords' ] as $key) {
            if (array_key_exists($key, $settings) && !empty($settings[$key])) {
                $settings[$key] = trim(strip_tags($settings[$key]));
            }
        }

        // Encode scripts
        foreach ([ 'body_end_script', 'body_start_script', 'header_script' ] as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = base64_encode($settings[$key]);
            }
        }

        // Encode Google Analytics custom vars
        if (array_key_exists('google_analytics', $settings)
            && is_array($settings['google_analytics'])
        ) {
            foreach ($settings['google_analytics'] as &$element) {
                if (array_key_exists('custom_var', $element) &&
                    !empty($element['custom_var'])
                ) {
                    $element['custom_var'] = base64_encode($element['custom_var']);
                }
            }
        }

        // TODO: Remove this hack when frontend settings name are updated
        $settings = $this->updateOldSettingsName($settings);

        // Save settings
        $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->set($settings);

        // Delete caches for custom_css and frontpages
        $this->get('core.dispatcher')->dispatch('setting.update');

        // TODO: Remove when using new ORM features
        $cache = $this->get('cache');
        foreach ($this->keys as $key) {
            $cache->delete($key);
        }

        $msg->add(_('Settings saved.'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getcode());
    }

    /**
     * Saves a list of files and returns the list of filenames.
     *
     * @param array $files The list of files to save.
     *
     * @return array The list of filenames.
     */
    protected function saveFiles($files)
    {
        $dir      = MEDIA_PATH . '/sections/';
        $msg      = $this->get('core.messenger');
        $settings = [];

        // Check if upload directory is already created
        if (!is_dir($dir)) {
            \Onm\FilesManager::createDirectory($dir);
        }

        foreach ($files as $key => $file) {
            list(, $width) = getimagesize($file);

            if ($width > 120) {
                $msg->add(
                    _('The maximum height for the "Site Logo" is 120px. Please adjust your image size.'),
                    'error',
                    400
                );

                continue;
            }

            $name = $file->getClientOriginalName();

            $file->move($dir, $name);

            $settings[$key] = $name;
        }

        return $settings;
    }

    /**
     * Update old settings name with new values
     *
     * @param array $settings The list of settings.
     *
     * @return array $settings The list of settings with old name updated.
     */
    protected function updateOldSettingsName($settings)
    {
        if (array_key_exists('facebook', $settings)
            && is_array($settings['facebook'])
        ) {
            if (array_key_exists('page', $settings['facebook'])) {
                $settings['facebook_page'] = $settings['facebook']['page'];
            }

            if (array_key_exists('id', $settings['facebook'])) {
                $settings['facebook_id'] = $settings['facebook']['id'];
            }
        }

        if (array_key_exists('logo_enabled', $settings)) {
            $settings['section_settings']['allowLogo'] = $settings['logo_enabled'];
        }

        return $settings;
    }
}
