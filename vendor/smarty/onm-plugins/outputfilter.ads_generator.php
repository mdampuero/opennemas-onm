<?php
use Onm\Settings as s;

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.ads_generator.php
 * Type:     output
 * Name:     ads_generator
 * Purpose:  Generates all the script tags for OpenX based ads.
 * -------------------------------------------------------------
 */
function smarty_outputfilter_ads_generator($output, Smarty_Internal_Template $smarty)
{
    if (is_array($smarty->parent->tpl_vars)
        && array_key_exists('advertisements', $smarty->parent->tpl_vars)
        && is_array($smarty->parent->tpl_vars['advertisements']->value)
    ) {
        $adsReviveConfs = s::get('revive_ad_server');

        $advertisements = $smarty->parent->tpl_vars['advertisements']->value;
        $actual_category  = $smarty->parent->tpl_vars['actual_category']->value;

        $reviveZonesInformation = $dfpZonesInformation = array();
        foreach ($advertisements as $advertisement) {
            if ($advertisement->with_script == 2) {
                $reviveZonesInformation []= " 'zone_{$advertisement->id}' : ".(int) $advertisement->params['openx_zone_id'];
            } elseif ($advertisement->with_script == 3) {
                $dfpZonesInformation []= "googletag.defineSlot('{$advertisement->params['googledfp_unit_id']}', [{$advertisement->params['width']}, {$advertisement->params['height']}], 'zone_{$advertisement->id}').addService(googletag.pubads());";
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

            $dfpOutput = '<script type="text/javascript">var googletag=googletag||{};googletag.cmd=googletag.cmd||[],function(){var a=document.createElement("script");a.async=!0,a.type="text/javascript";var b="https:"==document.location.protocol;a.src=(b?"https:":"http:")+"//www.googletagservices.com/tag/js/gpt.js";var c=document.getElementsByTagName("script")[0];c.parentNode.insertBefore(a,c)}();</script>';
            $dfpOutput .= "<script type='text/javascript'>\n"
                          ."googletag.cmd.push(function() {\n"
                          .implode("\n", $dfpZonesInformation)
                          ."\ngoogletag.pubads().enableSingleRequest();\n"
                          ."googletag.enableServices();\n"
                          ."});\n</script>";

            $output = str_replace('</head>', $dfpOutput.'</head>', $output);
        }

    }

    return $output;
}
