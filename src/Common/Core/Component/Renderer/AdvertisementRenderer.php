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
        $this->router    = $this->container->get('router');
        $this->tpl       = $this->container->get('core.template.admin');

        $this->ds = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance');
    }

    /**
     * Returns the string that depicts the default mark shown alongside ads
     *
     * @param \Advertisement $ad the advertisement object where to search for the mark
     *
     * @return string The default mark for the advertisements
     */
    public function getMark(\Advertisement $ad = null)
    {
        // If the mark for the advertisement is not empty then return it
        if (is_object($ad) && array_key_exists('mark_text', $ad->params) && !empty($ad->params['mark_text'])) {
            return $ad->params['mark_text'];
        }

        // If the mark is not valid then use the default one.
        $settings    = $this->ds->get('ads_settings');
        $defaultMark = (
                is_array($settings)
                && array_key_exists('default_mark', $settings)
                && !empty($settings['default_mark'])
            )
            ? $settings['default_mark']
            : _('Advertisement');

        return $defaultMark;
    }

    /**
     * Returns the list of CSS classes according to device restrictions for an Ad
     *
     * @param \Advertisement $ad the advertisement to get restrictions from
     *
     * @return string the css classes to apply
     */
    public function getDeviceCSSClasses(\Advertisement $ad)
    {
        if (!array_key_exists('devices', $ad->params)) {
            return '';
        }

        $cssClasses = [];
        foreach ($ad->params['devices'] as $device => $status) {
            if ($status === 0) {
                $cssClasses[] = 'hidden-' . $device;
            }
        }

        return implode(' ', $cssClasses);
    }

    /**
     * Renders an advertisement given some params
     *
     * @param \Advertisement $ad The advertisement to render.
     * @param array $params an array of parameters to render the ad
     *
     * @return string the HTML content for the advertisement
     */
    public function render(\Advertisement $ad, $params = [])
    {
        $safeFrame = $this->ds->get('ads_settings')['safe_frame'];

        if ($safeFrame) {
            return $this->renderSafeFrameSlot($ad, $params);
        }

        $deviceClasses = $this->getDeviceCSSClasses($ad);

        $tpl         = '<div class="ad-slot oat oat-visible oat-%s %s" data-mark="%s">%s</div>';
        $content     = $this->renderInline($ad, $params);
        $mark        = $this->getMark($ad);
        $orientation = empty($ad->params['orientation']) ?
            'top' : $ad->params['orientation'];

        return sprintf($tpl, $orientation, $deviceClasses, $mark, $content);
    }

    /**
     * Renders an advertisement.
     *
     * @param \Advertisement $ad The advertisement to render.
     * @param string $format the render format to use 'amp' or 'inline'
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad, $format = null)
    {
        if ($ad->with_script == 1) {
            return $this->getHtml($ad);
        } elseif ($ad->with_script == 2) {
            return $this->renderInlineReviveSlot($ad);
        } elseif ($ad->with_script == 3) {
            return $this->renderInlineDFPSlot($ad);
        } elseif ($ad->with_script == 4) {
            return $this->renderInlineSmartSlot($ad);
        }

        $img = $this->getImage($ad);

        if (empty($img)) {
            return '';
        }

        return $this->renderInlineImage($ad, $img, $format);
    }

    /**
     * Generates the HTML header section for the DFP ads.
     *
     * @param array  $ads    The list of advertisements to generate the header from.
     * @param string $format The render format to use 'amp' or 'inline'
     *
     * @return string the HTML content for the DFP slot.
     */
    public function renderInlineDFPHeader($ads, $params)
    {
        if (empty($ads)) {
            return '';
        }

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

        $targetingCode = $this->getDFPTargeting(
            $params['category'],
            $params['extension'],
            $params['content']->id
        );

        $options    = $this->ds->get('dfp_options');
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
     * @param \Advertisement $ad     The advertisement to render.
     * @param string         $format The render format to use 'amp' or 'inline'
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
     * @param string $ad     The advertisement to render.
     * @param \Photo $img    The image object.
     * @param string $format The render format to use 'amp' or 'inline'
     *
     * @return string The HTML code for the advertisement.
     */
    public function renderInlineImage($ad, $img, $format = null)
    {
        $publicId = date('YmdHis', strtotime($ad->created)) .
            sprintf('%06d', $ad->pk_advertisement);
        $template = 'advertisement/helpers/inline/image.tpl';

        if ($img->type_img === 'swf') {
            $template = 'advertisement/helpers/inline/flash.tpl';
        }

        if ($format == 'amp') {
            $template = 'advertisement/helpers/inline/image.amp.tpl';
        }

        return $this->tpl->fetch($template, [
            'height'   => $img->height,
            'mediaUrl' => $img->path_img . $img->path_file,
            'src'      => SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME
                . '/images' . $img->path_file . $img->name,
            'url'      => $this->container->get('router')->generate(
                'frontend_ad_redirect',
                [ 'id' => $publicId ],
                true
            ),
            'width'    => $img->width
        ]);
    }

    /**
     * Generates the HTML code to include in header for Revive advertisements.
     *
     * @param array $ads The list of advertisements.
     *
     * @return string The HTML code to include in header.
     */
    public function renderInlineReviveHeader($ads)
    {
        if (empty($ads)) {
            return '';
        }
        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 2
                && array_key_exists('openx_zone_id', $a->params)
                && !empty($a->params['openx_zone_id']);
        });

        if (empty($ads)) {
            return '';
        }

        $config = $this->ds->get('revive_ad_server');
        $zones  = [];

        foreach ($ads as $ad) {
            $zones[] = [
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
     * @param \Advertisement $ad the ad to render.
     *
     * @return string the HTML content for the DFP slot.
     */
    public function renderInlineReviveSlot($ad)
    {
        $iframe = in_array($ad->positions, [ 50, 150, 250, 350, 450, 550 ]);
        $url    = $this->router->generate('frontend_ad_show', [
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
     * Generates the HTML code to include in header for Smart advertisements.
     *
     * @param array $ads     The list of advertisements.
     * @param string $format The render format to use 'amp' or 'inline'
     *
     * @return string The HTML code to include in header.
     */
    public function renderInlineSmartHeader($ads, $params)
    {
        if (empty($ads)) {
            return '';
        }

        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 4
                && array_key_exists('smart_format_id', $a->params)
                && !empty($a->params['smart_format_id']);
        });

        if (empty($ads)) {
            return '';
        }

        $config = $this->ds->get('smart_ad_server');
        $zones  = [];

        foreach ($ads as $ad) {
            $zones[] = [
                'id'        => $ad->id,
                'format_id' => (int) $ad->params['smart_format_id']
            ];
        }

        $template = 'smart.header.tpl';
        if (is_array($config)
            && array_key_exists('tags_format', $config)
            && $config['tags_format'] == 'onecall_sync'
        ) {
            $template = 'smart.header.sync.tpl';
        }

        return $this->tpl
            ->fetch('advertisement/helpers/inline/' . $template, [
                'config'        => $config,
                'page_id'       => $config['page_id'][$params['advertisementGroup']],
                'zones'         => $zones,
                'customCode'    => $this->getSmartCustomCode(),
                'targetingCode' => $this->getSmartTargeting(
                    $params['category'],
                    $params['extension'],
                    $params['content']->id
                )
            ]);
    }

    /**
     * Renders a Smart advertisement.
     *
     * @param \Advertisement $ad the ad to render.
     *
     * @return string the HTML content for the Smart slot.
     */
    public function renderInlineSmartSlot($ad)
    {
        $config = $this->ds->get('smart_ad_server');

        $template = 'smart.slot.tpl';
        if (is_array($config)
            && array_key_exists('tags_format', $config)
            && $config['tags_format'] == 'onecall_sync'
        ) {
            $template = 'smart.slot.sync.tpl';
        }

        return $this->tpl
            ->fetch('advertisement/helpers/inline/' . $template, [
                'id' => $ad->params['smart_format_id'],
            ]);
    }

    /**
     * Selects and renders an interstitial from a list of advertisements.
     *
     * @param array $ads The list of advertisements.
     *
     * @return string The HTML code for
     */
    public function renderInlineInterstitial($ads)
    {
        if (empty($ads)) {
            return '';
        }

        $tpl = '<div class="interstitial">'
            . '<div class="interstitial-wrapper" style="width: %s;">'
                . '<div class="interstitial-header">'
                    . '<span class="interstitial-header-title">'
                        . _('Entering on the requested page')
                    . '</span>'
                    . '<a class="interstitial-close-button" href="#" title="'
                        . _('Skip advertisement') . '">'
                        . '<span>' . _('Skip advertisement') . '</span>'
                    . '</a>'
                . '</div>'
                . '<div class="interstitial-content" style="height: %s;">'
                    . '<div class="ad-slot oat oat-visible oat-%s" data-id="%s"'
                        . ' data-timeout="%s" data-type="%s">%s</div>'
                . '</div>'
            . '</div>'
        . '</div>';

        $interstitials = array_filter($ads, function ($a) {
            $hasInterstitial = array_filter($a->positions, function ($pos) {
                return ($pos + 50) % 100 == 0;
            });

            return $hasInterstitial;
        });

        if (empty($interstitials)) {
            return '';
        }

        $ad = $interstitials[array_rand($interstitials)];

        $orientation = empty($ad->params['orientation']) ?
            'top' : $ad->params['orientation'];

        $sizes = $ad->normalizeSizes($ad->params);
        $size  = array_filter($sizes, function ($a) {
            return $a['device'] === 'desktop';
        });

        if (empty($sizes)) {
            $size = $sizes;
        }

        $size = array_shift($sizes);

        return sprintf(
            $tpl,
            $size['width'] . 'px',
            empty($size['height']) ? 'auto' : $size['height'] . 'px',
            $orientation,
            $ad->pk_advertisement,
            empty($ad->timeout) ? 5 : $ad->timeout,
            implode(',', $ad->positions),
            $this->renderInline($ad)
        );
    }

    /**
     * Returns the HTML for a safe frame ad slot
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters
     *
     * @return string the HTML generated
     */
    public function renderSafeFrameSlot(\Advertisement $ad, $params)
    {
        $html  = '<div class="ad-slot oat"%s data-type="%s"%s></div>';
        $id    = '';
        $type  = $ad->positions;
        $width = '';

        // Style for advertisements via renderbanner
        if (array_key_exists('width', $params)) {
            $width = sprintf(
                ' data-width="%d"',
                (int) $params['width']
            );
        }

        // Style for floating advertisements in frontpage manager
        if (array_key_exists('floating', $params) && $params['floating'] == true) {
            $type = 37;
            $id  .= ' data-id="' . $ad->pk_content . '" ';
        }

        return sprintf($html, $id, $type, $width);
    }

    /**
     * Renders a SafeFrame document for an advertisement
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters
     *
     * @return string the HTML generated
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        if ($ad->with_script == 1) {
            return $this->renderSafeFrameHtml($ad);
        } elseif ($ad->with_script == 2) {
            return $this->renderSafeFrameRevive($ad, $params);
        } elseif ($ad->with_script == 3) {
            return $this->renderSafeFrameDFP($ad, $params);
        } elseif ($ad->with_script == 4) {
            return $this->renderSafeFrameSmart($ad, $params);
        }

        $img = $this->getImage($ad);

        if (empty($img)) {
            return '';
        }

        if (strtolower($img->type_img) == 'swf') {
            return $this->renderSafeFrameFlash($ad, $img);
        }

        return $this->renderSafeFrameImage($ad, $img);
    }

    /**
     * Returns the HTML code for a OpenX advertisement.
     *
     * @param \Advertisement $ad       The advertisement object.
     * @param string         $category The current category.
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
            'url'       => $this->ds->get('revive_ad_server')['url']
        ];

        return $this->container->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/openx.tpl', $params);
    }

    /**
     * Returns the HTML code for a Google DFP advertisement.
     *
     * @param \Advertisement $ad       The advertisement object.
     * @param string         $category The current category.
     *
     * @return string The HTML code for the Google DFP advertisement.
     */
    protected function renderSafeFrameDFP($ad, $params)
    {
        $params = [
            'id'            => $ad->id,
            'dfpId'         => $ad->params['googledfp_unit_id'],
            'sizes'         => $ad->getSizes($ad->normalizeSizes($ad->params)),
            'customCode'    => $this->getDFPCustomCode(),
            'targetingCode' => $this->getDFPTargeting(
                $params['category'],
                $params['extension'],
                $params['contentId']
            )
        ];

        return $this->tpl->fetch('advertisement/helpers/safeframe/dfp.tpl', $params);
    }

    /**
     * Returns the HTML code for a Smart advertisement.
     *
     * @param \Advertisement $ad       The advertisement object.
     * @param string         $category The current category.
     *
     * @return string The HTML code for the Smart advertisement.
     */
    protected function renderSafeFrameSmart($ad, $params)
    {
        $config = $this->ds->get('smart_ad_server');
        $params = [
            'config'        => $config,
            'page_id'       => $config['page_id'][$params['advertisementGroup']],
            'format_id'     => (int) $ad->params['smart_format_id']
        ];

        return $this->tpl->fetch('advertisement/helpers/safeframe/smart.tpl', $params);
    }

    /**
     * Returns the HTML code for a flash-based advertisement.
     *
     * @param \Advertisement $ad  The advertisement object.
     * @param \Photo         $img The flash object.
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
            'url'    => $this->container->get('router')->generate('frontend_ad_redirect', [
                'id' => $publicId
            ])
        ];

        return $this->container->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/flash.tpl', $params);
    }

    /**
     * Returns the HTML code for a HTML/JS advertisement.
     *
     * @param \Advertisement $ad The advertisement object.
     *
     * @return string The HTML code for the HTML/JS advertisement.
     */
    protected function renderSafeFrameHtml($ad)
    {
        $tpl   = '<html><style>%s</style><body><div class="content">%s</div></body>';
        $html  = $this->getHtml($ad);
        $style = 'body { margin: 0; overflow: hidden; padding: 0; text-align:'
            . ' center; } img { max-width: 100% }';

        return sprintf($tpl, $style, $html);
    }

    /**
     * Returns the HTML code for an image-based advertisement.
     *
     * @param \Advertisement $ad  The advertisement object.
     * @param \Photo         $img The image object.
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
            'url'      => $this->container->get('router')->generate(
                'frontend_ad_redirect',
                [ 'id' => $publicId ]
            ),
        ];

        return $this->container->get('core.template.admin')
            ->fetch('advertisement/helpers/safeframe/image.tpl', $params);
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getDFPCustomCode()
    {
        $code = $this->ds->get('dfp_custom_code');

        if (empty($code)) {
            return '';
        }

        return base64_decode($code);
    }

    /**
     * Returns the targeting-related JS code for google DFP.
     *
     * @param string  $category  The current category.
     * @param string  $module    The current module.
     * @param integer $contentId The id of the content current.
     *
     * @return string The targeting-related JS code.
     */
    protected function getDFPTargeting($category, $module, $contentId)
    {
        $options = $this->ds->get('dfp_options');

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

        if (array_key_exists('content_id', $options)
            && !empty($options['content_id'])
            && !empty($contentId)
        ) {
            $targetingCode .=
                "googletag.pubads().setTargeting('{$options['content_id']}', ['{$contentId}']);\n";
        }

        return $targetingCode;
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getSmartCustomCode()
    {
        $code = $this->ds->get('smart_custom_code');

        if (empty($code)) {
            return '';
        }

        return base64_decode($code);
    }

    /**
     * Returns the targeting-related JS code for google DFP.
     *
     * @param string  $category The current category.
     * @param string  $module    The current module.
     * @param integer $contentId The id of the content current.
     *
     * @return string The targeting-related JS code.
     */
    protected function getSmartTargeting($category, $module, $contentId)
    {
        $config = $this->ds->get('smart_ad_server');

        $targetingCode = '';
        if (array_key_exists('category_targeting', $config)
            && !empty($config['category_targeting'])
            && !empty($category)
        ) {
            $targetingCode .= $config['category_targeting'] . '=' . $category . ';';
        }

        if (array_key_exists('module_targeting', $config)
            && !empty($config['module_targeting'])
            && !empty($module)
        ) {
            $targetingCode .= $config['module_targeting'] . '=' . $module . ';';
        }

        if (array_key_exists('url_targeting', $config)
            && !empty($config['url_targeting'])
            && !empty($contentId)
        ) {
            $targetingCode .= $config['url_targeting'] . '=' . $contentId . ';';
        }

        return $targetingCode;
    }

    /**
     * Returns the advertisement script.
     *
     * @param \Advertisement $ad The advertisement object.
     *
     * @return string The advertisement script.
     */
    protected function getHtml($ad)
    {
        if (empty($ad->script)) {
            $this->container->get('application.log')->info(
                'The advertisement ' . $ad->id . ' is empty'
            );
        }

        return $ad->script;
    }

    /**
     * Returns the image object for the advertisement.
     *
     * @param \Advertisement $ad The advertisement object.
     *
     * @return \Photo The image for the advertisement.
     */
    protected function getImage($ad)
    {
        if (empty($ad->img)) {
            $this->container->get('application.log')->info(
                'The advertisement photo for the ad (' . $ad->id . ') is empty'
            );

            return null;
        }

        try {
            return $this->container->get('entity_repository')
                ->find('Photo', $ad->img);
        } catch (\Exception $e) {
            $this->container->get('error.log')->error($e->getMessage());
        }

        return null;
    }
}
