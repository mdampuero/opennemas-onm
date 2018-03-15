<?php
/**
 * Smarty plugin for rendering a banner for a position.
 *
 * @param array    $params The list of parameters.
 * @param Template $tpl    The template object.
 */
function smarty_insert_renderbanner($params, $smarty)
{
    $safeframeEnabled = $smarty->getContainer()->get('setting_repository')
        ->get('ads_settings')['safe_frame'];

    $tpl    = '<div class="ad-slot oat%s">%s</div>';
    $class  = '" data-type="' . $params['type'];
    $format = 'safeframe';

    if (array_key_exists('render_params', $smarty->tpl_vars)
        && array_key_exists('ads-format', $smarty->tpl_vars['render_params']->value)
        && !empty($smarty->tpl_vars['render_params']->value['ads-format'])
    ) {
        $format = $smarty->tpl_vars['render_params']->value['ads-format'];
    }

    if (array_key_exists('format', $params) && !empty($params['format'])) {
        $format = $params['format'];
    }

    if ($safeframeEnabled && $format === 'safeframe') {
        return sprintf($tpl, $class, '');
    }

    $ads  = $smarty->tpl_vars['advertisements']->value;
    $type = $params['type'];

    if (!is_array($ads)) {
        return '';
    }

    $ads = array_filter($ads, function ($ad) use ($type) {
        return is_array($ad->positions)
            && in_array($type, $ad->positions)
            && $ad->isInTime();
    });

    if (empty($ads)) {
        return '';
    }

    $ad = $ads[array_rand($ads)];

    $renderer    = $smarty->getContainer()->get('core.renderer.advertisement');
    $orientation = empty($ad->params['orientation']) ?
        'top' : $ad->params['orientation'];

    $content = $renderer->renderInline($ad, $format);

    $class = ' oat-visible oat-' . $orientation . ' '
        . $renderer->getDeviceCssClasses($ad);

    return sprintf($tpl, $class, $content);
}
