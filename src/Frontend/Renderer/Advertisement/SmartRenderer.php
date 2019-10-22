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
 * The SmartRenderer class provides methods to generate the HTML code
 * for Smart advertisements.
 */
class SmartRenderer extends AdvertisementRenderer
{
    /**
     * Renders an advertisement.
     *
     * @param \Advertisement $ad The advertisement to render.
     * @param array          $params The list of parameters
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad, $params)
    {
        $config = $this->ds->get('smart_ad_server');

        $template = 'smart.slot.onecall_async.tpl';
        if (is_array($config)
            && array_key_exists('tags_format', $config)
        ) {
            $template = 'smart.slot.' . $config['tags_format'] . '.tpl';
        }

        return $this->tpl
            ->fetch('advertisement/helpers/inline/' . $template, [
                'config'        => $config,
                'id'            => $ad->params['smart_format_id'],
                'page_id'       => $config['page_id'][$params['advertisementGroup']],
                'rand'          => rand(),
                'targetingCode' => $this->getTargeting(
                    $params['category'],
                    $params['extension'],
                    $params['content']->id
                )
            ]);
    }

    /**
     * Renders a SafeFrame document for a Smart advertisement
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters
     *
     * @return string the HTML generated
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
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
     * Generates the HTML code to include in header for Smart advertisements.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters
     *
     * @return string The HTML code to include in header.
     */
    public function renderInlineHeader($ads, $params)
    {
        $config = $this->ds->get('smart_ad_server');
        $zones  = [];

        foreach ($ads as $ad) {
            $zones[] = [
                'id'        => $ad->id,
                'format_id' => (int) $ad->params['smart_format_id']
            ];
        }

        $template = 'smart.header.onecall_async.tpl';
        if (is_array($config)
            && array_key_exists('tags_format', $config)
        ) {
            $template = 'smart.header.' . $config['tags_format'] . '.tpl';
        }

        return $this->tpl
            ->fetch('advertisement/helpers/inline/' . $template, [
                'config'        => $config,
                'page_id'       => $config['page_id'][$params['advertisementGroup']],
                'zones'         => $zones,
                'customCode'    => $this->getCustomCode(),
                'targetingCode' => $this->getTargeting(
                    $params['category'],
                    $params['extension'],
                    $params['content']->id ?? null
                )
            ]);
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getCustomCode()
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
    protected function getTargeting($category, $module, $contentId)
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
}
