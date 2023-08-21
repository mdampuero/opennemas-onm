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
class MasterSettingController extends SettingController
{
    /**
     * The list of settings that must be base64 encoded/decoded.
     *
     * @var array
     */
    protected $base64Encoded = [
        'body_end_script',
        'body_end_script_amp',
        'body_start_script',
        'body_start_script_amp',
        'custom_css_amp',
        'header_script',
        'header_script_amp',
    ];

    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'body_end_script',
        'body_end_script_amp',
        'body_start_script',
        'body_start_script_amp',
        'custom_css_amp',
        'disable_default_ga',
        'frontpage_max_items',
        'full_rss',
        'gfk',
        'header_script',
        'header_script_amp',
        'redirection',
        'robots_txt_rules',
        'theme_font',
        'theme_font_secondary',
        'theme_skin',
        'seo_information',
    ];

    /**
     * The list of settings that must be parsed to int.
     *
     * @var array
     */
    protected $toint = [
        'frontpage_max_items',
        'seo_information',
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [
        'body_end_script',
        'body_end_script_amp',
        'body_start_script',
        'body_start_script_amp',
        'custom_css_amp',
        'disable_default_ga',
        'frontpage_max_items',
        'full_rss',
        'gfk',
        'header_script',
        'header_script_amp',
        'redirection',
        'robots_txt_rules',
        'seo_information',
    ];

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
        return new JsonResponse(
            array_merge_recursive(
                parent::listAction($request),
                [
                    'extra' => [
                        'theme_skins' => $this->get('core.theme')->getSkins()
                    ]
                ]
            )
        );
    }
}
