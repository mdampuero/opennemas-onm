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
 * The HtmlRenderer class provides methods to generate the HTML code
 * for Html advertisements.
 */
class HtmlRenderer extends AdvertisementRenderer
{
    /**
     * Renders an inline HTML/JS advertisement.
     *
     * @param \Advertisement $ad The advertisement to render.
     * @param array          $params The list of parameters
     *
     * @return string The HTML for the slot.
     */
    public function renderInline(\Advertisement $ad, $params)
    {
        return $this->getHtml($ad);
    }

    /**
     * Renders a SafeFrame document for an HTML/JS advertisement
     *
     * @param \Advertisement $ad The advertisement to render.
     * @param array          $params The list of parameters
     *
     * @return string the HTML generated
     */
    public function renderSafeFrame(\Advertisement $ad, $params)
    {
        $tpl   = '<html><style>%s</style><body><div class="content">%s</div></body>';
        $html  = $this->getHtml($ad);
        $style = 'body { margin: 0; overflow: hidden; padding: 0; text-align:'
            . ' center; } img { max-width: 100% }';

        return sprintf($tpl, $style, $html);
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
}
