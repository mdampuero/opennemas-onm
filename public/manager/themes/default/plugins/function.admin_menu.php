<?php
function smarty_function_admin_menu($params, &$smarty)
{

    require(APP_PATH.'/Manager/Resources/Menu.php');

    $menu = new \Onm\UI\SimpleMenu($menuXml, SITE_URL.'manager');
    $htmlOutput = $menu->render(array('doctype' => 'html5'));

    return($htmlOutput);
}

