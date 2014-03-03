<?php
/**
 * Smarty plugin
 * Check if this block is allow display your content
 *
 * {acl isAllowed="PRIVILEGE" hasCategoryAccess="10"}
 *    {* If $_SESSION['userid'] has access then show this content *}
 *    ...smarty content...
 * {/acl}
 *
*/
use Onm\Security\Acl;

function smarty_block_acl($params, $content, &$smarty, $open) {
    if( $open ) {
        // NADA
    } else {
        $check = true;

        if (isset($params['isAllowed'])) {
            $isAllowed = $params['isAllowed'];
            $check = $check && Acl::check($isAllowed);
        }

        if (isset($params['isNotAllowed'])) {
            $isAllowed = $params['isNotAllowed'];
            $check = !($check && Acl::check($isAllowed));
        }

        if (isset($params['hasCategoryAccess'])) {
            $hasCategoryAccess = $params['hasCategoryAccess'];
            $check = $check && Acl::checkCategoryAccess($hasCategoryAccess);
        }

        if (isset($params['nohasCategoryAccess'])) {
            $hasCategoryAccess = $params['nohasCategoryAccess'];
            $check = $check && Acl::checkCategoryAccess($hasCategoryAccess);
            $check = !($check);
        }
        $else = $smarty->left_delimiter . 'aclelse' . $smarty->right_delimiter;


        // $check = false;
        $true_false = explode($else, $content, 2);
        $true = (isset($true_false[0]) ? $true_false[0] : null);
        $false = (isset($true_false[1]) ? $true_false[1] : null);

        return $check ? $true : $false;
    }
}
