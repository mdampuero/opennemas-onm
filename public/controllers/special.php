<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/

use Onm\Settings as s;
/**
 * Start up and setup the app
*/
require_once '../bootstrap.php';

/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);

$category_name = $request->query->filter('category_name', '',
                    FILTER_SANITIZE_STRING);

if (!empty($category_name)) {
    $ccm                = ContentCategoryManager::get_instance();
    $category           = $ccm->get_id($category_name);
    $actual_category_id = $category; // FOR WIDGETS
    $category_real_name = $ccm->get_title($category_name); //used in title

} else {
    $category_real_name = 'Portada';
    $category_name      = 'home';
    $category           = 0;
    $actual_category_id = 0;
}

$tpl->assign(array(
    'category_name'         => $category_name,
    'category'              => $category,
    'actual_category_id'    => $actual_category_id,
    'category_real_name'    => $category_real_name,
    'actual_category_title' => $category_real_name,
    'actual_category'       => $category_name,
));

/**
 * Route to the proper action
 */
$action = $request->query->filter('action', 'list', FILTER_SANITIZE_STRING);

switch ($action) {
    case 'show':
        $dirtyID = $request->query->filter('special_id', '',
            FILTER_SANITIZE_STRING);

        $specialID = Content::resolveID($dirtyID);
        $cacheID   = $tpl->generateCacheId($category_name, null, $specialID);
        $special   = new Special($specialID);

        if ($special->available == 1) {

            Content::setNumViews($specialID);
            $contents = $special->getContents($specialID);
            $columns  = array();

            if (!empty($contents)) {
                if ((count($contents) == 1)  &&
                    (($contents[0]['type_content']=='Attachment')
                    || ($contents[0]['type_content']=='3'))) {

                    $content = Content::get($contents[0]['fk_content']);

                    $special->pdf_path = $content->path;
                } else {
                    foreach ($contents as $item) {

                        $content = Content::get($item['fk_content']);

                        if (isset($content->img1)) {
                            $img                = new Photo($content->img1);
                            $content->img1_path = $img->path_file.$img->name;
                            $content->img1      = $img;
                        }
                        if (isset($content->fk_video)) {
                            $video              = new Video($content->fk_video);
                            $content->obj_video = $video;
                        }

                        if (($item['position']%2) == 0) {
                            $content->placeholder = 'placeholder_0_1';
                        } else {
                            $content->placeholder = 'placeholder_1_1';
                        }
                        $columns[] = $content;

                        $content->category_name  =
                            $content->loadCategoryName($item['fk_content']);
                        $content->category_title =
                            $content->loadCategoryTitle($item['fk_content']);

                    }
                }
            }

            if (!empty($special->img1)) {
                $img               = new Photo($special->img1);
                $special->path_img = $img->path_file.$img->name;
                $special->img      = $img;
            }

            $tpl->assign('special', $special);
            $tpl->assign('columns', $columns);

        }//if available

        $tpl->display('special/special.tpl', $cacheID);

    break;

    case 'list':

        $cacheID  = $tpl->generateCacheId($category_name, null, null);
        $cm       = new ContentManager();
        $specials = $cm->find_by_category(
                        'Special', $actual_category_id,
                        'available=1',
                        ' ORDER BY starttime DESC LIMIT 10');

        if (!empty($specials)) {
            foreach ($specials as &$special) {
                if (!empty($special->img1)) {
                        $img                = new Photo($special->img1);
                        $special->img1_path = $img->path_file.$img->name;
                        $special->img       = $img;
                }
                $special->category_name  = $special->loadCategoryName($special->id);
                $special->category_title = $special->loadCategoryTitle($special->id);
            }

            //Asignar un especial que no sea pdf.
            $tpl->assign('specials', $specials);
        }
        $tpl->display('special/frontpage_special.tpl', $cacheID);

    break;
}

// Fetch information for Advertisements
require_once "index_advertisement.php";
