<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer;

/**
 * The AdvertisementRenderer class defines common properties and methods for all
 * advertisement renderers.
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
     * The available inline formats.
     *
     * @var array
     */
    protected $inlineFormats = [ 'amp', 'fia', 'newsletter' ];

    /**
     * The available advertisement types.
     *
     * @var array
     */
    protected $types = [ 'Image', 'Html', 'Revive', 'Dfp', 'Smart' ];

    /**
     * Initializes the AdvertisementRenderer
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->router    = $this->container->get('router');
        $this->tpl       = $this->container->get('view')->get('backend');
        $this->instance  = $this->container->get('core.instance');
        $this->ds        = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance');
    }

    /**
     * Returns the list of CSS classes according to device restrictions.
     *
     * @param \Advertisement $ad The advertisement to get restrictions from.
     *
     * @return string The css classes to apply.
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
     * Returns the list of inline render formats.
     *
     * @return array The array of inline formats.
     */
    public function getInlineFormats()
    {
        return $this->inlineFormats;
    }

    /**
     * Returns the string that depicts the default mark shown alongside ads.
     *
     * @param \Advertisement $ad The advertisement object where to search for the mark.
     *
     * @return string The default mark for the advertisements.
     */
    public function getMark(\Advertisement $ad = null)
    {
        // If the mark for the advertisement is not empty then return it
        if (is_object($ad)
            && array_key_exists('mark_text', $ad->params)
            && !empty($ad->params['mark_text'])
        ) {
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
     * Renders an advertisement given the advertisement and parameters.
     *
     * @param \Advertisement $ad     The advertisement to render.
     * @param array          $params Array of parameters to render the ad.
     *
     * @return string The HTML content for the advertisement.
     */
    public function render(\Advertisement $ad, $params)
    {
        // Get renderer class and ad format
        $renderer  = $this->getRendererClass($ad->with_script);
        $adsFormat = $params['ads_format'] ?? null;

        // Check for safeframe
        $isSafeFrame = $this->ds->get('ads_settings')['safe_frame'];
        if ($isSafeFrame && !in_array($adsFormat, $this->inlineFormats)) {
            return array_key_exists('floating', $params)
                && $params['floating'] === true
                    ? $this->renderSafeFrameSlot($ad)
                    : $renderer->renderSafeFrame($ad, $params);
        }

        return $renderer->renderInline($ad, $params);
    }

    /**
     * Returns the generic headers HTML for inline Adservers advertisements.
     *
     * @param array $ads    Array of advertisements.
     * @param array $params Array of parameters to render the ad header.
     *
     * @return string The HTML content for the header advertisement.
     */
    public function renderInlineHeaders($ads, $params)
    {
        if (empty($ads)) {
            return '';
        }

        $headers = '';
        foreach (array_slice($this->types, -3) as $type) {
            $method   = 'render' . $type . 'Headers';
            $headers .= $this->{$method}($ads, $params);
        }

        return $headers;
    }

    /**
     * Selects and renders an interstitial from a list of advertisements.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters to render the ad header.
     *
     * @return string The HTML code for Interstitial .
     */
    public function renderInlineInterstitial($ads, $params)
    {
        if (empty($ads)) {
            return '';
        }

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

        $orientation = empty($ad->params['orientation'])
            ? 'top' : $ad->params['orientation'];

        $size = $this->getDeviceAdvertisementSize($ad, 'desktop');
        if (empty($size)) {
            return '';
        }

        $renderer = $this->getRendererClass($ad->with_script);

        return $this->tpl->fetch(
            'advertisement/helpers/inline/interstitial.tpl',
            [
                'size'        => $size,
                'orientation' => $orientation,
                'ad'          => $ad,
                'content'     => $renderer->renderInline($ad, $params)
            ]
        );
    }

    /**
     * Returns the advertisement width and height for a specific device.
     *
     * @param \Advertisement $ad     The advertisement to render.
     * @param string         $device The device to get sizes from.
     *
     * @return array Array with advertisement width and height.
     */
    protected function getDeviceAdvertisementSize($ad, $device)
    {
        $nomalizedSizes = $ad->normalizeSizes($ad->params);

        $sizes = array_filter($nomalizedSizes, function ($a) use ($device) {
            return $a['device'] === $device;
        });

        $size = array_shift($sizes);

        return $size;
    }

    /**
     * Returns the HTML for a safe frame ad slot
     *
     * @param int $type The ad script type.
     * 0 -> Image, 1 -> Html, 2 -> Revive, 3 -> DFP, 4 -> Smart.
     *
     * @return \AdvertisementRenderer The advertisement renderer object.
     */
    protected function getRendererClass($scriptType)
    {
        $class     = $this->types[$scriptType] . 'Renderer';
        $classPath = __NAMESPACE__ . '\\Advertisement\\' . $class;

        return new $classPath($this->container);
    }

    /**
     * Wraps an advertisement rendered content in ad-slot template.
     *
     * @param \Advertisement $ad      The advertisement to render.
     * @param string         $content The advertisement rendered content.
     *
     * @return string The advertisement rendered content wrapped in ad-slot template.
     */
    protected function getSlot($ad, $content)
    {
        $tpl  = '<div class="ad-slot oat oat-visible oat-%s %s" data-mark="%s">%s</div>';
        $mark = $this->getMark($ad);

        $deviceClasses = $this->getDeviceCSSClasses($ad);
        $orientation   = empty($ad->params['orientation']) ?
            'top' : $ad->params['orientation'];

        return sprintf($tpl, $orientation, $deviceClasses, $mark, $content);
    }

    /**
     * Returns the HTML header section for the DFP ads.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters to render the ad header.
     *
     * @return string HTML for the DFP header.
     */
    protected function renderDfpHeaders($ads, $params)
    {
        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 3
                && array_key_exists('googledfp_unit_id', $a->params)
                && !empty($a->params['googledfp_unit_id']);
        });

        return !empty($ads)
            ? $this->getRendererClass(3)->renderInlineHeader($ads, $params)
            : '';
    }

    /**
     * Returns the HTML header section for the Revive ads.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters to render the ad header.
     *
     * @return string HTML for the Revive header.
     */
    protected function renderReviveHeaders($ads, $params)
    {
        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 2
                && array_key_exists('openx_zone_id', $a->params)
                && !empty($a->params['openx_zone_id']);
        });

        return !empty($ads)
            ? $this->getRendererClass(2)->renderInlineHeader($ads, $params)
            : '';
    }

    /**
     * Returns the HTML for a safe frame ad slot.
     *
     * @param \Advertisement $ad The ad to render.
     *
     * @return string The HTML generated.
     */
    protected function renderSafeFrameSlot(\Advertisement $ad)
    {
        $html = '<div class="ad-slot oat" data-id="%s" data-type="%s"></div>';
        $id   = $ad->id;
        $type = 37; // Floating banner type

        return sprintf($html, $id, $type);
    }

    /**
     * Returns the HTML header section for the Smart ads.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters to render the ad header.
     *
     * @return string HTML for the Smart header.
     */
    protected function renderSmartHeaders($ads, $params)
    {
        $ads = array_filter($ads, function ($a) {
            return $a->with_script == 4
                && array_key_exists('smart_format_id', $a->params)
                && !empty($a->params['smart_format_id']);
        });

        return !empty($ads)
            ? $this->getRendererClass(4)->renderInlineHeader($ads, $params)
            : '';
    }
}
