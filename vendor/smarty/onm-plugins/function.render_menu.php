<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_menu.php
 * Render menu items.
 */
function smarty_function_render_menu($params, &$smarty) {

	// Initialicing parameters
    $menuName = (isset($params['name']) ? $params['name'] : null);
    $position = (isset($params['position']) ? $params['position'] : null);
	$tpl = (isset($params['tpl']) ? $params['tpl'] : null);

    $output = '';
    if(empty($menuName) && empty($position)) {
        $smarty->trigger_error("Menu doesn't exists");
        return $output;
    }

    $menu = new Menu();
    if (!empty($menuName)) {
        $menu->getMenu($menuName);
    } else {
        // Get the menu by its position name
        $menu->getMenuFromPosition($position);
    }

    if (!empty($menu->items)) {
        $smarty->assign('menuItems', $menu->items);

    } else {
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
