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
        'automatic_translators', 'comscore', 'contact_email',
        'cookies_hint_enabled', 'cookies_hint_url', 'facebook', 'facebook_id',
        'facebook_page', 'favico', 'google_analytics',
        'google_analytics_others', 'google_custom_search_api_key',
        'google_maps_api_key', 'google_tags_id', 'google_news_name',
        'google_page', 'googleplus_page', 'instagram_page', 'items_in_blog',
        'items_per_page', 'linkedin_page', 'max_session_lifetime',
        'mobile_logo', 'ojd', 'onm_digest_pass', 'onm_digest_user',
        'paypal_mail', 'pinterest_page', 'piwik', 'recaptcha',
        'refresh_interval', 'rtb_files', 'logo_enabled', 'site_agency',
        'site_color', 'site_color_secondary', 'site_description', 'site_footer',
        'site_footer', 'site_keywords', 'site_language', 'site_logo',
        'site_name', 'site_title', 'twitter_page', 'vimeo_page',
        'webmastertools_bing', 'webmastertools_google', 'youtube_page',
        'robots_txt_rules', 'chartbeat', 'body_end_script', 'body_start_script',
        'header_script', 'elements_in_rss', 'redirection', 'locale'
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
        $locales = $this->get('core.locale')->getAvailableLocales();

        if (!empty($query)) {
            $locales = array_filter($locales, function ($a) use ($query) {
                return strpos(strtolower($a), strtolower($query)) !== false;
            });
        }

        $keys    = array_keys($locales);
        $values  = array_values($locales);
        $locales = [];

        for ($i = 0; $i < count($keys); $i++) {
            $locales[] = [ 'code' => $keys[$i], 'name' => $values[$i] ];
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

        foreach ([ 'locale', 'logo_enabled' ] as $key) {
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
                    'backend'  => $this->get('core.locale')->getLocales(),
                    'frontend' => $this->getFrontendLocales($settings)
                ],
                'timezones' => \DateTimeZone::listIdentifiers(),
                'prefix'    => $this->get('core.instance')->getMediaShortPath()
                    . '/sections/',
                'translation_services' => $this->get('core.translate')->getTranslatorsData()
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
     * Returns the list of frontend locales basing on the current locale
     * configuration.
     *
     * @param array $settings The list of settings.
     *
     * @return array The list of frontend locales.
     */
    protected function getFrontendLocales($settings)
    {
        $frontend = [];

        if (empty($settings)
            || !is_array($settings)
            || !array_key_exists('locale', $settings)
            || !array_key_exists('frontend', $settings['locale'])
            || !is_array($settings['locale']['frontend'])
        ) {
            return $frontend;
        }

        $locales = $this->get('core.locale')->getAvailableLocales();

        foreach ($settings['locale']['frontend'] as $code) {
            $frontend[$code] = $locales[$code];
        }

        return $frontend;
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
}
