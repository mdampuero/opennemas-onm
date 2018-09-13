<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * smarty_function_is_module_activated, Smarty plugin for render message board
 * <code>
 * {messageboard type="growl" clear="true"}
 * </code>
 *
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 *
 * @return null|string Return a HTML code of the message board
 */
function smarty_block_is_module_activated($params, $content, &$smarty, &$repeat)
{
    if ($repeat) {
        return;
    }

    $modules = explode(',', $params['name']);

    if (count($modules) > 1) {
        $returnContent = false;
        foreach ($modules as $module) {
            $returnContent = ($returnContent || getService('core.security')->hasExtension($module));
            if ($returnContent == true) {
                break;
            }
        }

        $output = ($returnContent) ? $content : "";
    } else {
        $output = (getService('core.security')->hasExtension($params['name'])) ? $content : "";
    }

    if (isset($params['deactivated']) && $params['deactivated'] == 1) {
        $output = (empty($output)) ? $content : "";
    }

    return $output;
}
