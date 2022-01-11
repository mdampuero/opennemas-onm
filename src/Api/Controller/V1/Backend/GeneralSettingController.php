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

/**
 * Displays and saves system settings.
 */
class GeneralSettingController extends SettingController
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
        'refresh_interval',
        'site_name',
        'site_description',
        'site_title',
        'site_footer',
        'site_keywords',
        'webmastertools_bing',
        'webmastertools_google',
    ];

    protected $toint = [
        'refresh_interval',
    ];
    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [];
}
