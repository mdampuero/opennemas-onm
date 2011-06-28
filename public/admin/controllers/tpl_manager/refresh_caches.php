<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

$tplManager = new TemplateCacheManager(TEMPLATE_USER_PATH);

if(isset($_REQUEST['category'])) {
    $ccm = ContentCategoryManager::get_instance();
    if($_REQUEST['category']!='home') {
        $category_name = $ccm->get_name($_REQUEST['category']);
        $title = $ccm->get_title($category_name);
    } else {
        $category_name = 'home';
        $title = 'PORTADA';
    }

    $tplManager->delete($category_name . '|RSS');
    $delete = $tplManager->delete($category_name . '|0'); // this sentence remove index.tpl and mobile.index.tpl

    echo("<div class='notice'>". _("Cache for <em>{$title}</em> frontpage deleted succesfully.") ."</div>");
} else {
    echo("<div class='error'>There was an error trying to delete the requested cache page.</div>");
}
