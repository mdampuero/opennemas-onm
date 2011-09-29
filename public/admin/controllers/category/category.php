<?php
use Onm\Settings as s,
    Onm\Message as m;
/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

Acl::checkOrForward('CATEGORY_ADMIN');
/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Section Manager'));

$ccm = ContentCategoryManager::get_instance();

$allcategorys = $ccm->categories;
$tpl->assign('allcategorys', $allcategorys);


if( isset($_REQUEST['action']) ) {

	switch($_REQUEST['action']) {

		case 'list':
            Acl::checkOrForward('CATEGORY_ADMIN');
			$categorys =array();
			$subcategorys =array();
            $i=0;
            $num_contents=array();
            $num_sub_contents =array();
            // Contabilizar por grupos
            $groups['articles'] = $ccm->count_content_by_type_group(1);
            $groups['photos']   = $ccm->count_content_by_type_group(8);
            $groups['advertisements'] = $ccm->count_content_by_type_group(2);

            foreach($allcategorys as $cate) {
               if($cate->internal_category !=0 && $cate->fk_content_category == 0 ) {
                         $num_contents[$i]['articles']       = (isset($groups['articles'][$cate->pk_content_category]))? $groups['articles'][$cate->pk_content_category] : 0;
                         $num_contents[$i]['photos']         = (isset($groups['photos'][$cate->pk_content_category]))? $groups['photos'][$cate->pk_content_category] : 0;
                         $num_contents[$i]['advertisements'] = (isset($groups['advertisements'][$cate->pk_content_category]))? $groups['advertisements'][$cate->pk_content_category] : 0;

                         $categorys[$i] = $cate;

                         $resul = $ccm->getSubcategories( $cate->pk_content_category);
                         $j=0;
                         foreach($resul as $cate) {
                              $num_sub_contents[$i][$j]['articles']       = (isset($groups['articles'][$cate->pk_content_category]))? $groups['articles'][$cate->pk_content_category] : 0;
                              $num_sub_contents[$i][$j]['photos']         = (isset($groups['photos'][$cate->pk_content_category]))? $groups['photos'][$cate->pk_content_category] : 0;
                              $num_sub_contents[$i][$j]['advertisements'] = (isset($groups['advertisements'][$cate->pk_content_category]))? $groups['advertisements'][$cate->pk_content_category] : 0;
                              $j++;
                         }
                         $subcategorys[$i]=$resul;
                     $i++;
               }
            }

            $tpl->assign('categorys', $categorys);
            $tpl->assign('num_contents', $num_contents);
            $tpl->assign('num_sub_contents', $num_sub_contents);
            $tpl->assign('subcategorys', $subcategorys);
            $tpl->assign('ordercategorys', $allcategorys);

            $tpl->display('category/list.tpl');


		break;

		case 'new':
            Acl::checkOrForward('CATEGORY_CREATE');

            $tpl->assign('formAttrs', 'enctype="multipart/form-data"');

            $categories = array();
            foreach($allcategorys as $cate) {
               if($cate->internal_category != 0 && $cate->fk_content_category == 0) {
                   $categories[] = $cate;
               }
            }
            $tpl->assign('allcategorys', $categories);

            $tpl->display('category/form.tpl');

		break;

		case 'read':
            Acl::checkOrForward('CATEGORY_UPDATE');

            $tpl->assign('formAttrs', 'enctype="multipart/form-data"');
            $category = new ContentCategory( $_REQUEST['id'] );
            $tpl->assign('category', $category);
            $subcategorys = $ccm->getSubcategories( $_REQUEST['id'] );
            $tpl->assign('subcategorys', $subcategorys);
            $categories = array();
            foreach($allcategorys as $cate) {
               if($cate->internal_category != 0 && $cate->fk_content_category == 0) {
                   $categories[] = $cate;
               }
            }
            $tpl->assign('allcategorys', $categories);
            $tpl->assign('configurations',s::get('section_settings'));


            $tpl->display('category/form.tpl');

		break;

		case 'update':
            
            Acl::checkOrForward('CATEGORY_UPDATE');

            $configurations = s::get('section_settings');
            if($configurations['allowLogo'] == 1 ) {
                $sectionDir = !empty($configurations['logoDir'])?($configurations['logoDir']):'';
 
                if(!empty($_FILES) && isset($_FILES['logo_path'])) {
                    $nameFile = $_FILES['logo_path']['name'];
                    $uploaddir = MEDIA_PATH.'/'.$sectionDir.'/'.$nameFile;

                    if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                      $_REQUEST['logo_path'] = $nameFile;
                    }
                }
            }

            $category = new ContentCategory();
            if($category->update( $_REQUEST )) {
                $ccm->reloadCategories();
            }

            /* Limpiar la cache de portada de todas las categorias */
            if(isset ($_REQUEST['inmenu']) && $_REQUEST['inmenu']==1) {
               $refresh = Content::refreshFrontpageForAllCategories();
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;

		case 'create':
            Acl::checkOrForward('CATEGORY_CREATE');

            $category = new ContentCategory();

            $configurations = s::get('section_settings');
            if($configurations['allowLogo'] == 1 ) {
                $sectionDir = !empty($configurations['logoDir'])?($configurations['logoDir']):'';

                if(!empty($_FILES) && isset($_FILES['logo_path'])) {
                    $nameFile = $_FILES['logo_path']['name'];
                    $uploaddir= MEDIA_PATH.'/'.$sectionDir.'/'.$nameFile;

                    if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                       $_POST['logo_path'] = $nameFile;
                    }
                }
            }

            if($category->create( $_POST )) {
                $ccm->reloadCategories();
            }
  
            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');
            
            $tpl->display('category/form.tpl');

        break;

		case 'delete':
            Acl::checkOrForward('CATEGORY_DELETE');

            $category = new ContentCategory();
             
            if($category->delete( $_POST['id'] )) {
                $ccm->reloadCategories();
                $msg = _("Categoy deleted successfully");
            }else{
                $msg = _("To delete a category previously you have to empty it");
            }
            m::add( $msg );

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;

		case 'empty':

		    $category = new ContentCategory();
			if( $category->empty_category( $_POST['id'] ))
                m::add( _("Category has been emptied successfully") );

			Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		  break;

		case 'set_inmenu':

		    $category = new ContentCategory($_REQUEST['id']);
            // FIXME: evitar otros valores erróneos
            $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
            $category->set_inmenu($status);
            /* Limpiar la cache de portada de todas las categorias */
         //   $refresh = Content::refreshFrontpageForAllCategories();

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;

		case 'validate':

            $configurations = s::get('section_settings');

            if($configurations['allowLogo'] == 1 ) {
                $sectionDir = !empty($configurations['logoDir'])?($configurations['logoDir']):'';

                if(!empty($_FILES) && isset($_FILES['logo_path'])) {
                    $nameFile = $_FILES['logo_path']['name'];
                    $uploaddir = MEDIA_PATH.'/'.$sectionDir.'/'.$nameFile;

                    if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
                      $_POST['logo_path'] = $nameFile;

                    }else{
                      $_POST['logo_path'] = '';

                    }
                }
            }
            if(empty($_POST['id'])) {
               $category = new ContentCategory();
               if($category->create( $_POST )) {
                   $ccm->reloadCategories();
               }
            } else {
               $category = new ContentCategory();
               if($category->update( $_POST ) ){
                   $ccm->reloadCategories();
                   /* Limpiar la cache de portada de todas las categorias */
                   if(isset ($_REQUEST['inmenu']) && $_REQUEST['inmenu']==1) {
                       $refresh = Content::refreshFrontpageForAllCategories();
                   }
               }
            }

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=read&id='.$category->pk_content_category);

		break;

        case 'config':

            $configurationsKeys = array(
                                        'section_settings',
                                        );

            $configurations = s::get($configurationsKeys);

            $tpl->assign(
                         array(
                                'configs'   => $configurations,
                            )
                        );

            $tpl->display('category/config.tpl');

        break;

        case 'save_config':
            Acl::checkOrForward('CATEGORY_SETTINGS');

            unset($_POST['action']);
            unset($_POST['submit']);

            if($_POST['section_settings']['allowLogo'] == 1 && !empty($_POST['section_settings']['logoDir'])){
                $path = MEDIA_PATH.'/'.$_POST['section_settings']['logoDir'];
                FilesManager::createDirectory($path);
            }

            foreach ($_POST as $key => $value ) {
                s::set($key, $value);
            }
 
            m::add(_('Settings saved.'), m::SUCCESS);

            $httpParams = array(
                                array('action'=>'list'),
                                );
            Application::forward($_SERVER['SCRIPT_NAME'] . '?'.String_Utils::toHttpParams($httpParams));

        break;

		default:

            Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;
	}
} else {

	Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

}
