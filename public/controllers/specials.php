<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s;
/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

$ccm = new ContentCategoryManager();
$cm = new ContentManager();

$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
if(empty($category_name)) {
    $category_name = filter_input(INPUT_POST,'category_name',FILTER_SANITIZE_STRING);
}

$menuFrontpage = Menu::renderMenu('album');
$tpl->assign('menuFrontpage',$menuFrontpage->items);


if(!empty($category_name)) {
    $category = $ccm->get_id($category_name);
    $actual_category_id = $category; // FOR WIDGETS
    $category_real_name = $ccm->get_title($category_name); //used in title
    $tpl->assign(array( 'category_name' => $category_name ,
                        'category' => $category ,
                        'actual_category_id' => $actual_category_id ,
                        'category_real_name' => $category_real_name ,
                ) );
} else {
    //$category_name = 'Portada';
    $category_real_name = 'Portada';
    $tpl->assign(array(
        'category_real_name' => $category_real_name ,
    ));
}

/******************************  CATEGORIES & SUBCATEGORIES  *********************************/

//Getting articles
$cm = new ContentManager();

/**
 * Route to the proper action
 */
$action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING);

if (!is_null($action) ) {

    switch ($action) {

        case 'frontpage':

             $tpl->display('special/special_frontpage.tpl',$cacheID);

        break;

        case 'show':

            /********************************* ESPECIALES COLUMNA *************************/
            //Modulo especial
            $special=new Special();
            if(!empty($_REQUEST['special_id'])){
                $specials=$special->get_by_category($actual_category, ' pk_special !='.$_REQUEST['special_id'].' AND available=1 ', ' favorite DESC, created DESC LIMIT 0,5');
            }else{
                $specials=$special->get_by_category($actual_category, ' available=1 ', ' favorite DESC, created DESC LIMIT 0,5');

            }
            $tpl->assign('specials', $specials);
            if(!empty($specials)) {
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

            }
            $special = new Special($_REQUEST['special_id']);
            if ($special->available==1) {
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
                $noticias_right= array();
                $noticias_left =array();

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


                }//if available

        break;
    }
} else {
    Application::forward301('/');
}


// Visualizar
$tpl->display('special/special.tpl', $cacheID);


