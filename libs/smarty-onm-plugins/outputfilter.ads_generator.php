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

    $category  = $smarty->parent->tpl_vars['actual_category']->value;
    $positions = [];
    $settings  = getService('setting_repository')->get('ads_settings');

    if (!$settings['safeFrame']) {
        $adsRenderer    = getService('core.renderer.advertisement');
        $xtags          = $smarty->smarty->tpl_vars['x-tags']->value;
        $content        = $smarty->parent->tpl_vars['content']->value;

        $params = [
            'category'  => $category,
            'extension' => $app['extension'],
            'content'   => $content,
            'x-tags'    => $xtags,
        ];

        $reviveOutput = $adsRenderer->renderInlineReviveHeader($ads, $params);
        $dfpOutput    = $adsRenderer->renderInlineDFPHeader($ads, $params);
        $interstitial = $adsRenderer->renderInterstitial($ads, $params);

        $output = str_replace('</head>', $reviveOutput . '</head>', $output);
        $output = str_replace('</head>', $dfpOutput . '</head>', $output);
        $output = str_replace('</body>', $interstitial . '</body>', $output);
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
            'lifetime'  => $settings['lifetime_cookie'],
            'positions' => implode(',', $positions),
            'time'      => time(),
            'url'       => getService('router')
                ->generate('api_v1_advertisements_list')
        ]);

    $output = str_replace('</head>', $content . '</head>', $output);

    return $output;
}
