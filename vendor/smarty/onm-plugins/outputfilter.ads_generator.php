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
        $adsOpenXconfs = s::get('revive_ad_server');
        $advertisements = $smarty->parent->tpl_vars['advertisements']->value;

        $zonesInformation = array();
        foreach ($advertisements as $advertisement) {
            if ($advertisement->with_script == 2) {
                $zonesInformation []= " 'zone_{$advertisement->type_advertisement}' : ".(int) $advertisement->params['openx_zone_id'];
            }
        }

        if (count($zonesInformation) > 0) {
            $adsPositions = "\n<script type='text/javascript'><!--// <![CDATA[
var OA_zones = { \n".implode(",\n", $zonesInformation)."\n}
// ]]> --></script>
<script type='text/javascript' src='{$adsOpenXconfs['url']}/www/delivery/spcjs.php?id_sec={$adsOpenXconfs['site_id']}'></script>";

            $output = str_replace('</head>', $adsPositions.'</head>', $output);
        }
    }

    return $output;
}
