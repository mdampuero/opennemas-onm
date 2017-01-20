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
    // Don't render any advertisement if module is not activated
    // Just render default onm ads from file
    // No DFP nor OpenX allowed
    if (!getService('core.security')->hasExtension('ADS_MANAGER')
        || !is_array($smarty->parent->tpl_vars)
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
            ->fetch('advertisement/helpers/js.tpl', [
                'category'  => $category,
                'lifetime'  => $settings['lifetime_cookie'],
                'positions' => implode(',', $positions),
                'time'      => time(),
                'url'       => getService('router')
                    ->generate('api_v1_advertisements_list')
            ]);

        $output = str_replace('</body>', $content.'</body>', $output);
    }

    return $output;
}
