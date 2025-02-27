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
class LanguageSettingController extends SettingController
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
        'time_zone',
        'locale',
        'translators',
        'translatorsDefault'
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [];

    public function saveAction(Request $request)
    {
        $country = $request->get('instance');

        // Save country for instance
        $instance = $this->get('core.instance');
        $instance->merge($country);
        $this->get('orm.manager')->persist($instance);

        return parent::saveAction($request);
    }

    public function listAction(Request $request)
    {
        $locale = $this->get('core.locale');

        return new JsonResponse(
            array_merge_recursive(
                parent::listAction($request),
                [
                    'instance' => [
                        'country' => $this->get('core.instance')->country
                    ],
                    'extra'    => [
                        'countries' => $this->get('core.geo')->getCountries(),
                        'locales'   => [
                            'backend'  => $locale->getAvailableLocales('backend'),
                            'frontend' => $locale->getAvailableLocales('frontend')
                        ],
                        'timezones' => \DateTimeZone::listIdentifiers(),
                        'translation_services' =>
                            $this->get('core.factory.translator')->getTranslatorsData(),
                    ]
                ]
            )
        );
    }
}
