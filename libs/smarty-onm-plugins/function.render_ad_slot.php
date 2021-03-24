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

    $tpl      = '<div class="ad-slot oat%s">%s</div>';
    $class    = '" data-position="' . $position;
    $renderer = $smarty->getContainer()->get('frontend.renderer.advertisement');

    // Filter advertisement by position
    $adsPositions = $renderer->getPositions('ads_positions');
    if (empty($adsPositions) || !in_array($position, $adsPositions)) {
        return '';
    }

    $ads = $renderer->getAdvertisements();
    if (!is_array($ads)) {
        return '';
    }

    $contentHelper = $smarty->getContainer()->get('core.helper.content');

    $ads = array_filter($ads, function ($ad) use ($contentHelper, $position) {
        return is_array($ad->positions)
            && in_array($position, $ad->positions)
            && $contentHelper->isInTime($ad);
    });

    if (empty($ads)) {
        return '';
    }

    $ad = $ads[array_rand($ads)];
    if (array_key_exists('mode', $params) && $params['mode'] === 'consume') {
        $ad = array_pop($ads);

        $renderer->setAdvertisements($ads);
    }

    $adsFormat = $smarty->getValue('ads_format');
    if ($safeframeEnabled && !in_array($adsFormat, $renderer->getInlineFormats())) {
        return sprintf($tpl, $class, '');
    }

    // Get targeting parameters for advertising renderers
    $app    = $smarty->getValue('app');
    $params = array_merge($params, [
        'category'           => $smarty->getValue('actual_category'),
        'extension'          => $app['extension'],
        'advertisementGroup' => $app['advertisementGroup'],
        'content'            => $smarty->getValue('content'),
        'ads_format'         => $adsFormat ?? null,
    ]);

    return $renderer->render($ad, $params);
}
