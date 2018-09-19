<?php
/**
 * Check if user is allowed to display the block content
 *
 * {acl isAllowed="PRIVILEGE" hasCategoryAccess="10"}
 *    {* If user from session has access to the category then show this content *}
 *    ...smarty content...
 * {/acl}
 *
 * @param array $params The list of parameters passed to the block.
 * @param string $content The content inside the block.
 * @param \Smarty $smarty The instance of smarty.
 * @param boolean $open Whether if we are in the open of the tag of in the close.
 *
 * @return null|string
*/
function smarty_block_acl($params, $content, Smarty_Internal_Template $smarty, $open)
{
    if (!$open) {
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
}
