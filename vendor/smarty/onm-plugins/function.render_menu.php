<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_menu.php
 * Render menu items.
 */
function smarty_function_render_menu($params, &$smarty) {

	// Initialicing parameters
	$menuName = (isset($params['name']) ? $params['name'] : null);
	$tpl = (isset($params['tpl']) ? $params['tpl'] : null);
    $smarty->assign('actual_category', $params['actual_category']);
    $output = '';
    if(empty($menuName)) {
        $smarty->trigger_error("Menu doesn't exists");
        return $output;
    }
    $menuItems= Menu::renderMenu($menuName);
    if (!empty($menuItems->items)) {
        $smarty->assign('menuItems', $menuItems->items);
    }


    $output .= "\n". $smarty->fetch( $tpl );

	// Render menu items
	return $output;
}
