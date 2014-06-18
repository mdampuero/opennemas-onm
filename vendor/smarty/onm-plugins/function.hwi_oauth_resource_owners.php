<?php
/**
 * Returns the resource owners.
 *
 * @param  array                    $params Array of parameters.
 * @param  Smarty_Internal_Template $smarty The smarty object.
 * @return array                            Resource owners.
 */
function smarty_function_hwi_oauth_resource_owners($params, &$smarty)
{
    $helper = getService('hwi_oauth.templating.helper.oauth');

    return $helper->getResourceOwners();
}
