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
 * @return string Return a HTML code of the message board
 */
function smarty_function_multi_option_adapter($params)
{
    if (!is_array($params) ||
        !array_key_exists('field', $params) ||
        empty($params['field']) ||
        !array_key_exists('params', $params) ||
        empty($params['params'])
    ) {
        return null;
    }

    if (isset($params['field'][$params['params']['default']])) {
        return $params['field'][$params['params']['default']];
    }

    return current($params['field']);
}
