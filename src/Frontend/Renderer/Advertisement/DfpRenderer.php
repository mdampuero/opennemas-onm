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
 * The DfpRenderer class provides methods to generate the HTML code
 * for Dfp advertisements.
 */
class DfpRenderer extends AdvertisementRenderer
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
        $size      = $this->getDeviceAdvertisementSize($ad, 'phone');
        $targeting = $this->getTargeting(
            $params['category'],
            $params['extension'],
            $params['content']->id
        );

        $content = $this->tpl->fetch('advertisement/helpers/amp/dfp.tpl', [
            'dfpId'     => $ad->params['googledfp_unit_id'],
            'sizes'     => $this->getAmpMultiSizes($ad),
            'width'     => $size['width'],
            'height'    => $size['height'],
            'targeting' => !empty($targeting)
                ? json_encode([ 'targeting' => $targeting ])
                : null,
        ]);

        return $this->getSlot($ad, $content, false);
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

        return $this->tpl->fetch('advertisement/helpers/fia/dfp.tpl', [
            'id'      => $ad->id,
            'dfpId'   => $ad->params['googledfp_unit_id'],
            'sizes'   => $ad->getSizes($ad->normalizeSizes($ad->params)),
            'width'   => $size['width'],
            'height'  => $size['height'],
            'default' => $params['op-ad-default'] ?? null,
        ]);
    }

    /**
     * Renders a DFP advertisement slot.
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

        $content = $this->tpl->fetch('advertisement/helpers/inline/dfp.slot.tpl', [
            'id' => $ad->id
        ]);

        return $this->getSlot($ad, $content);
    }

    /**
     * Renders a SafeFrame document for a DFP advertisement.
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters.
     *
     * @return string The generated HTML.
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        $params = [
            'id'         => $ad->id,
            'dfpId'      => $ad->params['googledfp_unit_id'],
            'sizes'      => $ad->getSizes($ad->normalizeSizes($ad->params)),
            'customCode' => $this->getCustomCode(),
            'targeting'  => $this->getTargeting(
                $params['category'],
                $params['extension'],
                $params['contentId']
            )
        ];

        return $this->tpl->fetch('advertisement/helpers/safeframe/dfp.tpl', $params);
    }

    /**
     * Generates the HTML header section for the DFP ads.
     *
     * @param array  $ads    The list of advertisements to generate the header from.
     * @param array  $params The list of parameters.
     *
     * @return string the HTML content for the DFP slot.
     */
    public function renderInlineHeader($ads, $params)
    {
        $zones = [];
        foreach ($ads as $advertisement) {
            $zones[] = [
                'id'    => $advertisement->id,
                'dfpId' => $advertisement->params['googledfp_unit_id'],
                'sizes' => $advertisement->getSizes()
            ];
        }

        return $this->tpl->fetch('advertisement/helpers/inline/dfp.header.tpl', [
            'category'   => $params['category'],
            'extension'  => $params['extension'],
            'customCode' => $this->getCustomCode(),
            'options'    => $this->ds->get('dfp_options'),
            'zones'      => $zones,
            'targeting'  => $this->getTargeting(
                $params['category'],
                $params['extension'],
                $params['content']->id
            ),
        ]);
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getCustomCode()
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
    protected function getTargeting($category, $module, $contentId)
    {
        $options = $this->ds->get('dfp_options');
        $tags    = 'tags';

        if (!is_array($options)) {
            return '';
        }

        $module = $module === 'frontpages' ? 'home' : $module;

        $targetingMap = [];
        if (array_key_exists('target', $options) && !empty($options['target'])) {
            $targetingMap[$options['target']] = $category;
        }

        if (array_key_exists('module', $options) && !empty($options['module'])) {
            $targetingMap[$options['module']] = $module;
        }

        if (array_key_exists('tags', $options) && !empty($options['tags'])) {
            // Dividimos las etiquetas en un array. 'tags' aquí es la clave dentro de $options.
            $tagsArray = explode(', ', $options['tags']); // Divide las etiquetas separadas por coma y espacio.

            // Asignamos el array de etiquetas a la clave correspondiente en $targetingMap.
            $targetingMap['tags'] = $tagsArray; // 'tags' es la clave, y el valor es el array de etiquetas.
        }

        if (array_key_exists('content_id', $options)
            && !empty($options['content_id'])
            && !empty($contentId)
        ) {
            $targetingMap[$options['content_id']] = $contentId;
        }
        return $targetingMap;
    }

    /**
     * Returns the list of AMP multi-size for Google DFP.
     *
     * @param \Advertisement $ad The ad to render.
     *
     * @return string The list of AMP multi-size for Google DFP.
     */
    protected function getAmpMultiSizes(\Advertisement $ad)
    {
        $nomalizedSizes = $ad->normalizeSizes();

        $sizes = array_filter($nomalizedSizes, function ($a) {
            return !array_key_exists('device', $a);
        });

        $sizes = array_map(function ($a) {
            return "{$a['width']}x{$a['height']}";
        }, $sizes);

        return implode(',', $sizes);
    }
}
