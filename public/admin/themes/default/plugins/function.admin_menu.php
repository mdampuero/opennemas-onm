<?php
function smarty_function_admin_menu($params, &$smarty) {

    require(SITE_PATH.'/admin/include/menu.php');
    require_once(SITE_CORE_PATH.'privileges_check.class.php');

    $menu = new \Onm\UI\SimpleMenu($menuXml);
    $htmlOutput = $menu->getHTML();

    return($htmlOutput);

}
