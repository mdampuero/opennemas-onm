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

    $forceNotSafeframe = (array_key_exists('format',$params) && $params['format'] !== 'safeframe');

    if ($safeFrame && !$forceNotSafeframe) {
        return sprintf($tpl, $class, '');
    }

    $renderer = getService('core.renderer.advertisement');
    $type     = $params['type'];
    $ads      = $smarty->tpl_vars['advertisements']->value;

    if (!is_array($ads)) {
        $ads = [];
    }

    $ads = array_filter($ads, function ($ad) use ($type) {
        return $ad->type_advertisement == $type && $ad->isInTime();
    });

    // Render the advertisement content
    $content = '';

    if (count($ads) > 0) {
        $ad = $ads[array_rand($ads)];

        $orientation = empty($ad->params['orientation']) ?
            'top' : $ad->params['orientation'];

        $format = array_key_exists('format',$params) ? $params['format'] : 'inline';
        $deviceClasses = $renderer->getDeviceCSSClases($ad);
        $class   = ' oat-visible oat-' . $orientation . ' ' . $deviceClasses;
        $content = $renderer->renderInline($ad, $format);
        $content = sprintf($tpl, $class, $content);
    }

    return $content;
}
