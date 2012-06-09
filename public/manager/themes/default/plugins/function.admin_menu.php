<?php
function smarty_function_admin_menu($params, &$smarty) {

    require(SITE_PATH.'/manager/include/menu.php');

    $menu = new \Onm\UI\SimpleMenu($menuXml, SITE_URL.'manager');
    $htmlOutput = $menu->render(array('doctype' => 'html5'));

    return($htmlOutput);

}
