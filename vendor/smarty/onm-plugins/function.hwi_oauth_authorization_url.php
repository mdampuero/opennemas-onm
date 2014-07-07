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
    $router = getService('router');

    $redirect = null;
    if (array_key_exists('redirect_url', $params)) {
        $redirect = $router->generate($params['redirect_url'], array(), true);
    }

    $extra = array();
    if (array_key_exists('extra', $params)) {
        $extra = $params['extra'];
    }

    return $helper->getAuthorizationUrl($params['name'], $redirect, $extra);
}
