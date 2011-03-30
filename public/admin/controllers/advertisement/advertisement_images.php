<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');
/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

$cm = new ContentManager();
$photos = $cm->find_by_category('Photo',2, 'fk_content_type=8 ', 'ORDER BY created DESC');

$photos = $cm->paginate_num($photos,16);
$pages=$cm->pager;
echo "<em>Arrastre y suelte las publicidades para seleccionarlas, use el logo para usar Flash</em><br><br>";
if($pages->_totalPages>1){
	echo "<p align='center'>Paginas: ";
	for($i=1;$i<=$pages->_totalPages;$i++){
			echo ' <a style="cursor:pointer;" onClick="get_advertisements('.$i.')">'.$i.'</a> ';
	}
	echo "</p> ";
}
echo "<ul id='thelist' class='gallery_list'> ";
if($photos){
$num=1;
	foreach ($photos as $as) {
		//if(file_exists(MEDIA_IMG_PATH.$as->path_file.$as->name)){
				$ph=new Photo($as->pk_photo);

				$ph->set_status(1,$_SESSION['userid']);
				echo '<li style="position:relative"> ';
				if((strtolower($ph->type_img)=='swf')) {
					echo'
						<object style="z-index:-3; cursor:default;"  title="Desc: '.$as->description.' Tags: '.$as->metadata.' ">
							<param name="movie" value="'.MEDIA_IMG_PATH_URL.$as->path_file.$as->name.'"> <param name="autoplay" value="false">  <param name="autoStart" value="0">
							<embed style="cursor:default;" width="68" height="40"  src="'.MEDIA_IMG_PATH_URL.$as->path_file.$as->name.'" name="'.$as->pk_photo.'" border="0" de:mas="'.$as->name.'" de:url="'.MEDIA_IMG_PATH_URL.$as->path_file.'" de:ancho="'.$as->width.'" de:alto="'.$as->height.'" de:peso="'.$as->size.'" de:created="'.$as->created.'" de:type_img="'.$as->type_img.'" de:description="'.$as->description.' title="'.$as->title.'"</embed>
						</object>
						<span style="float:right; clear:none;">
                        <img id="draggable_img'.$num.'" 
                             class="draggable" 
                             src='.SITE_URL_ADMIN.'/themes/default/images/flash.gif
                             style="width:20px" name="'.$as->pk_photo.'" 
                             border="0" de:mas="'.$as->name.'" de:url="'.MEDIA_IMG_PATH_URL.$as->path_file.'" de:ancho="'.$as->width.'" de:alto="'.$as->height.'" de:peso="'.$as->size.'" de:created="'.$as->created.'" de:type_img="'.$as->type_img.'" de:description="'.$as->description.'" ></span>
					';

				} else {
					require( dirname(__FILE__).'/../../themes/default/plugins/function.cssphotoscale.php' );
					$params = array('width' => $as->width, 'height' => $as->height, 'photo' => $as, 'resolution' => 68);
					echo 	'<div><img style="'.smarty_function_cssphotoscale($params)
							.'" src="'.MEDIA_IMG_PATH_URL.$as->path_file.$as->name.'" id="draggable_img'.$num.'" class="draggable" name="'.$as->pk_photo.'" border="0" de:mas="'.$as->name.'" de:url="'.MEDIA_IMG_PATH_URL.$as->path_file.'" de:ancho="'.$as->width.'" de:alto="'.$as->height.'" de:peso="'.$as->size.'" de:created="'.$as->created.'" de:type_img="'.$as->type_img.'"  de:description="'.$as->description.'"/></div>';
				}

				echo 	'</li>	'
						.' <script type="text/javascript">'
						.'new Draggable(\'draggable_img'.$num.'\', { revert:true, scroll: window, ghosting:true }  );'
						.'</script>';
				$num++;
		//}else{
		//	$ph=new Photo($as->pk_photo);
		//	$ph->set_status(0,$_SESSION['userid']);
		//}
	}
}
echo "	 </ul><br>";
