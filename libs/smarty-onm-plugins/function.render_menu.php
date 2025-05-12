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

    $oql = implode(' and ', array_filter(array_map(function ($key) use ($keys) {
        return !empty($keys[$key]) ? sprintf(' %s = "%s" ', $key, $keys[$key]) : null;
    }, array_keys($keys))));

    if (empty($oql) || empty($tpl)) {
        return '';
    }

    try {
        $mh = $smarty->getContainer()->get('core.helper.menu');
        $ms = $smarty->getContainer()->get('api.service.menu');
        $cs = $smarty->getContainer()->get('api.service.category');

        $ms->setCount(0);

        $menu = $ms->getItemLocaleBy($oql);
        if (empty($menu) || empty($menu->menu_items)) {
            return '';
        }

        $menuItemsObject = $mh->castToObjectNested($menu->menu_items);

        $smarty->assign([
            'actual_category' => $params['actual_category'] ?? null,
            'categories'      => $cs->setCount(0)->getList()['items'],
            'menuItems'       => $menuItemsObject ?? [],
        ]);

        $output = $smarty->fetch($tpl);

        return $output;
    } catch (\Api\Exception\GetItemException $e) {
        return '';
    }
}
