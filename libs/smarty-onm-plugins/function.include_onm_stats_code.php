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
    $request = $smarty->getContainer()->get('request_stack')
        ->getCurrentRequest();

    if (empty($request)
        || preg_match('@^/admin@', $request->getRequestUri())
        || preg_match('@/preview$@', $request->getRequestUri())
        || preg_match('@^/ext@', $request->getRequestUri())
        || !array_key_exists('contentId', $smarty->getTemplateVars())
    ) {
        return '';
    }

    $output = smarty_function_script_tag(
        [ 'common' => 1, 'src' => '/onm/jquery.onm-stats.js' ],
        $smarty
    );

    $output .= '<script>jQuery.onmStats({ content_id: \''
        . $smarty->getTemplateVars()['contentId']
        . '\' });</script>';

    return $output;
}
