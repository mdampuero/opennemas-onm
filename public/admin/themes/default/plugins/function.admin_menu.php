<?php
function smarty_function_admin_menu($params, &$smarty) {

    require(APP_PATH.'/Backend/Resources/Menu.php');

    $menu = new \Onm\UI\SimpleMenu($menuXml);
    // $htmlOutput = $menu->getHTML();
    $htmlOutput = $menu->render(array('doctype' => 'html5'));

    return($htmlOutput);

}
