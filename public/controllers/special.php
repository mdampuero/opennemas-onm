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

$category_name = $request->query->filter('category_name', '', FILTER_SANITIZE_STRING);

if(!empty($category_name)) {
    $ccm = new ContentCategoryManager();
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

/**
 * Route to the proper action
 */
$action = $request->query->filter('action', '', FILTER_SANITIZE_STRING);

if (!is_null($action) ) {

    switch ($action) {

        case 'show':

            $dirtyID = $request->query->filter('special_id', '' , FILTER_SANITIZE_STRING);

            $specialID = Content::resolveID($dirtyID);
			$cacheID = $tpl->generateCacheId($category_name, null, $specialID);
			if (is_null($specialID)) {

                $specials = $special->get_by_category($category_name, ' available=1 ',
                        ' favorite DESC, created DESC LIMIT 0,5');

                if(!empty($specials)) {
                    //Asignar un especial que no sea pdf.

                    $seguir=0;

                    foreach($specials as $spec) {
                        if(($spec['only_pdf'] != 1) && ($spec['available'] == 1) && ($seguir==0)) {
                            $specialID = $spec['pk_special'];
                            $special = $spec;

                            $seguir=1;
                        }
                    }
                }

            } else{
                $special = new Special($specialID);
            }
            if ($special->available==1) {

                Content::setNumViews($specialID);
                $contents = $special->get_contents($specialID);
                $columns = array();

                if(!empty($contents)) {
                    foreach($contents as $item) {

                        $content = Content::get($item['fk_content']);

                        if(isset($content->img1)) {
                            $img = new Photo($content->img1);
                            $content->img1_path = $img->path_file.$img->name;
                            $content->img1 = $img;
                        }
                        if(isset($content->fk_video)) {
                            $video = new Video($content->fk_video);
                            $content->obj_video = $video;
                        }

                        if(($item['position']%2)==0){
                            $content->placeholder = 'placeholder_0_1';
                        }else{
                            $content->placeholder = 'placeholder_1_1';
                        }
                        $columns[] = $content;

                        $content->category_name = $content->loadCategoryName($item['fk_content']);
                        $content->category_title = $content->loadCategoryTitle($item['fk_content']);

                    }
                }

                if(!empty($special->img1)){
                    $img = new Photo($special->img1);
                    $special->path_img = $img->path_file.$img->name;
                    $special->img = $img;
                }

                $tpl->assign('special', $special);
                $tpl->assign('columns', $columns);


                }//if available

        break;
    }

} else {
    Application::forward301('/');
}

// Visualizar
$tpl->display('special/special.tpl', $cacheID);


