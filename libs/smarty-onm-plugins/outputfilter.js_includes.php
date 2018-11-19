<?php
/**
 * Handles all the js includes and print them into .
 *
 * @param string $output
 * @param \Smarty $smarty
 *
 * @return string
 */
function smarty_outputfilter_js_includes($output, $smarty)
{
    $manager = getService('core.service.assetic.javascript_manager');
    $bag     = getService('core.service.assetic.asset_bag');
    $scripts = $bag->getScripts();

    $assets = [];
    foreach ($scripts as $name => $files) {
        $assets = array_merge(
            $assets,
            $manager->writeAssets($files, $bag->getFilters(), $name)
        );
    }

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
