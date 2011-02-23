<?php
function smarty_function_admin_menu($params, &$smarty) {

    // Get custom libraries that handle the menu
    require_once( SITE_LIBS_PATH.'menu.class.php');
    require(SITE_PATH.'/admin/include/menu.php');
    require_once(SITE_CORE_PATH.'privileges_check.class.php');

    // Get the menu and output it as ypmenu.
    //$menu = new Menu();
    //$htmlOutput = $menu->getMenu('YpMenu', $menuXml, 1);


    require_once( SITE_LIBS_PATH.'simple_menu.class.php');

    $menu = new SimpleMenu($menuXml);
    $htmlOutput = $menu->getHTML();

    return($htmlOutput);
}
