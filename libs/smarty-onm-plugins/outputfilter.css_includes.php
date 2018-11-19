<?php
/**
 * Handles all the css includes and print them into .
 *
 * @param array $params the list of parameters
 * @param \Smarty $smarty the smarty instance
 *
 * @return string
 */
function smarty_outputfilter_css_includes($output, $smarty)
{
    $manager = getService('core.service.assetic.stylesheet_manager');
    $bag     = getService('core.service.assetic.asset_bag');
    $styles  = $bag->getStyles();

    $assets = [];
    foreach ($styles as $name => $files) {
        $assets = array_merge(
            $assets,
            $manager->writeAssets($files, $bag->getFilters(), $name)
        );
    }

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
