<?php
/**
 * Displays a menu basing on parameters. The supported parameters to identify
 * the menu are the menu id, the menu name and the menu position.
 *
 * The menu is first searched by id, then by name and last by position.
 *
 * @example {render_menu tpl="xyzzy/wobble.tpl" pk_menu=10}
 * @example {render_menu tpl="glorp/foo.tpl" pk_menu=25 name="norf"}
 * @example {render_menu tpl="norf/glork.tpl" pk_menu=30 name="norf" position="fred"}
 *
 * @param array   $params The list of parameters.
 * @param \Smarty $smarty The smarty object.
 *
 * @return string The HTML string for menu.
 */
function smarty_function_render_menu($params, &$smarty)
{
    $tpl  = $params['tpl'] ?? null;
    $keys = [
        'pk_menu'  => $params['pk_menu'] ?? null,
        'name'     => $params['name'] ?? null,
        'position' => $params['position'] ?? null,
    ];

    $validKeys = array_filter($keys, function ($a) {
        return !empty($a);
    });

    if (empty($validKeys) || empty($tpl)) {
        return '';
    }

    $menu = null;
    $i    = 0;

    $menuService = $smarty->getContainer()->get('api.service.menu');
    while (empty($menu) && $i < count($validKeys)) {
        $key = array_keys($validKeys)[$i++];
        $oql = sprintf(
            ' %s = "%s" ',
            $key,
            $validKeys[$key]
        );
        $menu = [];
        try {
            $menu = $menuService->getItemBy($oql);
        } catch (\Api\Exception\GetItemException $e) {
            $menu = [];
        }
    }

    if (empty($menu)) {
        return '';
    }

    $smarty->assign([
        'menuItems'       => !empty($menu->menu_items) ? $menu->menu_items : [],
        'actual_category' => $params['actual_category'] ?? null
    ]);

    // Disable caching for this partial
    $caching = $smarty->caching;

    $smarty->caching = 0;

    $output = $smarty->fetch($tpl);

    // Restore previous caching value
    $smarty->caching = $caching;

    return $output;
}
