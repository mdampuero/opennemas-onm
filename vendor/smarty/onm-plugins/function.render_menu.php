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

    $output = '';
    if(empty($menuName)) {
        $smarty->trigger_error("Menu doesn't exists");
        return $output;
    }

    $menuItems = Menu::renderMenu($menuName);
    if (!empty($menuItems->items)) {
        $smarty->assign('menuItems', $menuItems->items);

    }else {
       $smarty->assign('menuItems', array());
    }

    $caching = $smarty->caching;
    $smarty->caching = 0;
    $smarty->assign('actual_category', $params['actual_category']);
    $output .= "\n". $smarty->fetch( $tpl );
    $smarty->caching = $caching;


	// Render menu items
	return $output;
}
