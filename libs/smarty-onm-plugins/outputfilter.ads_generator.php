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

    $safeFrame = getService('setting_repository')->get('ads_settings')['safe_frame'];
    if (!$safeFrame) {
        $adsRenderer    = getService('core.renderer.advertisement');
        $actualCategory = $smarty->parent->tpl_vars['actual_category']->value;
        $xtags          = $smarty->smarty->tpl_vars['x-tags']->value;
        $content        = $smarty->parent->tpl_vars['content']->value;

        $params = [
            'category'  => $actualCategory,
            'extension' => $app['extension'],
            'content'   => $content,
            'x-tags'    => $xtags,
        ];

        $reviveOutput = $adsRenderer->renderInlineReviveHeader($ads, $params);
        if ($reviveOutput) {
            $output = str_replace('</head>', $reviveOutput.'</head>', $output);
        }

        $dfpOutput = $adsRenderer->renderInlineDFPHeader($ads, $params);
        if (!empty($dfpOutput)) {
            $output = str_replace('</head>', $dfpOutput.'</head>', $output);
        }
    } else {
        // Don't render any advertisement if module is not activated
        // Just render default onm ads from file
        // No DFP nor OpenX allowed
        if (!is_array($smarty->parent->tpl_vars)
            || !array_key_exists('ads_positions', $smarty->parent->tpl_vars)
            || !is_array($smarty->parent->tpl_vars['ads_positions']->value)
        ) {
            return $output;
        }

        $category  = $smarty->parent->tpl_vars['actual_category']->value;
        $settings  = getService('setting_repository')->get('ads_settings');
        $positions = is_object($smarty->parent->tpl_vars['ads_positions']) ?
            $smarty->parent->tpl_vars['ads_positions']->value : [];

        if (count($positions) > 0) {
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
        }
    }

    return $output;
}
