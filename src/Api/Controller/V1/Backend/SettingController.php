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
     */
    public function listAction()
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
            'logo_enabled', 'site_agency', 'site_color', 'site_color_secondary',
            'site_description', 'site_footer', 'site_footer', 'site_keywords',
            'site_language', 'site_logo', 'site_name', 'site_title',
            'twitter_page', 'vimeo_page', 'webmastertools_bing',
            'webmastertools_google', 'youtube_page',
            'robots_txt_rules', 'chartbeat',
            'body_end_script', 'body_start_script','header_script',
            'elements_in_rss', 'redirection', 'locale'
        ];

        $settings = $this->get('setting_repository')->get($keys);

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

        foreach ([ 'locale', 'logo_enabled' ] as $key) {
            $settings[$key] = $this->get('data.manager.adapter')
                ->adapt($key, $settings[$key]);
        }

        $toint = [ 'items_in_blog', 'items_per_page', 'elements_in_rss', 'refresh_interval' ];
        foreach ($toint as $key) {
            $settings[$key] = (int) $settings[$key];
        }

        return new JsonResponse([
            'country'   => $this->get('core.instance')->country,
            'extra'     => [
                'countries' => $this->get('core.geo')->getCountries(),
                'timezones' => \DateTimeZone::listIdentifiers(),
                'locales'   => $this->get('core.locale')->getLocales()
            ],
            'settings'  => $settings,
        ]);
    }
}
