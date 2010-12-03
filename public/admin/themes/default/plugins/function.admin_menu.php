<?php
function smarty_function_admin_menu($params, &$smarty) {

    // Get custom libraries that handle the menu
    require_once( SITE_LIBS_PATH.'menu.class.php');
    require(SITE_PATH.'/admin/include/menu.php');
    require_once(SITE_CORE_PATH.'privileges_check.class.php');

    // Get the menu and output it as ypmenu.
    $menu = new Menu();
    $return = $menu->getMenu('YpMenu', $menuXml, 1);
    return($return);
}