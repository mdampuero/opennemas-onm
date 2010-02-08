<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

// Ejemplo para tener objeto global
require_once('core/application.class.php');
require_once('libs/utils.functions.php');

Application::import_libs('*');
$app = Application::load();

$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Gesti&oacute;n de Secciones');

require_once('core/pc_content_manager.class.php');
require_once('core/pc_content.class.php');
require_once('core/pc_content_category.class.php');
require_once('core/pc_content_category_manager.class.php');


if (!isset($resp)) {$resp = 1;}

			

if( isset($_REQUEST['action']) ) {
    switch($_REQUEST['action']) {
        case 'list':  //Buscar publicidad entre los content
                $cm = new PC_ContentCategoryManager();
                //Listamos los tipos
                //$alltypes = $cm->list_types();
                $alltypes=array(1=>'foto',2=>'video',3=>'carta',4=>'opinion');
                // ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
                foreach($alltypes as $key => $val){
                    $categorys[$val] = $cm->find_by_type($key, 'inmenu=1','ORDER BY posmenu');
                }
                $tpl->assign('categorys', $categorys);

        break;

        case 'new':
                $cm = new PC_ContentCategoryManager();
                $alltypes = $cm->list_types();
                $tpl->assign('alltypes', $alltypes);
                // Nada
        break;

        case 'read': //habrá que tener en cuenta el tipo
                $cm = new PC_ContentCategoryManager();
                $category = new PC_ContentCategory( $_REQUEST['id'] );
                $tpl->assign('category', $category);
                $alltypes = $cm->list_types();
                $tpl->assign('alltypes', $alltypes);
        break;

        case 'update':
                $category = new PC_ContentCategory();
                $category->update( $_REQUEST );

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;

        case 'create':
                $category = new PC_ContentCategory();
                if($category->create( $_REQUEST )) {
                        Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
                } else {
                        $tpl->assign('errors', $category->errors);
                }
        break;

        case 'delete':
                $category = new PC_ContentCategory();
                $resp=$category->delete( $_POST['id'] );

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$resp);
        break;

        case 'change_available':
                $status = ($_REQUEST['status']==1)? 1: 0;
                $category = new PC_ContentCategory( $_REQUEST['id'] );
                $resp=$category->set_available( $status );

                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$resp);

        break;

        case 'validate':
                $category = null;
                if(empty($_POST["id"])) {
                        $category = new PC_ContentCategory();
                        if(!$category->create( $_POST ))
                                $tpl->assign('errors', $category->errors);
                } else {
                        $category = new PC_ContentCategory();
                        $category->update( $_REQUEST );
                }
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$category->pk_content_category);
        break;

        default:
                Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
        break;
    }
} else {
	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
}

$tpl->display('pc_sections.tpl');

