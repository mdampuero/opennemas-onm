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
class TraffectiveRenderer extends AdvertisementRenderer
{
    /**
     * Generates the HTML code to include in header for Smart advertisements.
     *
     * @param array $ads    The list of advertisements.
     * @param array $params The list of parameters.
     *
     * @return string The HTML code to include in header.
     */
    public function renderInlineHeader($ads, $params)
    {
        $config = $this->ds->get('traffective_config');

        $config['ads']     = !empty($config['ads']) && $config['ads'] == 1;
        $config['progAds'] = !empty($config['progAds']) && $config['progAds'] == 1;

        if (empty($config['domain'])) {
            return '';
        }

        return $this->tpl->fetch('advertisement/helpers/inline/traffective.tpl', [
            'config' => $config,
            'targeting' => $this->getTargeting(
                $params['category'],
                $params['extension']
            ),
        ]);
    }

    /**
     * Returns the targeting-related JS code for google DFP.
     *
     * @param string  $category  The current category.
     * @param string  $module    The current module.
     *
     * @return string The targeting-related JS code.
     */
    protected function getTargeting($category, $module)
    {
        $targeting = [];

        if (!empty($category)) {
            $targeting['category'] = ($category === 'home') ?
                'homepage' : $category;
        }

        if (!empty($module)) {
            $targeting['extension'] = $module;
        }

        return $targeting;
    }
}
