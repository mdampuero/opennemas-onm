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
    if (!getService('core.security')->hasExtension('ADS_MANAGER')) {
        return $output;
    }

    if (is_array($smarty->parent->tpl_vars)
        && array_key_exists('advertisements', $smarty->parent->tpl_vars)
        && is_array($smarty->parent->tpl_vars['advertisements']->value)
    ) {
        $adsReviveConfs = getService('setting_repository')->get('revive_ad_server');

        $advertisements = $smarty->parent->tpl_vars['advertisements']->value;
        $actual_category  = $smarty->parent->tpl_vars['actual_category']->value;

        $reviveZonesInformation = $dfpZonesInformation = array();
        foreach ($advertisements as $advertisement) {
            if ($advertisement->with_script == 2
                && array_key_exists('openx_zone_id', $advertisement->params)
                && !empty($advertisement->params['openx_zone_id'])
            ) {
                $reviveZonesInformation []= " 'zone_{$advertisement->id}' : ".(int) $advertisement->params['openx_zone_id'];
            } elseif ($advertisement->with_script == 3
                && array_key_exists('googledfp_unit_id', $advertisement->params)
                && !empty($advertisement->params['googledfp_unit_id'])
            ) {
                if (is_array($advertisement->params['width'])
                    && is_array($advertisement->params['height'])
                ) {
                    $sizes = "[";
                    $comma = '';
                    foreach ($advertisement->params['width'] as $key => $value) {
                        $sizes .= $comma."[".$value.",".$advertisement->params['height'][$key]."]";
                        $comma = ', ';
                    }
                    $sizes .= "]";
                } else {
                    $sizes = " [{$advertisement->params['width']}, {$advertisement->params['height']}]";
                }
                $dfpZonesInformation []= "googletag.defineSlot('{$advertisement->params['googledfp_unit_id']}',".$sizes.
                    ", 'zone_{$advertisement->id}').addService(googletag.pubads());";
            }
        }

        // Generate revive ads positions
        if (count($reviveZonesInformation) > 0 && count($adsReviveConfs) > 0) {
            $reviveAdsPositions = "\n<script type='text/javascript'><!--// <![CDATA[
var OA_zones = { \n".implode(",\n", $reviveZonesInformation)."\n}
// ]]> --></script>
<script type='text/javascript' src='{$adsReviveConfs['url']}/www/delivery/spcjs.php?cat_name={$actual_category}'></script>";

            $output = str_replace('</head>', $reviveAdsPositions.'</head>', $output);
        }

        if (count($dfpZonesInformation) > 0) {
            // Check if targeting is set
            $dfpOptions = getService('setting_repository')->get('dfp_options');
            $targetingCode = '';
            if (is_array($dfpOptions) &&
                array_key_exists('target', $dfpOptions) &&
                !empty($dfpOptions['target'])
            ) {
                $targetingCode = "\ngoogletag.pubads().setTargeting('".$dfpOptions['target']."', ['".$actual_category."']);";
            }
            if (is_array($dfpOptions) &&
                array_key_exists('module', $dfpOptions) &&
                !empty($dfpOptions['module'])
            ) {
                $content = $smarty->parent->tpl_vars['content']->value;
                $module = '';
                if (!is_null($content)) {
                    $module = $content->content_type_name;
                } elseif ($smarty->smarty->tpl_vars['x-tags']->value) {
                    $xTags = $smarty->smarty->tpl_vars['x-tags']->value;
                    $module = ($xTags == 'frontpage-page,home') ? 'home' : strtok($xTags, ',');
                } elseif (!empty($smarty->smarty->tpl_vars['polls']->value)) {
                    $module = 'poll-frontpage';
                }
                $targetingCode .= "\ngoogletag.pubads().setTargeting('".$dfpOptions['module']."', ['".$module."']);";
            }
            // Check for custom code
            $dfpCustomCode = getService('setting_repository')->get('dfp_custom_code');
            $customCode = '';
            if (!empty($dfpCustomCode)
            ) {
                $customCode = "\n".base64_decode($dfpCustomCode);
            }

            $dfpOutput = "<script async='async' src='https://www.googletagservices.com/tag/js/gpt.js'></script>\n"
                ."<script>\n"
                ."var googletag = googletag || {};\n"
                ."googletag.cmd = googletag.cmd || [];\n"
                ."</script>\n";
            $dfpOutput .= "<script type='text/javascript'>\n"
                          ."googletag.cmd.push(function() {\n"
                          .implode("\n", $dfpZonesInformation)
                          .$targetingCode
                          .$customCode
                          ."\ngoogletag.pubads().enableSingleRequest();\n"
                          ."googletag.pubads().collapseEmptyDivs();\n"
                          ."googletag.enableServices();\n"
                          ."});\n</script>";

            $output = str_replace('</head>', $dfpOutput.'</head>', $output);
        }
    }

    return $output;
}
