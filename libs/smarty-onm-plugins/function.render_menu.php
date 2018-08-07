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

    // Menu doesn't exists
    if (is_null($menu)) {
        return $output;
    }

    $menu->items = $menu->localize($menu->getRawItems());

    $smarty->assign([
        'menuItems'       => ((!empty($menu->items)) ? $menu->items : []),
        'actual_category' => $params['actual_category'],
    ]);
    // Disable caching for this partial
    $caching = $smarty->caching;

    $smarty->caching = 0;

    $output .= "\n" . $smarty->fetch($tpl);

    // Restore previous caching value
    $smarty->caching = $caching;

    return $output;
}
