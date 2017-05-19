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
     * Renders an advertisement given some params
     *
     * @param Advertisement $ad The advertisement to render.
     * @param array $params an array of parameters to render the ad
     *
     * @return string the HTML content for the advertisement
     **/
    public function render(\Advertisement $ad, $params = [])
    {
        $safeFrame = $this->container->get('setting_repository')
            ->get('ads_settings')['safe_frame'];

        if ($safeFrame) {
            $content = $this->renderSafeFrameSlot($ad, $params);
        } else {
            $content = $this->renderInline($ad, $params);

            $tpl   = '<div class="ad-slot oat%s">%s</div>';
            $class = ' oat-visible text-center oat-'.$ad->params['orientation'];

            $content = sprintf($tpl, $class, $content);
        }

        return $content;
    }

    /**
     * Renders an advertisement.
     *
     * @param Advertisement $ad The advertisement to render.
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad)
    {
        if ($ad->with_script == 1) {
            return $ad->script;
        } elseif ($ad->with_script == 2) {
            return $this->renderInlineReviveSlot($ad);
        } elseif ($ad->with_script == 3) {
            return  $this->renderInlineDFPSlot($ad);
        }

        return $this->renderInlineImage($ad);
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
    public function renderInlineDFPHeader($ads, $params)
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
            $zones[] = [
                'id'    => $advertisement->id,
                'dfpId' => $advertisement->params['googledfp_unit_id'],
                'sizes' => $advertisement->getSizes()
            ];
        }
        $targetingCode = $this->getDFPTargeting($params['category'], $params['extension']);

        $options    = $this->sm->get('dfp_options');
        $customCode = $this->getDFPCustomCode();

        return $this->tpl->fetch('advertisement/helpers/inline/dfp.header.tpl', [
            'category'      => $params['category'],
            'extension'     => $params['extension'],
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
    public function renderInlineDFPSlot($ad)
    {
        return $this->tpl->fetch('advertisement/helpers/inline/dfp.slot.tpl', [
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
    public function renderInlineImage($ad)
    {
        try {
            $img = $this->container->get('entity_repository')
                ->find('Photo', $ad->img);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return '';
        }

        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);
        $template = 'advertisement/helpers/inline/image.tpl';

        if ($img->type_img === 'swf') {
            $template = 'advertisement/helpers/inline/flash.tpl';
        }

        return $this->tpl->fetch($template, [
            'height'   => $img->height,
            'mediaUrl' => $img->path_img . $img->path_file,
            'src'      => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME
                . '/images' . $img->path_file . $img->name,
            'url'      => $this->container->get('router')
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
    public function renderInlineReviveHeader($ads)
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
    public function renderInlineReviveSlot($ad)
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

    /**
     * Returns the HTML for a safe frame ad slot
     *
     * @param  Advertisement $ad The ad to render.
     * @param array $params the list of parameters
     *
     * @return string the HTML generated
     **/
    public function renderSafeFrameSlot(\Advertisement $ad, $params)
    {
        $html  = '<div class="ad-slot oat"%s data-type="%s"%s></div>';
        $id    = '';
        $type  = $ad->type_advertisement;
        $width = '';

        // Style for advertisements via renderbanner
        if (array_key_exists('width', $params)) {
            $width = sprintf(
                ' data-width="%d"',
                (int) $params['width']
            );
        }

        // Style for floating advertisements in frontpage manager
        if ($ad->type_advertisement == 37) {
            $id .= ' data-id="' . $ad->pk_content . '" ';
        }

        return sprintf($html, $id, $type, $width);
    }

    /**
     * Renders a SafeFrame document for an advertisement
     *
     * @param  Advertisement $ad The ad to render.
     * @param array $params the list of parameters
     *
     * @return string the HTML generated
     **/
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        if ($ad->with_script == 1) {
            return $this->renderSafeFrameHtml($ad);
        } elseif ($ad->with_script == 2) {
            return $this->renderSafeFrameRevive($ad, $params);
        } elseif ($ad->with_script == 3) {
            return  $this->renderSafeFrameDFP($ad, $params);
        }

        $img = $this->get('entity_repository')->find('Photo', $ad->img);

        if (!empty($img) && strtolower($img->type_img) == 'swf') {

            return $this->renderSafeFrameFlash($ad, $img);
        }

        return $this->renderSafeFrameImage($ad, $img);
    }

    /**
     * Returns the HTML code for a OpenX advertisement.
     *
     * @param Advertisement $ad       The advertisement object.
     * @param string        $category The current category.
     *
     * @return string The HTML code for the OpenX advertisement.
     */
    protected function renderSafeFrameRevive($ad, $params)
    {
        $params = [
            'id'        => $ad->id,
            'category'  => $params['category'],
            'extension' => $params['extension'],
            'openXId'   => $ad->params['openx_zone_id'],
            'url'       => $this->get('setting_repository')
                ->get('revive_ad_server')['url']
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/openx.tpl', $params);
    }

    /**
     * Returns the HTML code for a Google DFP advertisement.
     *
     * @param Advertisement $ad       The advertisement object.
     * @param string        $category The current category.
     *
     * @return string The HTML code for the Google DFP advertisement.
     */
    protected function renderSafeFrameDFP($ad, $params)
    {
        $params = [
            'id'        => $ad->id,
            'dfpId'     => $ad->params['googledfp_unit_id'],
            'sizes'     => $ad->getSizes($ad->normalizeSizes($ad->params)),
            'targetingCode' => $this->getDFPTargeting($params['category'], $params['extension']),
            'customCode'    => $this->getDFPCustomCode()
        ];

        return $this->tpl->fetch('advertisement/helpers/safeframe/dfp.tpl', $params);
    }

    /**
     * Returns the HTML code for a flash-based advertisement.
     *
     * @param Advertisement $ad  The advertisement object.
     * @param Photo         $img The flash object.
     *
     * @return string The HTML code for a flash-based advertisement.
     */
    protected function renderSafeFrameFlash($ad, $img)
    {
        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);

        $params = [
            'width'  => $img->width,
            'height' => $img->height,
            'src'    => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME . '/images'
                . $img->path_file . $img->name,
            'url'    => $this->get('router')->generate('frontend_ad_redirect', [
                'id' => $publicId
            ])
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/flash.tpl', $params);
    }

    /**
     * Returns the HTML code for a HTML/JS advertisement.
     *
     * @param Advertisement $ad The advertisement object.
     *
     * @return string The HTML code for the HTML/JS advertisement.
     */
    protected function renderSafeFrameHtml($ad)
    {
        $tpl   = '<html><style>%s</style><body><div class="content">%s</div></body>';
        $html  = $ad->script;
        $style = 'body { margin: 0; overflow: hidden; padding: 0; text-align:'
            . ' center; } img { max-width: 100% }';

        return sprintf($tpl, $style, $html);
    }

    /**
     * Returns the HTML code for an image-based advertisement.
     *
     * @param Advertisement $ad  The advertisement object.
     * @param Photo         $img The image object.
     *
     * @return string The HTML code for the image-based advertisement.
     */
    protected function renderSafeFrameImage($ad, $img)
    {
        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);

        $params = [
            'category' => $img->category_name,
            'width'    => $img->width,
            'height'   => $img->height,
            'src'      => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME
                . '/images' . $img->path_file . $img->name,
            'url'      => $this->get('router')
                ->generate('frontend_ad_redirect', [
                    'id' => $publicId
                ]),
        ];

        return $this->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/image.tpl', $params);
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getDFPCustomCode()
    {
        $code = $this->container->get('setting_repository')->get('dfp_custom_code');

        if (empty($code)) {
            return '';
        }

        return base64_decode($code);
    }

    /**
     * Returns the targeting-related JS code for google DFP.
     *
     * @param string $category The current category.
     *
     * @return string The targeting-related JS code.
     */
    protected function getDFPTargeting($category, $module)
    {
        $options = $this->container->get('setting_repository')->get('dfp_options');

        if (!is_array($options)) {
            return '';
        }

        $module = ($module == 'frontpages') ? 'home' : $module;

        $targetingCode = '';
        if (array_key_exists('target', $options) && !empty($options['target'])) {
            $targetingCode .=
                "googletag.pubads().setTargeting('{$options['target']}', ['{$category}']);\n";
        }

        if (array_key_exists('module', $options) && !empty($options['module'])) {
            $targetingCode .=
                "googletag.pubads().setTargeting('{$options['module']}', ['{$module}']);\n";
        }

        return $targetingCode;
    }
}
