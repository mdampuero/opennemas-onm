<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

require_once(SITE_LIBS_PATH.'utils.functions.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', _('Section Manager'));

/**
 * Setup Database access
*/
$ccm = new ContentCategoryManager();

$allcategorys = $ccm->find('internal_category != 0 AND fk_content_category =0', 'ORDER BY inmenu DESC, posmenu');
$tpl->assign('allcategorys', $allcategorys);

if (!isset($_REQUEST['page'])) {
     $_REQUEST['page'] = 1;
}

if( isset($_REQUEST['action']) ) {

	switch($_REQUEST['action']) {

		case 'list':

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
				   if($cate->internal_category !=0 ) {

						 $num_contents[$i]['articles']       = (isset($groups['articles'][$cate->pk_content_category]))? $groups['articles'][$cate->pk_content_category] : 0;
						 $num_contents[$i]['photos']         = (isset($groups['photos'][$cate->pk_content_category]))? $groups['photos'][$cate->pk_content_category] : 0;
						 $num_contents[$i]['advertisements'] = (isset($groups['advertisements'][$cate->pk_content_category]))? $groups['advertisements'][$cate->pk_content_category] : 0;
						 $categorys[$i]=$cate;

						 $resul = $ccm->find('internal_category != 0 AND fk_content_category ='.$cate->pk_content_category, 'ORDER BY  inmenu DESC, posmenu ASC');
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

               $tpl->assign('formAttrs', 'enctype="multipart/form-data"');

			   $tpl->display('category/form.tpl');

		break;

		case 'read': //habrá que tener en cuenta el tipo

               $tpl->assign('formAttrs', 'enctype="multipart/form-data"');
			   $category = new ContentCategory( $_REQUEST['id'] );
			   $tpl->assign('category', $category);
			   $subcategorys = $ccm->find('fk_content_category ='.$_REQUEST['id'], 'ORDER BY fk_content_category,posmenu');
			   $tpl->assign('subcategorys', $subcategorys);

			   $tpl->display('category/form.tpl');

		break;

		case 'update':

			   $category = new ContentCategory();
			   $nameFile = $_FILES['logo_path']['name'];
			   if(!empty($nameFile)){
					$uploaddir="../media/sections/".$nameFile;
					if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
						 $_REQUEST['logo_path'] = $nameFile;
					}else{
						 $_REQUEST['logo_path'] ='';
					}
			   }
			   $category->update( $_REQUEST );

			   Application::forward($_SERVER['SCRIPT_NAME'].'?action=list');

		break;

		  case 'create':

			   $category = new ContentCategory();
						   $nameFile = $_FILES['logo_path']['name'];
						   $uploaddir="../media/sections/".$nameFile;

						   if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
							   $_POST['logo_path'] = $nameFile;
						   }else{
								$_POST['logo_path'] ='';
						   }

			   if($men = $category->create( $_POST )) {
				   Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$men);
			   } else {
				   $tpl->assign('errors', $category->errors);
			   }

			   $tpl->display('category/form.tpl');

		  break;

		  case 'delete':

			   $category = new ContentCategory();
			   $resp = $category->delete( $_POST['id'] );

			   Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$resp);

		  break;

		  case 'empty':

			   $category = new ContentCategory();
			   $resp = $category->empty_category( $_POST['id'] );

			   Application::forward($_SERVER['SCRIPT_NAME'].'?action=list&resp='.$resp);

		  break;

		  case 'set_inmenu':

			   $category = new ContentCategory($_REQUEST['id']);
			   // FIXME: evitar otros valores erróneos
			   $status = ($_REQUEST['status']==1)? 1: 0; // Evitar otros valores
			   $category->set_inmenu($status);

			   Application::forward($_SERVER['SCRIPT_NAME'].'?action=list_pendientes&category='.$_REQUEST['id'].'&page='.$_REQUEST['page']);

		  break;

		  case 'validate':

			   $nameFile = $_FILES['logo_path']['name'];
			   $uploaddir="../media/sections/".$nameFile;

			   if (move_uploaded_file($_FILES["logo_path"]["tmp_name"], $uploaddir)) {
				  $_POST['logo_path'] = $nameFile;
				  $_REQUEST['logo_path'] = $nameFile;
			   }else{
				  $_POST['logo_path'] = '';
				  $_REQUEST['logo_path'] = '';
			   }
			   if(empty($_POST["id"])) {
					   $category = new ContentCategory();
					   if(!$category->create( $_POST ))
					   $tpl->assign('errors', $category->errors);
			   } else {
					   $category = new ContentCategory();
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
