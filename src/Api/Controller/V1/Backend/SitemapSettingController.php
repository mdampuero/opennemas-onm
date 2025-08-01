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
class SitemapSettingController extends SettingController
{
    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'sitemap',
    ];

    /**
     * The list of settings that must be parsed to int.
     *
     * @var array
     */
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
    public function saveAction(Request $request)
    {
        $settings = $request->get('settings');
        $settings = is_array($settings) ? $settings : [ $settings ];

        if (array_key_exists('sitemap', $settings) && !empty($settings['sitemap'])) {
            $remove = false;
            $config = $this->get('orm.manager')->getDataSet('Settings', 'instance')->get('sitemap');

            if (!isset($config) || !is_array($config)) {
                $config = [];
            }

            foreach ($settings['sitemap'] as $key => $value) {
                if (!array_key_exists($key, $config) || $value === $config[$key]) {
                    continue;
                }

                $remove = true;
            }

            if ($remove) {
                $this->get('core.helper.sitemap')->deleteSitemaps();
            }
        }

        return parent::saveSettings($settings);
    }

    /**
     * Returns the list of settings.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('MASTER')
     *     and hasPermission('MASTER')")
     */
    public function listAction(Request $request)
    {
        $ss['settings']['sitemap'] = array_map(function ($e) {
            return (int) $e;
        }, $this->get('core.helper.sitemap')->getSettings());

        return new JsonResponse(
            array_merge_recursive(
                $ss,
                [
                    'extra' => [
                        'sitemaps' => $this->get('core.helper.sitemap')->getSitemapsInfo(),
                    ]
                ]
            )
        );
    }
}
