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
class SitemapSettingController extends SettingController
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
    protected $keys  = [
        'sitemap',
    ];
    protected $toint = [
        'sitemap',
    ];
    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [
        'sitemap'
    ];

    public function saveAction(Request $request)
    {
        $settings = $request->get('settings');

        if (array_key_exists('sitemap', $settings) && !empty($settings['sitemap'])) {
            $remove = false;
            $config = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('sitemap');

            foreach ($settings['sitemap'] as $key => $value) {
                if ($key === 'total' || $value === $config[$key]) {
                    continue;
                }

                $remove = true;
            }

            if ($remove) {
                $this->get('core.helper.sitemap')->deleteSitemaps();
            }
        }

        return parent::saveAction($request);
    }
}
