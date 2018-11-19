<?php
/**
 * Generates the login URL for resource owner.
 *
 * @param  array                    $params Array of parameters.
 * @param  Smarty_Internal_Template $smarty The smarty object.
 * @return string                           The login url.
 */
function smarty_function_hwi_oauth_login_url($params, &$smarty)
{
    return $smarty
        ->getContainer()
        ->get('core.helper.hwi_oauth')
        ->getLoginUrl($params['name']);
}
