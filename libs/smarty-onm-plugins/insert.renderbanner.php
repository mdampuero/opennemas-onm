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
    $safeFrameSettingEnabled = getService('setting_repository')
        ->get('ads_settings')['safe_frame'];

    $tpl   = '<div class="ad-slot oat%s">%s</div>';
    $class = '" data-type="' . $params['type'];

    // Allow to force the rendering format != safeframe by passing the
    // format=XXX parameter to the plugin or by using a template variable
    // render_params['ads-format']
    $formatFromParam = array_key_exists('format',$params) ? $params['format'] : null;
    $formatFromTemplateVar = (
            array_key_exists('render_params', $smarty->tpl_vars)
            && array_key_exists('ads-format', $smarty->tpl_vars['render_params']->value)
            && $smarty->tpl_vars['render_params']->value['ads-format']
        ) ? $smarty->tpl_vars['render_params']->value['ads-format']
        : null;

    $avoidSafeframe = ($formatFromParam !== null && $formatFromTemplateVar !== null)
        || (!is_null($formatFromParam) && $formatFromParam !== 'safeframe')
        || (!is_null($formatFromTemplateVar) && $formatFromTemplateVar !== 'safeframe');

    if ($safeFrameSettingEnabled && !$avoidSafeframe) {
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

        $format = $formatFromTemplateVar;
        if ($formatFromParam != $formatFromTemplateVar
            && $formatFromParam != null
        ) {
            $format = $formatFromParam;
        }
        $format = empty($format) ? 'inline' : $format;

        $deviceClasses = $renderer->getDeviceCSSClases($ad);
        $class   = ' oat-visible oat-' . $orientation . ' ' . $deviceClasses;
        $content = $renderer->renderInline($ad, $format);
        $content = sprintf($tpl, $class, $content);
    }

    return $content;
}
