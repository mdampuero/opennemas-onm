<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Renderer;

/**
 * The AdvertisementRenderer service provides methods to generate the HTML code
 * for advertisements basing on the advertisements information.
 */
class AdvertisementRenderer
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the AdvertisementRenderer
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;

        $this->router = $this->container->get('router');
        $this->sm     = $this->container->get('setting_repository');
        $this->tpl    = $this->container->get('core.template.admin');
    }

    /**
     * Renders an advertisement.
     *
     * @param Advertisement $ad The advertisement to render.
     *
     * @return string The HTML for the slot.
     */
    public function render(\Advertisement $ad)
    {
        if ($ad->with_script == 1) {
            return $ad->script;
        } elseif ($ad->with_script == 2) {
            return $this->renderReviveSlot($ad);
        } elseif ($ad->with_script == 3) {
            return  $this->renderDFPSlot($ad);
        }

        return $this->renderImage($ad);
    }

    /**
     * Generates the HTML header section for the DFP ads.
     *
     * @param array $ads    The list of advertisements to generate the header
     *                      from.
     * @param array $params The list of parameters.
     *
     * @return string the HTML content for the DFP slot.
     */
    public function renderDFPHeader($ads, $params)
    {
        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 3
                && array_key_exists('googledfp_unit_id', $a->params)
                && !empty($a->params['googledfp_unit_id']);
        });

        if (empty($ads)) {
            return '';
        }

        $zones = [];
        foreach ($ads as $advertisement) {
            // TODO: Check Api/AdvertisementController::getSizes.
            $sizes = array_map(function ($a) {
                return "[ {$a['width']}, {$a['height']} ]";
            }, $advertisement->params['sizes']);

            $sizes = '[ ' . implode(', ', $sizes) . ' ]';

            $zones[] = [
                'id'    => $advertisement->id,
                'dfpId' => $advertisement->params['googledfp_unit_id'],
                'sizes' => $sizes
            ];
        }

        $options    = $this->sm->get('dfp_options');
        $customCode = $this->sm->get('dfp_custom_code');

        if (!empty($customCode)) {
            $customCode = base64_decode($dfpCustomCode);
        }

        return $this->tpl->fetch('advertisement/helpers/inline/dfp_header.tpl', [
            'category'      => $category,
            'extension'     => $extension,
            'customCode'    => $customCode,
            'options'       => $options,
            'targetingCode' => $targetingCode,
            'zones'         => $zones
        ]);
    }

    /**
     * Renders a DFP advertisement slot.
     *
     * @param Advertisement $ad The advertisement to render.
     *
     * @return string The HTML content for the DFP advertisement slot.
     */
    public function renderDFPSlot($ad)
    {
        return $this->tpl->fetch('advertisement/helpers/inline/dfp_slot.tpl', [
            'id' => $ad->pk_advertisement
        ]);
    }

    /**
     * Renders an image/swf based advertisement.
     *
     * @param string $ad The advertisement to render.
     *
     * @return string The HTML code for the advertisement.
     */
    public function renderImage($ad)
    {
        try {
            $img = $this->container->get('entity_repository')
                ->find('Photo', $ad->img);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return '';
        }

        $template = 'advertisement/helper/inline/image.tpl';

        if ($img->type_img === 'swf') {
            $template = 'advertisement/helper/inline/flash.tpl';
        }

        return $this->tpl->fetch($template, [
            'height'   => $img->height,
            'mediaUrl' => $img->path_img . $img->path_file,
            'src'      => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME
                . '/images' . $img->path_file . $img->name,
            'url'      => $this->get('router')
                ->generate('frontend_ad_redirect', [
                    'id' => $publicId
                ]),
            'width'    => $img->width
        ]);
    }

    /**
     * Generates the HTML code to include in header for Revive advertisements.
     *
     * @param array The list of advertisements.
     *
     * @return string The HTML code to include in header.
     */
    public function renderReviveHeader($ads)
    {
        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 2
                && array_key_exists('openx_zone_id', $a->params)
                && !empty($a->params['openx_zone_id']);
        });

        if (empty($ads)) {
            return '';
        }

        $config = $this->sm->get('revive_ad_server');
        $zones  = [];

        foreach ($ads as $ad) {
            $zones =  [
                'id'      => $ad->id,
                'openXId' => (int) $ad->params['openx_zone_id']
            ];
        }

        return $this->tpl
            ->fetch('advertisement/helpers/inline/revive.header.tpl', [
                'config' => $config,
                'zones'  => $zones
            ]);
    }

    /**
     * Renders a Revive advertisement.
     *
     * @param Advertisement $ad the ad to render.
     *
     * @return string the HTML content for the DFP slot.
     */
    public function renderReviveSlot($ad)
    {
        $iframe = in_array($ad->type_advertisement, [ 50, 150, 250, 350, 450, 550 ]);
        $url    = $this->router->generate('frontend_ad_get', [
            'id' => $ad->pk_content
        ]);

        return $this->tpl
            ->fetch('advertisement/helpers/inline/revive.slot.tpl', [
                'id'     => $ad->id,
                'iframe' => $iframe,
                'url'    => $url,
            ]);
    }
}
