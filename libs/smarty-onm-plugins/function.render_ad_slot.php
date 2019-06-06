<?php
/**
 * Smarty plugin for rendering a banner for a position.
 *
 * @param array    $params The list of parameters.
 * @param Template $tpl    The template object.
 */
function smarty_function_render_ad_slot($params, &$smarty)
{
    $safeframeEnabled = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('ads_settings')['safe_frame'];

    $tpl    = '<div class="ad-slot oat%s">%s</div>';
    $class  = '" data-position="' . $params['position'];
    $format = 'safeframe';

    // Filter advertisement by position
    $adsPositions = $smarty->getValue('ads_positions');
    if (is_array($adsPositions)
        && !in_array($params['position'], $adsPositions)) {
        return '';
    }

    // Get format from template
    $renderParams = $smarty->getValue('render_params');
    if (is_array($renderParams)
        && array_key_exists('ads-format', $renderParams)
        && !empty($renderParams['ads-format'])
    ) {
        $format = $renderParams['ads-format'];
    }

    if (array_key_exists('format', $params) && !empty($params['format'])) {
        $format = $params['format'];
    }

    // Need to use this smarty method due to consume mode (get tpl vars by ref)
    $ads    = $smarty->getValue('advertisements');
    $slotId = $params['position'];

    if (!is_array($ads)) {
        return '';
    }

    $ads = array_filter($ads, function ($ad) use ($slotId) {
        return is_array($ad->positions)
            && in_array($slotId, $ad->positions)
            && $ad->isInTime();
    });

    if (empty($ads)) {
        return '';
    }

    $ad = $ads[array_rand($ads)];
    if (array_key_exists('mode', $params) && $params['mode'] === 'consume') {
        $ad = array_pop($ads);

        $smarty->setValue('advertisements', $ads);
    }

    if ($safeframeEnabled && $format === 'safeframe') {
        return sprintf($tpl, $class, '');
    }

    // Get targeting parameters for smart ajax format
    $app             = $smarty->getValue('app');
    $targetingParams = [
        'category'           => $smarty->getValue('actual_category'),
        'extension'          => $app['extension'],
        'advertisementGroup' => $app['advertisementGroup'],
        'content'            => $smarty->getValue('content')
    ];

    $renderer    = $smarty->getContainer()->get('core.renderer.advertisement');
    $adOutput    = $renderer->renderInline($ad, $format, $targetingParams);
    $orientation = empty($ad->params['orientation']) ? 'top' : $ad->params['orientation'];

    $class = ' oat-visible oat-' . $orientation . ' ' . $renderer->getDeviceCssClasses($ad);
    $mark  = $renderer->getMark($ad);
    if (!empty($mark)) {
        $class .= '" data-mark="' . $mark . '';
    }

    return sprintf($tpl, $class, $adOutput);
}
