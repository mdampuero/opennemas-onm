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
function smarty_outputfilter_css_includes($output, $smarty)
{
    $manager = getService('core.service.assetic.stylesheet_manager');
    $bag     = getService('core.service.assetic.asset_bag');

    $assets = $manager->writeAssets($bag->getStyles());

    $styles = '';
    if (!empty($assets)) {
        foreach ($assets as $asset) {
            $styles .= "<link rel='stylesheet' type='text/css' href='" . $asset . "'>";
        }
    }

    $styles .= implode('', $bag->getLiteralStyles());
    $output  = str_replace('</head>', $styles . '</head>', $output);

    return $output;
}
