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

function smarty_block_acl($params, $content, &$smarty, $open) {
    if( $open ) {
        // NADA
    } else {
        $check = true;

        if(isset($params['isAllowed'])) {
            $isAllowed = $params['isAllowed'];
            $check = $check && Acl::check($isAllowed);
        }

        if(isset($params['isNotAllowed'])) {
            $isAllowed = $params['isNotAllowed'];
            $check = !($check && Acl::check($isAllowed));
        }

        if(isset($params['hasCategoryAccess'])) {
            $hasCategoryAccess = $params['hasCategoryAccess'];
            $check = $check && Acl::checkCategoryAccess($hasCategoryAccess);
        }

        if(isset($params['nohasCategoryAccess'])) {
            $hasCategoryAccess = $params['nohasCategoryAccess'];
            $check = $check && Acl::checkCategoryAccess($hasCategoryAccess);
            $check = !($check);
        }

        return $check ? $content: '';
    }
}