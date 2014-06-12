<?php
/**
 * Generates the authorization URL for resource owner.
 *
 * @param  array                    $params Array of parameters.
 * @param  Smarty_Internal_Template $smarty The smarty object.
 * @return string                           The authorization url.
 */
function smarty_function_hwi_oauth_authorization_url($params, &$smarty)
{
    $helper = getService('hwi_oauth.templating.helper.oauth');

    $redirect = array_key_exists('redirect_url', $params) ?
        $params['redirect_url'] : null;

    $extra = array_key_exists('extra', $params) ? $params['extra'] : array();

    return $helper->getAuthorizationUrl($params['name'], $redirect, $extra);
}
