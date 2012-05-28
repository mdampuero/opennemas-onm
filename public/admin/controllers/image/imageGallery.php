<?php
/*
 * This file is part of the onm package.
 *
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Setup app
 **/
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');

/**
 * Setup view
 **/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$cm = new ContentManager();

/**
 * If image manager module is available get information for photos.
 **/
if (\Onm\Module\ModuleManager::isActivated('IMAGE_MANAGER')) {

    if (!defined('ITEMS_GALLERY') ){ define('ITEMS_GALLERY', 20); }

    $htmlOut ='';

    $action   = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING, array('options' => array('default'=> 'listByMetadatas')));
    $page     = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT,  array('options' => array('default'=> 1)));

    $category = filter_input( INPUT_GET, 'category', FILTER_VALIDATE_INT, array('options' => array('default'=> 0)));

    $numItems = ITEMS_GALLERY + 1;
    if (empty($page)) {
        $limit    = "LIMIT {$numItems}";
    } else {
        $limit    = "LIMIT ".($page-1) * ITEMS_GALLERY .', '.$numItems;
    }

    // Take one more than ITEMS_GALLERY for implement pagination
    if ($action == 'listByMetadatas' || empty($category)) {

        $metadatas = filter_input(INPUT_GET,'metadatas',FILTER_SANITIZE_STRING, array('options' => array('default'=> '')));

        if (!empty($metadatas)) {

            $presentSearch = cSearch::getInstance();
            $arrayIds      = $presentSearch->searchContentsSelect('pk_content', $metadatas, 'photo', 100);
            if (!empty($arrayIds))
            {
                $szWhere   = '( FALSE ';
                foreach ($arrayIds as $id) {
                    $szWhere .= ' OR pk_content = ' . $id[0];
                }
                $szWhere .= ')';
            } else {
                $szWhere = "TRUE";
                $htmlOut .= _(
                    "<div>"
                    ."<p>Unable to find any content matching your search criterira.</p>"
                    ."<p>Your search string <strong>" . $metadatas . "</strong> doesn't have any matched content.</p>"
                    ."</div>"
                );
            }

        } else {
            $szWhere = "TRUE";
        }

        if (empty($category)) {
            $photos = $cm->find(
                'Photo',
                'contents.fk_content_type = 8 AND photos.media_type="image" AND contents.content_status=1 AND ' . $szWhere,
                'ORDER BY created DESC '.$limit
            );
        } else {
            $photos = $cm->find_by_category(
                'Photo',
                $category,
                'fk_content_type = 8 AND photos.media_type="image" AND contents.content_status=1 AND ' . $szWhere,
                'ORDER BY created DESC '.$limit
            );
        }


        $params = array(
            'page'     => $page,
            'items'    => ITEMS_GALLERY,
            'total'    =>  count($photos),
            'function' => 'getGalleryImages',
            'others'   => '"listByMetadatas", "'.$category.'", "'.$metadatas.'"'
        );

        if (count($photos) > ITEMS_GALLERY) {
            array_pop($photos);
        }

        $tpl->assign('photos',  $photos);

        $imagePager = Onm\Pager\SimplePager::getPager($params);
        $tpl->assign('imagePager', $imagePager);

    } elseif ($action == 'listByCategory') {

        $photos = $cm->find_by_category(
            'Photo', $category,
            'contents.fk_content_type=8  AND contents.content_status=1 AND photos.media_type="image"',
            'ORDER BY created DESC '. $limit
        );

        $params = array(
            'page'=>$page,
            'items'=>ITEMS_GALLERY,
            'total' => count($photos),
            'function' => 'getGalleryImages',
            'others' => '"listByCategory", "'.$category.'",""'
        );
        if(count($photos)> ITEMS_GALLERY)
           array_pop($photos);
        $tpl->assign('photos', $photos);

        $imagePager = Onm\Pager\SimplePager::getPager($params);
        $tpl->assign('imagePager', $imagePager);

    }

    // AJAX REQUEST
    $htmlOut .= $tpl->fetch('image/image_gallery.ajax.tpl');
    Application::ajax_out($htmlOut);
}

