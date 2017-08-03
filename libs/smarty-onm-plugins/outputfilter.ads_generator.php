<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.ads_generator.php
 * Type:     output
 * Name:     ads_generator
 * Purpose:  Generates all the script tags for OpenX based ads.
 * -------------------------------------------------------------
 */
function smarty_outputfilter_ads_generator($output, $smarty)
{
    $ads = $smarty->parent->tpl_vars['advertisements']->value;
    $app = $smarty->parent->tpl_vars['app']->value;

    if (count($ads) <= 0) {
        return $output;
    }

    // Do not render the ads headers for AMP pages.
    $formatFromTemplateVar = (
            array_key_exists('render_params', $smarty->tpl_vars)
            && array_key_exists('ads-format', $smarty->tpl_vars['render_params']->value)
            && $smarty->tpl_vars['render_params']->value['ads-format']
        ) ? $smarty->tpl_vars['render_params']->value['ads-format']
        : null;

    if ($formatFromTemplateVar == 'amp') {
        return $output;
    }

    $category  = $smarty->parent->tpl_vars['actual_category']->value;
    $content   = $smarty->parent->tpl_vars['content']->value;
    $dirtyId   = rtrim(basename($content->uri), '.html');
    $positions = [];
    $settings  = getService('setting_repository')->get('ads_settings');
    $safeFrameEnabled = getService('core.helper.advertisement')->isSafeFrameEnabled();

    if (!$safeFrameEnabled) {
        $adsRenderer    = getService('core.renderer.advertisement');
        $xtags          = $smarty->smarty->tpl_vars['x-tags']->value;

        $ads = array_filter($ads, function ($a) {
            return $a->isInTime();
        });

        $params = [
            'category'  => $category,
            'extension' => $app['extension'],
            'dirtyId'   => $dirtyId,
            'content'   => $content,
            'x-tags'    => $xtags,
        ];

        $reviveOutput = $adsRenderer->renderInlineReviveHeader($ads, $params);
        $dfpOutput    = $adsRenderer->renderInlineDFPHeader($ads, $params);
        $interstitial = $adsRenderer->renderInlineInterstitial($ads, $params);
        $devices      = getService('core.template.admin')
            ->fetch('advertisement/helpers/inline/js.tpl');

        $devices = "\n" . str_replace("\n", ' ', $devices);

        $output = str_replace('</head>', $reviveOutput . '</head>', $output);
        $output = str_replace('</head>', $dfpOutput . '</head>', $output);
        $output = str_replace('</body>', $interstitial . '</body>', $output);
        $output = str_replace('</body>', $devices . '</body>', $output);
    } else {
        // No advertisements
        if (!array_key_exists('ads_positions', $smarty->parent->tpl_vars)
            || !is_array($smarty->parent->tpl_vars['ads_positions']->value)
        ) {
            return $output;
        }

        $positions = is_object($smarty->parent->tpl_vars['ads_positions']) ?
            $smarty->parent->tpl_vars['ads_positions']->value : [];
    }

    $content = getService('core.template.admin')
        ->fetch('advertisement/helpers/safeframe/js.tpl', [
            'debug'     => $app['environment'] === 'dev' ? 'true' : 'false',
            'category'  => $category,
            'extension' => $app['extension'],
            'dirtyId'   => $dirtyId,
            'lifetime'  => $settings['lifetime_cookie'],
            'positions' => implode(',', $positions),
            'time'      => time(),
            'url'       => getService('router')
                ->generate('api_v1_advertisements_list')
        ]);

    $output = str_replace('</head>', $content . '</head>', $output);

    return $output;
}
