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
 * @author Toni Martínez <toni@openhost.es>
 * @param array $params  Parameters of smarty function
 * @param Smarty $smarty Object reference to Smarty class
 * @return string Return a HTML code of the message board
 */

function smarty_block_is_module_activated($params, $content, &$smarty, &$repeat)
{
    if (!$repeat) {
        if (!isset($params['name'])) {
            $output = '';
        }

        $modules = (preg_split('@,@', $params['name']));

        $output = '';

        if (count($modules) > 1) {
            $returnContent = false;
            foreach ($modules as $module) {
                $returnContent = ($returnContent || \Onm\Module\ModuleManager::isActivated($module));
                if ($returnContent == true) {
                    break;
                }
            }

            $output = ($returnContent) ?  $content : "";
        } else {
            $output = (\Onm\Module\ModuleManager::isActivated($params['name'])) ?  $content : "";
        }
        return $output;
    }
}
