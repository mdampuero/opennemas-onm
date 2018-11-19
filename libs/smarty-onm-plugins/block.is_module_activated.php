<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Prints the content if the module is enabled
 *
 * @param array   $params The list of parameters passed to the block.
 * @param string  $content The content inside the block.
 * @param \Smarty $smarty The instance of smarty.
 * @param boolean $open Whether if we are in the open of the tag of in the close.
 *
 * @return null|string
 */
function smarty_block_is_module_activated($params, $content, &$smarty, &$repeat)
{
    if ($repeat) {
        return;
    }

    if (!isset($params['name'])) {
        $output = '';
    }

    if (!isset($params['name'])) {
        $output = '';
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
