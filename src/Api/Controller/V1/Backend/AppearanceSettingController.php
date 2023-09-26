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
class AppearanceSettingController extends SettingController
{
    /**
     * The list of settings that must be base64 encoded/decoded.
     *
     * @var array
     */
    protected $base64Encoded = [];

    protected $toint = [
        'items_in_blog',
        'items_per_page',
        'elements_in_rss',
        'logo_enabled',
        'logo_default',
        'logo_simple',
        'logo_favico',
        'logo_embed',
    ];
    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'site_color',
        'site_color_secondary',
        'logo_enabled',
        'cookies',
        'cookies_hint_url',
        'cmp_type',
        'cmp_id',
        'cmp_apikey',
        'browser_update',
        'items_per_page',
        'items_in_blog',
        'elements_in_rss',
        'logo_default',
        'logo_simple',
        'logo_favico',
        'logo_embed',
    ];

    protected $references = [
        'logo_simple' => 'Small logo',
        'logo_favico' => 'Favico',
        'logo_default' => 'Large logo',
        'logo_embed' => 'Social network default image'
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [];

    public function saveAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $settings = $request->get('settings');

        try {
            $settings = array_merge($this->saveFiles($settings), $settings);
            return parent::saveSettings($settings);
        } catch (\Exception $e) {
            return new JsonResponse($msg->getMessages(), $msg->getcode());
        }
    }

    protected function saveFiles($settings)
    {
        $logos    = array_intersect_key($settings, array_keys($this->references));
        $msg      = $this->get('core.messenger');
        $settings = [];
        foreach ($logos as $key => $id) {
            $logo   = $this->container->get('core.helper.content')->getContent($id, 'photo');
            $height = $this->container->get('core.helper.photo')->getPhotoHeight($logo);
            $width  = $this->container->get('core.helper.photo')->getPhotoWidth($logo);
            if ($key == 'logo_embed' && ($width < 200 || $height < 200)) {
                $msg->add(
                    sprintf(
                        _('The minimun size for the %s is 200x200. Please adjust your image size.'),
                        $this->references[$key]
                    ),
                    'error',
                    400
                );

                throw new \Exception("The minimun size for the %s is 200x200. Please adjust your image size.");
            } elseif ($height > 120 && $key !== 'logo_embed') {
                $msg->add(
                    sprintf(
                        _('The maximum height for the %s is 120px. Please adjust your image size.'),
                        $this->references[$key]
                    ),
                    'error',
                    400
                );

                throw new \Exception("The maximum height for the %s is 120px. Please adjust your image size.");
            }

            $settings[$key] = $this->container->get('core.helper.setting')->getLogo($key)->pk_content ?? null;
        }

        return $settings;
    }

    public function listAction(Request $request)
    {
        return new JsonResponse(
            parent::listAction($request)
        );
    }
}
