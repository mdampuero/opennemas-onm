<?php
use \Common\Core\Component\Renderer\AdvertisementRenderer;

/**
 * Smarty plugin for rendering a banner for a position.
 *
 * @param array    $params The list of parameters.
 * @param Template $tpl    The template object.
 */
function smarty_insert_renderbanner($params, $smarty)
{
    $adsRenderer = getService('core.renderer.advertisement');

    $adsInline = true;
    if ($adsInline) {
        $type = $params['type'];
        $ads  = $smarty->tpl_vars['advertisements']->value;

        if (!is_array($ads)) {
            $ads = [];
        }

        // Filter advertisements by position
        $ads = array_filter(
            $ads,
            function ($ad) use ($type) {
                return $ad->type_advertisement == $type;
            }
        );

        // Render the advertisement content
        $content = '';
        if (count($ads) > 0) {
            // Pick one random advertisement from those available
            $selectedAd = $ads[array_rand($ads)];

            $adContent = $adsRenderer->render($selectedAd);

            $content =  sprintf('<div class="ad-slot oat">%s</div>', $adContent);
        }
    } else {
        $content = sprintf('<div class="ad-slot oat" data-type="%s"></div>', $params['type']);
    }

    return $content;
}
