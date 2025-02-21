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
    public function renderInlineHeader($params)
    {
        $config = $this->ds->get('traffective_config');

        return $this->tpl->fetch('advertisement/helpers/inline/traffective.tpl', [
            'config' => $config,
        ]);
    }
}
