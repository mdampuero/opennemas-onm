<?php
function smarty_function_admin_menu($params, &$smarty)
{
    if (array_key_exists('file', $params)
        && file_exists($params['base'] . $params['file'])
    ) {
        $menu = include $params['base'] . $params['file'];

        $menu       = new \Onm\UI\SimpleMenu($menu, SITE_URL . 'manager');
        $htmlOutput = $menu->render([ 'doctype' => 'html5' ]);
    } else {
        $htmlOutput = $params['file'];
    }

    return $htmlOutput;
}
