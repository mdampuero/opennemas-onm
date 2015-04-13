<?php
function smarty_function_admin_menu($params, &$smarty)
{
    $htmlOutput = '';
    if (array_key_exists('file', $params)
        && file_exists($params['base'].$params['file'])
    ) {
        $menu = include $params['base'].$params['file'];

        $menu = new \Onm\UI\SimpleMenu($menu, SITE_URL.'manager');
        $htmlOutput = $menu->render(array('doctype' => 'html5'));
    } else {
        $htmlOutput = $params['file'];
    }

    return($htmlOutput);
}

