<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.js_includes.pre01.php
 * Type:     outputfilter
 * Name:     js_includes
 * Purpose:  Handles all the js includes and print them into .
 * -------------------------------------------------------------
 */
function smarty_outputfilter_js_includes($output, $smarty)
{
    $manager = getService('core.service.assetic.javascript_manager');
    $bag     = getService('core.service.assetic.asset_bag');

    $assets = $manager->writeAssets($bag->getScripts());

    $scripts = '';
    if (!empty($assets)) {
        foreach ($assets as $asset) {
            $scripts .= "<script src='" . $asset . "'></script>";
        }
    }

    $scripts .= implode('', $bag->getLiteralScripts());
    $output   = str_replace('</body>', $scripts . '</body>', $output);

    return $output;
}
