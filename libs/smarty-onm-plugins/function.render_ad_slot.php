<?php
/**
 * Smarty plugin for rendering a banner for a position.
 *
 * @param array    $params The list of parameters.
 * @param Template $tpl    The template object.
 */
function smarty_function_render_ad_slot($params, &$smarty)
{
    $position         = $params['position'];
    $safeframeEnabled = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('ads_settings')['safe_frame'];

    $tpl   = '<div class="ad-slot oat%s">%s</div>';
    $class = '" data-position="' . $position;

    // Filter advertisement by position
    $adsPositions = $smarty->getValue('ads_positions');
    if (is_array($adsPositions) && !in_array($position, $adsPositions)) {
        return '';
    }

    $ads = $smarty->getValue('advertisements');
    if (!is_array($ads)) {
        return '';
    }

    $ads = array_filter($ads, function ($ad) use ($position) {
        return is_array($ad->positions)
            && in_array($position, $ad->positions)
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

    $adsFormat = $smarty->getValue('ads_format');
    if ($safeframeEnabled && !in_array($adsFormat, ['amp', 'inline'])) {
        return sprintf($tpl, $class, '');
    }

    // Get targeting parameters for smart ajax format
    $app             = $smarty->getValue('app');
    $targetingParams = [
        'category'           => $smarty->getValue('actual_category'),
        'extension'          => $app['extension'],
        'advertisementGroup' => $app['advertisementGroup'],
        'content'            => $smarty->getValue('content'),
        'ads_format'         => $adsFormat ?? null,
    ];

    return $smarty->getContainer()->get('frontend.renderer.advertisement')
        ->render($ad, $targetingParams);
}
