<?php
/**
 * Smarty plugin for rendering a banner for a position.
 *
 * @param array    $params The list of parameters.
 * @param Template $tpl    The template object.
 */
function smarty_function_render_ad_slot($params, &$smarty)
{
    $request      = $smarty->getContainer()->get('request_stack')->getCurrentRequest();
    $isRestricted = $smarty->getContainer()->get('core.helper.advertisement')
        ->isRestricted($request->getUri());

    if ($isRestricted) {
        return '';
    }

    $position         = $params['position'];
    $safeframeEnabled = $smarty->getContainer()
        ->get('orm.manager')
        ->getDataSet('Settings', 'instance')
        ->get('ads_settings')['safe_frame'];

    $tpl      = '<div class="ad-slot oat%s"%s>%s</div>';
    $class    = '" data-position="' . $position;
    $mode     = '';
    $renderer = $smarty->getContainer()->get('frontend.renderer.advertisement');

    if (array_key_exists('mode', $params)) {
        $mode = ' data-mode="' . $params['mode'] . '"';
    }

    // Filter advertisement by position
    $adsPositions = $renderer->getPositions();
    if (empty($adsPositions) || !in_array($position, $adsPositions)) {
        return '';
    }

    $advertisement = $renderer->getAdvertisement($position, $params);
    if (empty($advertisement)) {
        return '';
    }

    $adsFormat = $smarty->getValue('ads_format');
    if ($safeframeEnabled && !in_array($adsFormat, $renderer->getInlineFormats())) {
        return sprintf($tpl, $class, $mode, '');
    }

    // Get targeting parameters for advertising renderers
    $app    = $smarty->getValue('app');
    $params = array_merge($params, [
        'category'           => $app['section'],
        'extension'          => $app['extension'],
        'advertisementGroup' => $app['advertisementGroup'],
        'content'            => $smarty->getValue('content'),
        'ads_format'         => $adsFormat ?? null,
    ]);

    return $renderer->render($advertisement, $params);
}
