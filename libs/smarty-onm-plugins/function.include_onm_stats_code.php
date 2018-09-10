<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function smarty_function_include_onm_stats_code($params, &$smarty)
{
    $output  = '';
    $request = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest();

    // Don't render script for previews or synchronized articles
    if (empty($request)
        || preg_match('@/preview$@', $request->getRequestUri())
        || preg_match('@^/ext@', $request->getRequestUri())
    ) {
        return $output;
    }

    $templateVars = $smarty->getTemplateVars();

    // Only if contentId is setted from controller the plugin will print something.
    if (array_key_exists('contentId', $templateVars)) {
        $contentId = $templateVars['contentId'];

        // Get the script tag for onm-stats jquery plugin
        $output .= smarty_function_script_tag(
            [ 'common' => 1, 'src' => '/onm/jquery.onm-stats.js' ],
            $smarty
        );

        // Print the call to the plugin with the proper contentId
        $output .=
            '<script>
                jQuery.onmStats({ content_id: \'' . $contentId . '\' });
            </script>';
    }

    return $output;
}
