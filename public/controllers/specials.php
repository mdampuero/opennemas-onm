<?php
//error_reporting(E_ALL);

header("Content-Type: text/html; charset=UTF-8");
require('config.inc.php');
require_once('core/application.class.php');

Application::import_libs('*');
$app = Application::load();

require_once('core/content_manager.class.php');
require_once('core/content.class.php');
require_once('core/article.class.php');
require_once('core/advertisement.class.php');
require_once('core/related_content.class.php');
require_once('core/attachment.class.php');
require_once('core/attach_content.class.php');

require_once('core/opinion.class.php');
require_once('core/special.class.php');
require_once('core/float_module.class.php');
require_once('core/forum.class.php');

require('core/media.manager.class.php');
require_once('core/img_galery.class.php');
require_once('core/album.class.php');

require_once('core/content_category.class.php');
require_once('core/content_category_manager.class.php');

$tpl = new Template();


require_once( "module_categories_manage.php");

 
$cm = new ContentManager();
/********************************* ESPECIALES COLUMNA *************************/
//Modulo especial
$special=new Special();
if(!empty($_REQUEST['special_id'])){
    $specials=$special->get_by_category($actual_category, ' pk_special !='.$_REQUEST['special_id'].' AND available=1 ', ' favorite DESC, created DESC LIMIT 0,5');
}else{
     $specials=$special->get_by_category($actual_category, ' available=1 ', ' favorite DESC, created DESC LIMIT 0,5');
     
}
 $tpl->assign('specials', $specials);
 if(!empty($specials)){
     //Asignar un especial que no sea pdf.
    if(empty($_REQUEST['special_id']) ){
        $seguir=0;

        foreach($specials as $spec) {
            if(($spec['only_pdf'] != 1) && ($spec['available'] == 1) && ($seguir==0)){
                $_REQUEST['special_id'] = $spec['pk_special'];

                $seguir=1;
            }
        }

    }
   
 }else{
       $html = <<< ERROR_HTML_OUTPUT
     <h1>No hay ningún especial disponible</h1>
        <script type="text/javascript">
            window.setTimeout(function() {
                history.back();
            }, 1200);
            </script>
ERROR_HTML_OUTPUT;
                echo $html;
                exit(0);
     Application::forward( 'index.php');

 }
/**********************************************************************/



//content_status= 0 no publicado, 1 publicado 3 pendiente
// ContentManager::find_by_category(<TIPO_CONTENIDO>, <CATEGORY>, <CLAUSE_WHERE>, <CLAUSE_ORDER>);
$special = new Special($_REQUEST['special_id']);
if($special->available==1){
	$tpl->assign('special', $special);

	$dimensions = new MediaItem("media/images/".$special->img1);
	$width=$dimensions->width;
        if(!$width) { //No imagen
             $width=10;
        }
        $tpl->assign('vertical', '0');
        if($width>=580){
            $width=670;
            if($width < $dimensions->height){
             $tpl->assign('vertical', '1');
            }
        }
       
        $tpl->assign('width', $width);
	
    /********************************************************************************************/

    $special->set_numviews($_REQUEST['special_id']);
    $nots=$special->get_contents($_REQUEST['special_id']);
    if($nots) {
        foreach($nots as $noticia){
            if(($noticia['position']%2)==0){
                $noticias_right[]=new Article($noticia['fk_content']);
            }else{
                $noticias_left[]=new Article($noticia['fk_content']);
            }
        }
    }
    $tpl->assign('noticias_right', $noticias_right);
    $tpl->assign('noticias_left', $noticias_left);

    $c=0;
    $relia= new Related_content();

    if($noticias_right){
        $adjuntos1 = array();
        $relat_c1 = array();
        $cat1 = array();
        $img_widths_c1 = array();
        $galery_c1 = array();
        
        foreach($noticias_right as $noticia) {
            //galerys
                $dimensions = new MediaItem("media/images/".$noticia->img1);
                $width = $dimensions->width;
                if($width>300){
                    $width=300;
                }
                $img_widths_c1[$c] = $width;
                $gal= new Img_galery();
                $galery_c1[$c] = $gal->read_galery_num( $noticia->id,1);

                $tantos = count($galery_c1[$c]);
                if($noticia->img1){
                    $tantos=$tantos+2; // 1 la imag 2 el contador en 1
                }
                $tantos_c1[$c] =$tantos;

                
                $relationes = $relia->get_relations($noticia->id);
                $i=0;
                foreach($relationes as $id_rel)
                {
                    //Se recorre el array para con el id localizar la tabla con todos los campos de la noticia relacionada
                    $relationed[$i] = $cm->find('Article' , 'pk_content="'.$id_rel.'" ' , 'ORDER BY position DESC');

                    //se genera un array para cada noticia se incluye todas las noticias relacionadas
                    if(!empty($relationed[$i]))
                        $relat_c1[$c][]=$relationed[$i][0];
                    else
                        $relat_c1[$c][]= null;
                    $i++;
                 }
                 //se pasa un array multiple con los id de la noticia de la colum1 y  las relacionadas con esta

                $attach_rel = new Attach_content();
                $reles=array();
                $reles=$attach_rel->get_attach_relations($noticia->id);
                $i=0;
                foreach($reles as $attaches) {
                     $resul = new Attachment($attaches);
                     $adjuntos1[$c][]=$resul;
                     $cat1[$c][$i]="cronicas";  //default
                     $cat1[$c][$i] = $ccm->get_name($resul->category);
                     $i++;
                }
            $c++;
        }
        $total_c1 = count($noticias_right)+1;
        $tpl->assign('relationed_c1', $relat_c1);
        $tpl->assign('adjuntos1', $adjuntos1);
        $tpl->assign('cat1', $cat1);
        $tpl->assign('img_widths_c1', $img_widths_c1);
        $tpl->assign('galery_c1', $galery_c1);
        $tpl->assign('tantos_c1', $tantos_c1);
        $tpl->assign('total_c1',$total_c1);
    }


    $c=0;
    if($noticias_left){
        $adjuntos2 = array();
        $relat_c2 = array();
        $cat2 = array();
        $img_widths_c2 = array();
        $galery_c2 = array();

        foreach($noticias_left as $noticia) {
            //galerys
                 $dimensions = new MediaItem("media/images/".$noticia->img1);
                        $width=$dimensions->width;
                        if($width>300){
                            $width=300;
                        }
                        $img_widths_c2[$c] = $width;
                        $gal= new Img_galery();
                $galery_c2[$c] = $gal->read_galery_num( $noticia->id,1);

                $tantos = count($galery_c2[$c]);
                if($noticia->img1){
                    $tantos=$tantos+2; // 1 la imag 2 el contador en 1
                }
                $tantos_c2[$c] =$tantos;


                $relationes = $relia->get_relations($noticia->id);
                $i=0;
                foreach($relationes as $id_rel)
                {
                    //Se recorre el array para con el id localizar la tabla con todos los campos de la noticia relacionada
                    $relationed[$i]=$cm->find('Article' , 'pk_content="'.$id_rel.'" ' , 'ORDER BY position DESC');

                    //se genera un array para cada noticia se incluye todas las noticias relacionadas
                    if(isset ($relat_c2[$c])){
                        $relat_c2[$c][]=$relationed[$i][0];
                    }
                    $i++;
                 }
                 //se pasa un array multiple con los id de la noticia de la colum1 y  las relacionadas con esta

                    $attach_rel = new Attach_content();
                    $reles=array();
                    $reles=$attach_rel->get_attach_relations($noticia->id);
                    $i=0;
                     foreach($reles as $attaches) {
                             $resul = new Attachment($attaches);
                             $adjuntos2[$c][]=$resul;
                             $cat2[$c][$i]="cronicas";  //default
                            $ccm->get_name($resul->category);
                             $i++;
                      }
            $c++;
        }



        $tpl->assign('relationed_c2', $relat_c2);
        $tpl->assign('adjuntos2', $adjuntos2);
        $tpl->assign('cat2', $cat2);
        $tpl->assign('img_widths_c2', $img_widths_c2);
        $tpl->assign('galery_c2', $galery_c2);
        $tpl->assign('tantos_c2', $tantos_c2);

    }

    }//if available

/****************************** COLUMN RIGHT ***********************************/
require_once ("widget_agenda.php");
require_once ("widget_albums.php");
require_once ("widget_float_module.php");
require_once ("widget_opinions.php");
require_once ("widget_most_viewed.php");
require_once ("widget_forums.php");

require_once ("widget_hemeroteca.php");


$tpl->assign('home', 'index.php');

// Visualizar
$tpl->display('especiales.tpl');


