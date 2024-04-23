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
    protected $base64Encoded = [
        'custom_css'
    ];

    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'custom_css',
        'theme_options',
    ];

    /**
     * The list of settings that must be parsed to int.
     *
     * @var array
     */
    protected $toint = [];

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
        $settings = $request->get('settings');
        if (array_key_exists('custom_css', $settings)) {
            $settings['custom_css'] = strip_tags($settings['custom_css']);
        }

        return parent::saveSettings($settings);
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

        $settings       = json_decode($jsonSettings, true);
        $baseSettings   = $this->container->get('core.helper.theme_settings')->getThemeSettings(true, false);
        $currenSettings = $this->container->get('core.helper.theme_settings')->getThemeSettings();

        foreach ($settings as $settingName => $settingValue) {
            if (!array_key_exists($settingName, $baseSettings)) {
                unset($settings[$settingName]);
                continue;
            }

            if (!array_key_exists($settingValue, $baseSettings[$settingName]['options'])) {
                unset($settings[$settingName]);
            }
        }

        $finalSettings = array_merge($currenSettings, $settings);
        return parent::saveSettings(['theme_options' => $finalSettings]);
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
}
