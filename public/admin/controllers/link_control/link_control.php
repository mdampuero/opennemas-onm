<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

if (!isset($_REQUEST['category'])) {$_REQUEST['category'] = 10;}
$category=$_REQUEST['category'];
$tpl->assign('category', $_REQUEST['category']);


	$cc = new ContentCategoryManager();
	$allcategorys = $cc->find('inmenu=1  AND internal_category=1 AND fk_content_category=0', 'ORDER BY posmenu');
	$tpl->assign('allcategorys', $allcategorys);
		$i=0;
			foreach( $allcategorys as $prima) {
				$subcat[$i]=$cc->find('inmenu=1 AND internal_category=1 AND fk_content_category ='.$prima->pk_content_category, 'ORDER BY posmenu');
				 $i++;
			}
		 	$tpl->assign('subcat', $subcat);

	$datos_cat = $cc->find('pk_content_category='.$_REQUEST['category'], NULL);
	$tpl->assign('datos_cat', $datos_cat);

	if($_REQUEST['category']==0){
		//  	$nameCat='cabeceras'; //Se mete en litter pq category 0
	}else{
		$nameCat=$cc->get_name($_REQUEST['category']);

	}
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
switch($action) {
		case 'delFile':
		//	if(is_file($path."/" . $nameCat . "/".$_REQUEST['basename'])) {
				//@unlink($path."/" . $nameCat . "/".$_REQUEST['basename']);
				//elim de la bd.
				$foto = new Photo($_REQUEST['id']);
				//	echo  "elim".$_REQUEST['id'];
				if($foto->remove($_REQUEST['id'])) {
	      			//recuperar id.

	      //			echo "delete bd ok. NoA la papelera";
				}
		//	}
		break;

		case 'mdelFiles':

			 if($_REQUEST['id']==6){ //Eliminar todos
				$cm = new ContentManager();
				$contents = $cm->find_by_category('Photo', $_REQUEST['category'], 'fk_content_type=8  and content_status=0', 'ORDER BY created DESC ');

				foreach ($contents as $cont){
					  $foto = new Photo($cont->id);
					  $foto->remove($cont->id);
				}
			 }else{

	            if(isset($_REQUEST['selected_fld']) && count($_REQUEST['selected_fld'])>0)
	            {
	                $fields = $_REQUEST['selected_fld'];
	                if(is_array($fields))
	                {
	                  foreach($fields as $i )
	                  {
	                  		$foto = new Photo($i);
							//	echo  "elim".$_REQUEST['id'];
							$foto->remove($i);
	                  }
	                }
	            }
			 }
		break;

}

if(!isset($_REQUEST['listmode'])) {
	Application::forward($_SERVER['SCRIPT_NAME'].'?listmode=weeks&category='.$category);
}

$listmode = (isset($_REQUEST['listmode']))? $_REQUEST['listmode']: 'weeks';
if($listmode == 'weeks') {

	$cm = new ContentManager();

	$photos = $cm->find_by_category('Photo', $_REQUEST['category'], 'fk_content_type=8 ', 'ORDER BY created DESC');

	if($photos){
		//Recorremos para comprobar si están sino mostramos default
		foreach($photos as $photo){
			if(file_exists(MEDIA_IMG_PATH.$photo->path_file.$photo->name)){
				$photo->content_status=1;
				$ph=new Photo($photo->pk_photo);
				$ph->set_status(1,$_SESSION['userid']);
			}else{
				$photo->content_status=0;
				$ph=new Photo($photo->pk_photo);
				$ph->set_status(0,$_SESSION['userid']);
				$no_link[]=$ph;
			}
		}

		$tpl->assign('photo', $no_link);
	}
}

$tpl->assign('MEDIA_IMG_PATH', MEDIA_IMG_PATH_WEB);


$tpl->assign('listmode', 'weeks');

$tpl->display('link_control/link_control.tpl');
