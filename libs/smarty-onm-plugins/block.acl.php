<?php
/**
 * Smarty plugin
 * Check if this block is allow display your content
 *
 * {acl isAllowed="PRIVILEGE" hasCategoryAccess="10"}
 *    {* If user from session has access to the category then show this content *}
 *    ...smarty content...
 * {/acl}
 *
*/
function smarty_block_acl($params, $content, Smarty_Internal_Template $smarty, $open)
{
    if ($open) {
        return null;
    }

    $security = getService('core.security');
    $check    = true;

    if (isset($params['hasExtension'])) {
        $check = $check && $security->hasExtension($params['hasExtension']);
    }

    if (isset($params['isAllowed'])) {
        $check = $check && $security->hasPermission($params['isAllowed']);
    }

    if (isset($params['isNotAllowed'])) {
        $check = $check && !$security->hasPermission($params['isNotAllowed']);
    }

    if (isset($params['hasCategoryAccess'])) {
        $check = $check && $security->hasCategory($params['hasCategoryAccess']);
    }

    $else = $smarty->left_delimiter . 'aclelse' . $smarty->right_delimiter;

    $true_false = explode($else, $content, 2);
    $true       = (isset($true_false[0]) ? $true_false[0] : null);
    $false      = (isset($true_false[1]) ? $true_false[1] : null);

    return $check ? $true : $false;
}
