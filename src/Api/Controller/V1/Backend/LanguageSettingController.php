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
        'translators'
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
}
