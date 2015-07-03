<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_menu.php
 * Render menu items.
 */
function smarty_function_render_menu($params, &$smarty) {

    // Initializing parameters
    $menuName = (isset($params['name']) ? $params['name'] : null);
    $position = (isset($params['position']) ? $params['position'] : null);
    $tpl      = (isset($params['tpl']) ? $params['tpl'] : null);

    $output = '';
    if (empty($menuName) && empty($position)) {
        $smarty->trigger_error("Menu doesn't exists");
        return $output;
    }

    $menuManager = getService('menu_repository');

    if (!empty($menuName)) {
        $criteria = [
            'name'              => [ [ 'value' => $menuName ] ],
        ];
        $menu = $menuManager->findOneBy($criteria, null, 1, 1);
    } else {
        // Get the menu by its position name
        $criteria = [
            'position'          => [ [ 'value' => $position ] ],
        ];
        $menu = $menuManager->findOneBy($criteria, null, 1, 1);
    }

    if (!empty($menu->items)) {
        $smarty->assign('menuItems', $menu->items);

    } else {
        $smarty->assign('menuItems', array());
    }

    $caching = $smarty->caching;
    $smarty->caching = 0;
    $smarty->assign('actual_category', $params['actual_category']);
    $output .= "\n". $smarty->fetch($tpl);
    $smarty->caching = $caching;

    // Render menu items
    return $output;
}
