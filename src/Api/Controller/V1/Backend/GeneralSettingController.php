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

/**
 * Displays and saves system settings.
 */
class GeneralSettingController extends SettingController
{
    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'refresh_enabled',
        'refresh_interval',
        'site_name',
        'site_description',
        'site_title',
        'site_footer',
        'site_keywords',
        'webmastertools_bing',
        'webmastertools_google',
    ];

    /**
     * The list of settings that must be parsed to int.
     *
     * @var array
     */
    protected $toint = [
        'refresh_enabled',
        'refresh_interval',
    ];

    public function listAction(Request $request)
    {
        return new JsonResponse(
            parent::listAction($request)
        );
    }
}
