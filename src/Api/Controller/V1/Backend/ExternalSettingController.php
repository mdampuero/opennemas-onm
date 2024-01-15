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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays and saves system settings.
 */
class ExternalSettingController extends SettingController
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
        'actOn.authentication',
        'adobe_base',
        'chartbeat',
        'comscore',
        'data_layer',
        'facebook',
        'facebook_id',
        'ga4_native_config',
        'ga4_native_id',
        'gfk',
        'google_analytics',
        'google_analytics_others',
        'google_custom_search_api_key',
        'google_news_name',
        'google_maps_api_key',
        'google_tags_id',
        'google_tags_id_amp',
        'instagram_page',
        'linkedin_page',
        'marfeel_compass',
        'marfeel_pass',
        'telegram_page',
        'whatsapp_page',
        'tiktok_page',
        'dailymotion_page',
        'ojd',
        'payments',
        'pinterest_page',
        'prometeo',
        'recaptcha',
        'twitter_page',
        'vimeo_page',
        'youtube_page',
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [
        'adobe_base',
        'gfk',
        'payments',
        'ga4_native_config',
        'ga4_native_id',
    ];

    public function listAction(Request $request)
    {
        return new JsonResponse(
            array_merge_recursive(
                parent::listAction($request),
                [
                    'extra'    => [
                        'data_types'  => $this->get('core.service.data_layer')->getTypes()
                    ]
                ]
            )
        );
    }
}
