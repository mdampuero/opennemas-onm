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
 * Fetch HTTP variables
*/

$category_name = $request->query->filter('category_name', 'home', FILTER_SANITIZE_STRING);
$subcategory_name = $request->query->filter('subcategory_name', '', FILTER_SANITIZE_STRING);
$cache_page = $request->query->filter('page', 0, FILTER_VALIDATE_INT);
$date = $request->query->filter('date', '', FILTER_SANITIZE_STRING);

if ( !(isset($category_name) && !empty($category_name)) ) {
    $category_name = 'home';
}
/**
 * Setup view
 */
$tpl = new Template(TEMPLATE_USER);
//$tpl->setConfig('newslibrary');
 $cache_id = $tpl->generateCacheId($category_name, $subcategory_name, $date);

$tpl->assign('newslibraryDate',$date);
/**
 * Fetch information for Advertisements
*/
require_once("index_advertisement.php");

$ccm = ContentCategoryManager::get_instance();
$settings = s::get('newslibraryView');

if ( 1==1 || ($tpl->caching == 0)  || !$tpl->isCached('frontpage/newslibrary.tpl', $cache_id) )
{

    $fp = new Frontpage();

    /****************** FETCHING NEWS IN STATIC FILES **********************/
   if (!empty($settings) &&
           $settings == 'listFrontpages') {

        if($category_name != 'home') {
          $actual_category_id = $ccm->get_id($category_name);
        } else {
          $actual_category_id = 0;
        }
        //TODO: review this option
        if( $fp->getFrontpage($date, $actual_category_id) ) {

            $articles_home = array();
            if(!empty($fp->contents)){
                foreach($fp->contents as $element) {

                    $content = new $element['content_type']($element['pk_fk_content']);
                    // add all the additional properties related with positions and params

                    $placeholder = ($actual_category_id == 0) ? 'home_placeholder': 'placeholder';
                    $content->load(array(
                        $placeholder => $element['placeholder'],
                        'position'   => $element['position'],
                        'type'       => $element['content_type'],
                        'params'     => unserialize($element['params']),
                    ));

                    if(!empty($content->fk_video)) {
                        $content->video = new Video($content->fk_video);

                    }else {
                        if(!empty($content->img1)) {
                            $content->image = new Photo($content->img1);
                        }
                    }

                    $articles_home[] = $content;

                }
            }
        }

        $tpl->assign('articles_home', $articles_home);

        $tpl->display('frontpage/fp_newslibrary.tpl');

    } elseif ($settings == 'staticFrontpages') {
                //cronicas method
        if($category_name != 'home') {
            $actual_category_id = $ccm->get_id($category_name);
        } else {
            $actual_category_id = 0;
        }
        $patterns = array ('',
                   );

        $path = preg_replace('/(\d{4})(\d{2})(\d{2})/', '/$1/$2/$3', $date);
        var_dump($path);
        var_dump(INSTANCE_MEDIA."library/{$path}/{$category_name}.html");
        if( !empty($date) ) {
            echo file_get_contents(INSTANCE_MEDIA."library/{$path}/{$category_name}.html");
        }
    } else {

        $cm = new ContentManager();
        $allCategories = $ccm->categories;

        $library = array();
        $contents= $cm->getContentsForLibrary($date);
        if(!empty($contents)) {
            foreach ($contents as $content) {
               $categoryID = $content->category;
               $library[$categoryID] = new stdClass();
               $library[$categoryID]->id = $categoryID;
               $library[$categoryID]->title = $allCategories[$categoryID]->title;
               $library[$categoryID]->contents[] = $content;
}
        }

        $tpl->assign('library',$library);

        $tpl->display('frontpage/fp_list_contents.tpl');

    }

} // $tpl->is_cached


