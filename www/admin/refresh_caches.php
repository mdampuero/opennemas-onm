<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once('core/application.class.php');
Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/content_category.class.php');

/* Modo treadstone {{{ */
require_once('./core/template_cache_manager.class.php');
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
    
    echo('Caché <strong>' . $title . '</strong> eliminada correctamente.');
} else {
    echo('No se pudo eliminar la cache solicitada');
}
/* }}} */