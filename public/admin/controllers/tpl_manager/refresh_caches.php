<?php

/**
 * Setup app
*/
require_once '../../../bootstrap.php';
require_once '../../session_bootstrap.php';

$tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

if(isset($_REQUEST['category'])) {
    $ccm = ContentCategoryManager::get_instance();
    if($_REQUEST['category']!='home') {
        $category_name = $ccm->get_name($_REQUEST['category']);
        $title = $ccm->get_title($category_name);
        $title = sprintf(_("Frontpage for category %s"), $title);
    } else {
        $category_name = 'home';
        $title = _('General frontpage');
    }
    $category_name = preg_replace('/[^a-zA-Z0-9\s]+/', '', $category_name);

    $tplManager->delete($category_name . '|RSS');
    $delete = $tplManager->delete($category_name . '|0'); // this sentence remove index.tpl and mobile.index.tpl

    echo("<div class='alert alert-info'>"
            ."<button class='close' data-dismiss='alert'>×</button>"
            . _("<strong>{$title}</strong> cache deleted succesfully.")
        ."</div>");
} else {
    echo("<div class='alert alert-error'>"
            ."<button class='close' data-dismiss='alert'>×</button>"
            ._("There was an error trying to delete the requested cache page.")
        ."</div>");
}