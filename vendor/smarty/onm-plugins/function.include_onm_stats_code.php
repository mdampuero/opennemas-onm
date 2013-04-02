<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
use \Onm\Settings as s;

function smarty_function_include_onm_stats_code($params, &$smarty)
{
    $output = "";

    // If comes from preview, don't render script
    if (preg_match('@/admin/frontpages@', $_SERVER['HTTP_REFERER'])) {
        return $output;
    }

    $templateVars = $smarty->getTemplateVars();

    // Only if contentId is setted from controller the plugin will print something.
    if (array_key_exists('contentId', $templateVars)) {
        $contentId = $templateVars['contentId'];

        // Get the script tag for onm-stats jquery plugin
        $output .= smarty_function_script_tag(
            array('common' => 1, 'src' => '/onm/jquery.onm-stats.js'),
            $smarty
        );

        // Print the call to the plugin with the proper contentId
        $output .=
            '<script type="text/javascript">
                jQuery.onmStats({ content_id: \''.$contentId.'\' });;
            </script>';
    }

    return $output;
}
