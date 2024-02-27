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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Annotation\Security;

/**
 * Displays and saves system settings.
 */
class ThemeSettingController extends SettingController
{
    /**
     * The list of settings that must be base64 encoded/decoded.
     *
     * @var array
     */
    protected $base64Encoded = [];

    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'site_color',
        'site_color_secondary',
        'full_rss',
        'theme_font',
        'theme_font_secondary',
        'theme_skin',
        'logo_enabled',
        'logo_default',
        'logo_simple',
        'logo_favico',
        'logo_embed',
        'theme_options',
    ];

    /**
     * The list of settings that must be parsed to int.
     *
     * @var array
     */
    protected $toint = [
        'logo_enabled',
        'logo_default',
        'logo_simple',
        'logo_favico',
        'logo_embed',
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [];

    /**
     * Performs the action of saving the configuration settings
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('MASTER')")
     */
    public function saveAction(Request $request)
    {
        return parent::saveSettings($request->get('settings'));
    }

    public function listAction(Request $request)
    {
        $settingHelper = $this->container->get('core.helper.theme_settings');


        $settings = parent::listAction($request);
        if (!array_key_exists('theme_options', $settings['settings'])) {
            $settings['settings']['theme_options'] = $settingHelper->getThemeSettings();
        }
        if (array_key_exists('theme_skin', $settings['settings'])) {
            $skinParams = $this->container->get('core.theme')->getSkin($settings['settings']['theme_skin']);
            $settings['settings']['theme_skin'] = strtolower($skinParams['internal_name']);
        }

        return new JsonResponse(
            array_merge_recursive(
                $settings,
                [
                    'extra' => [
                        'theme_skins' => $this->get('core.theme')->getSkins()
                    ]
                ]
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
     * @Security("hasExtension('MASTER')
     *     and hasPermission('MASTER')")
     */
    public function downloadAction()
    {
        $themeOptions = $this->container->get('core.helper.theme_settings')->getThemeSettings();

        $response = new JsonResponse($themeOptions);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Content-Disposition', 'attachment; filename=theme_settings.json');
        $response->headers->set('Cache-Control', 'no-cache');

        return $response;
    }

    /**
     * Import valid JSON as a theme settings
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('MASTER')
     *     and hasPermission('MASTER')")
     */
    public function importAction(Request $request)
    {
        $jsonSettings = $request->request->get('theme_settings', null);

        if ($this->isValidJsonSettings($jsonSettings)) {
            $settings       = json_decode($jsonSettings, true);
            $currenSettings = $this->container->get('core.helper.theme_settings')->getThemeSettings();
            $finalSettings  = array_merge($currenSettings, $settings);
            return parent::saveSettings(['theme_options' => $finalSettings]);
        }

        return new JsonResponse(_('Invalid parameters, please add a valid JSON'), 400);
    }

    /**
     * Restore theme settings to default value
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('MASTER')
     *     and hasPermission('MASTER')")
     */
    public function restoreAction()
    {
        $settingHelper = $this->container->get('core.helper.theme_settings');
        $themeOptions = $settingHelper->getThemeSettings(true);
        return parent::saveSettings(['theme_options' => $themeOptions]);
    }

    public function isValidJsonSettings($jsonSettings)
    {
        try {
            $settings     = json_decode($jsonSettings, true);
            $baseSettings = $this->container->get('core.helper.theme_settings')->getThemeSettings(true, false);

            foreach ($settings as $settingName => $settingValue) {
                if (!array_key_exists($settingName, $baseSettings)) {
                    return false;
                }
                if (!array_key_exists($settingValue, $baseSettings[$settingName]['options'])) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
