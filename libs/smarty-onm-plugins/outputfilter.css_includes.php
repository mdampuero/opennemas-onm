<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     output.filter.css_includes.pre01.php
 * Type:     outputfilter
 * Name:     css_includes
 * Purpose:  Handles all the css includes and print them into .
 * -------------------------------------------------------------
 */
function smarty_outputfilter_css_includes($output, Smarty_Internal_Template $smarty)
{
    $am = getService('stylesheet_manager');

    $am->initFilters();
    $am->initFactory();
    $assets = $am->writeAssets();

    if (!empty($assets)) {
        foreach ($assets as $asset) {
            $styles .= "<link rel='stylesheet' type='text/css' href='" . $asset . "'>";
        }

        $styles .= $am->literal;
        $output = str_replace('</head>', $styles.'</head>', $output);
    }

    return $output;
}
