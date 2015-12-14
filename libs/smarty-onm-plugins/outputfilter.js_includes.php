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
function smarty_outputfilter_js_includes($output, Smarty_Internal_Template $smarty)
{
    $am = getService('javascript_manager');

    $am->initFilters();
    $am->initFactory();
    $assets = $am->writeAssets();

    if (!empty($am->assets)) {
        foreach ($assets as $asset) {
            $scripts .= "<script src='" . $asset . "'></script>";
        }
    }

    $scripts .= $am->literal;
    $output = str_replace('</body>', $scripts.'</body>', $output);

    return $output;
}
