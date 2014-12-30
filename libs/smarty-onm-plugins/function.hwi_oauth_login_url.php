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
    $helper = getService('hwi_oauth.templating.helper.oauth');

    return $helper->getLoginUrl($params['name']);
}
