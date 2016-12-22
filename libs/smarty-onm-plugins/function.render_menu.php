<?php
/*
 * -------------------------------------------------------------
 * File:     	function.render_menu.php
 * Render menu items.
 */
function smarty_function_render_menu($params, &$smarty)
{
    // Initializing parameters
    $tpl      = (isset($params['tpl']) ? $params['tpl'] : null);
    $menuName = (isset($params['name']) ? $params['name'] : null);
    $position = (isset($params['position']) ? $params['position'] : null);
    $output   = '';

    if (empty($menuName) && empty($position)) {
        $smarty->trigger_error("Menu doesn't exists");
        return $output;
    }

    // Get menu from name or position
    if (!empty($menuName)) {
        $criteria = [ 'name' => [ [ 'value' => $menuName ] ], ];
    } else {
        $criteria = [ 'position' => [ [ 'value' => $position ] ], ];
    }
    $menu = getService('menu_repository')->findOneBy($criteria, null, 1, 1);

    $smarty->assign([
        'menuItems'       => ((!empty($menu->items)) ? $menu->items : []),
        'actual_category' => $params['actual_category'],
    ]);

    $caching = $smarty->caching;
    $smarty->caching = 0;
    $output .= "\n". $smarty->fetch($tpl);
    $smarty->caching = $caching;

    // Render menu items
    return $output;
}
