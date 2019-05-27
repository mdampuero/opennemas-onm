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

    while (!$menu && $i < count($validKeys)) {
        $key      = array_keys($validKeys)[$i++];
        $criteria = [ $key => [ [ 'value' => $validKeys[$key] ] ] ];

        $menu = $smarty->getContainer()->get('menu_repository')
            ->findOneBy($criteria, null, 1, 1);
    }

    if (empty($menu)) {
        return '';
    }

    $menu->items = $menu->localize($menu->getRawItems());

    $smarty->assign([
        'menuItems'       => !empty($menu->items) ? $menu->items : [],
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
