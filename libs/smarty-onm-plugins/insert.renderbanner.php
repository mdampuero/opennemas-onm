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
    $safeFrame = getService('setting_repository')
        ->get('ads_settings')['safe_frame'];

    $tpl   = '<div class="ad-slot oat%s">%s</div>';
    $class = '" data-type="' . $params['type'];

    if ($safeFrame) {
        return sprintf($tpl, $class, '');
    }

    $class    = ' oat-visible';
    $renderer = getService('core.renderer.advertisement');
    $type     = $params['type'];
    $ads      = $smarty->tpl_vars['advertisements']->value;

    if (!is_array($ads)) {
        $ads = [];
    }

    $ads = array_filter($ads, function ($ad) use ($type) {
        return $ad->type_advertisement == $type;
    });

    // Render the advertisement content
    $content = '';

    if (count($ads) > 0) {
        $selectedAd = $ads[array_rand($ads)];
        $content    = $renderer->render($selectedAd);
        $content    = sprintf($tpl, $class, $content);
    }

    return $content;
}
