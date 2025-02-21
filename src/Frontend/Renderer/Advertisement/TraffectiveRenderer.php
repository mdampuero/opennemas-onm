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
     * Returns the HTML for AMP advertisements.
     *
     * @param \Advertisement $ad     The advertisement to render.
     * @param array          $params The list of parameters.
     *
     * @return string The HTML for the advertisement.
     */
    public function renderAmp(\Advertisement $ad, $params)
    {
        // TODO: Implement renderAmp() method.
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
        // TODO: Implement renderFia() method.
    }

    /**
     * Renders an advertisement.
     *
     * @param \Advertisement $ad     The advertisement to render.
     * @param array          $params The list of parameters.
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad, $params)
    {
        // TODO: Implement renderInline() method.
    }

    /**
     * Renders a SafeFrame document for a Smart advertisement.
     *
     * @param \Advertisement $ad     The ad to render.
     * @param array          $params The list of parameters.
     *
     * @return string The generated HTML.
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        // TODO: Implement renderSafeFrame() method.
    }

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
        // TODO: Implement renderInlineHeader() method.
    }

    /**
     * Returns the custom code for Google DFP.
     *
     * @return string The custom code for Google DFP.
     */
    protected function getCustomCode()
    {
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
    }
}
