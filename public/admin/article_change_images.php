<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
echo "<br>";
$cm = new ContentManager();

//TODO: change by functions class utils.

if(isset($_REQUEST['action']) )
{
	switch($_REQUEST['action'])
    {
	case 'list_by_category':
            $category=intval($_REQUEST['category']);
          //  $photos = $cm->find_by_category('Photo', $_REQUEST['category'], 'fk_content_type=8 ', 'ORDER BY created DESC');
            list($photos, $pager)= $cm->find_pages('Photo', 'fk_content_type=8  and content_status=1 and photos.media_type="image"', 'ORDER BY  created DESC ',$_REQUEST['page'],30, $category);

            break;

        case 'list_by_metadatas':

            if( isset($_REQUEST['metadatas']) && !empty($_REQUEST['metadatas'])) {
                $presentSearch = cSearch::Instance();
                $arrayIds = $presentSearch->SearchContentsSelect('pk_content', $_REQUEST['metadatas'], 'photo', 100);
                if(!empty($arrayIds))
                {
                    $szWhere = '( FALSE ';
                    foreach($arrayIds as $Id)
                    {
                        $szWhere .= ' OR pk_content = ' . $Id[0];
                    }
                    $szWhere .= ')';
                } else {
                    $szWhere = "TRUE";
                    echo "<div align=\"center\" ><p>No se encontró ningún contenido con todos los términos de su búsqueda.</p>" .
                        "<p>Su búsqueda - <b>" . $_REQUEST['metadatas'] . "</b> - no produjo ningún documento.</p></div><br/>";

                    break;
                }
            }
            else
                $szWhere = "TRUE";

            if(!isset($_REQUEST['category']) || $_REQUEST['category'] <= 0) {
              //  $photos = $cm->find('Photo', 'fk_content_type = 8 AND ' . $szWhere, 'ORDER BY created DESC');
		list($photos, $pager)= $cm->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="image" and contents.content_status=1 AND '. $szWhere, 'ORDER BY  created DESC ',$_REQUEST['page'],30);

            } else {
               // $photos = $cm->find_by_category('Photo', $_REQUEST['category'], 'fk_content_type = 8 AND ' . $szWhere, 'ORDER BY created DESC');
                list($photos, $pager)= $cm->find_pages('Photo', 'contents.fk_content_type=8 and photos.media_type="image" and contents.content_status=1 AND '. $szWhere, 'ORDER BY  created DESC ',$_REQUEST['page'],30, $_REQUEST['category']);
            }

            break;
    }
}
/*
if( isset($photos) &&
    !empty($photos))
    $photos=$cm->paginate_num($photos,30);

$cat=$_REQUEST['category'];
$pages=$cm->pager;
*/
if(isset($pager) && !empty($pager)) {
    $cat=$_GET['category'];
    $pages=$pager;
    $paginacion = $cm->makePagesLink($pages, intval($cat),$_REQUEST['action'],$_REQUEST['metadatas']);
    echo $paginacion;
}
echo "<ul id='thelist' class='gallery_list clearfix' style='width: 100%; margin: 0pt; padding: 0pt;'> ";
if(isset($photos) && !empty($photos) ){
        $num=1;
        foreach ($photos as $as) {
        //	if(!is_file($as->path_file.$as->name)){
            if(file_exists(MEDIA_IMG_PATH.$as->path_file.$as->name)){
                require( dirname(__FILE__).'/themes/default/plugins/function.cssimagescale.php' );
                $params = array('media' => MEDIA_IMG_PATH, 'photo' => $as, 'resolution' => 67);
                 $params2 = array('media' => MEDIA_IMG_PATH, 'photo' => $as, 'resolution' => 67, 'getwidth'=>1);

                     echo '<li><div style="float: left;"> <a>'.
                        '<img style="'.smarty_function_cssimagescale($params).'" src="'.MEDIA_IMG_PATH_WEB.$as->path_file.'140x100-'.$as->name.'" de:width="'.smarty_function_cssimagescale($params2).'"  id="draggable'.$cat.'_img'.$num.'" class="draggable" name="'.$as->pk_photo.'" border="0" de:mas="'.$as->name.'" de:ancho="'.$as->width.'" de:alto="'.$as->height.'" de:peso="'.$as->size.'" de:created="'.$as->created.'"  de:description="'.htmlspecialchars(stripslashes($as->description), ENT_QUOTES).'"  de:path="'.$as->path_file.'" de:tags="'.$as->metadata.'" title="Desc:'.htmlspecialchars(stripslashes($as->description), ENT_QUOTES).' Tags:'.$as->metadata.'" />'.
                        '</a></div></li>	';
                    $num++;
            }else{
                $ph=new Photo($as->pk_photo);
                $ph->set_status(0,$_SESSION['userid']);
            }
        }
}
echo "	 </ul><br>";
	//No funciona  onmouseover="return escape(\'Desc:'.$as->description.'<br>Tags:'.$as->metadata.'\');"
