<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Renderer\Advertisement;

use Frontend\Renderer\AdvertisementRenderer;

/**
 * The ReviveRenderer class provides methods to generate the HTML code
 * for Revive advertisements.
 */
class ReviveRenderer extends AdvertisementRenderer
{
    /**
     * Returns the HTML for AMP advertisements.
     *
     * @param \Advertisement $ad The advertisement to render.
     *
     * @return string The HTML for the advertisement.
     */
    public function renderAmp($ad, $params)
    {
        $size = $this->getDeviceAdvertisementSize($ad, 'phone');

        return $this->tpl->fetch('advertisement/helpers/amp/revive.tpl', [
            'openXId'  => $ad->params['openx_zone_id'],
            'url'      => $this->ds->get('revive_ad_server')['url'],
            'width'    => $size['width'],
            'height'   => $size['height'],
        ]);
    }

    /**
     * Returns the HTML for instant articles advertisements.
     *
     * @param \Advertisement $ad The advertisement to render.
     *
     * @return string The HTML for the advertisement.
     */
    public function renderFia($ad, $params)
    {
        $size = $this->getDeviceAdvertisementSize($ad, 'phone');

        return $this->tpl->fetch('advertisement/helpers/fia/revive.tpl', [
            'id'       => $ad->id,
            'category' => $params['category'],
            'openXId'  => $ad->params['openx_zone_id'],
            'url'      => $this->ds->get('revive_ad_server')['url'],
            'width'    => $size['width'],
            'height'   => $size['height'],
            'default'  => $params['op-ad-default'] ?? null,
        ]);
    }

    /**
     * Renders an inline Revive advertisement.
     *
     * @param \Advertisement $ad     The advertisement to render.
     * @param array          $params The list of parameters.
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad, $params)
    {
        $format = $params['ads_format'] ?? null;
        if ($format === 'fia') {
            return $this->renderFia($ad, $params);
        }

        if ($format === 'amp') {
            return $this->renderAmp($ad, $params);
        }

        $iframe = in_array($ad->positions, [ 50, 150, 250, 350, 450, 550 ]);
        $url    = $this->container->get('router')->generate(
            'api_v1_advertisement_show',
            [ 'id' => $ad->id ]
        );

        $content = $this->tpl->fetch(
            'advertisement/helpers/inline/revive.slot.tpl',
            [
                'id'     => $ad->id,
                'iframe' => $iframe,
                'url'    => $url,
            ]
        );

        return $this->getSlot($ad, $content);
    }

    /**
     * Renders a SafeFrame document for a Revive advertisement.
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters.
     *
     * @return string The generated HTML.
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        $params = [
            'id'        => $ad->id,
            'category'  => $params['category'],
            'extension' => $params['extension'],
            'openXId'   => $ad->params['openx_zone_id'],
            'url'       => $this->ds->get('revive_ad_server')['url']
        ];

        return $this->tpl->fetch('advertisement/helpers/safeframe/revive.tpl', $params);
    }

    /**
     * Generates the HTML code to include in header for Revive advertisements.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters.
     *
     * @return string The HTML code to include in header.
     */
    public function renderInlineHeader($ads, $params)
    {
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
}
