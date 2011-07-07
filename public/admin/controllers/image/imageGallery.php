<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')) {
    /**
    * Setup app
    */
    require_once(dirname(__FILE__).'/../../../bootstrap.php');
    require_once(SITE_ADMIN_PATH.'session_bootstrap.php');
    /**
     * Setup view
    */
    $tpl = new TemplateAdmin(TEMPLATE_ADMIN);
    $cm = new ContentManager();
}
/*
 * If image manager module is available get information for photos.
 */
if (\Onm\Module\ModuleManager::isActivated('IMAGE_MANAGER')) {

    if( !defined('ITEMS_GALLERY') ){
        define('ITEMS_GALLERY', 25);
    }
 
    $action = filter_input(INPUT_GET,'action',FILTER_VALIDATE_INT, array('options' => array('default'=> 'listByMetadatas')));
    $page = filter_input(INPUT_GET,'page',FILTER_VALIDATE_INT,  array('options' => array('default'=> 1)));
    $html_out='';

    $category = filter_input( INPUT_GET, 'category', FILTER_VALIDATE_INT, array('options' => array('default'=> 0)));

    $numItems = ITEMS_GALLERY +1;
    if (empty($page)) {
        $limit= "LIMIT {$numItems}";
    } else {
        $limit= "LIMIT ".($page-1) * ITEMS_GALLERY .', '.$numItems;
    }

    // Take one more than ITEMS_GALLERY for implement pagination

    if($action == 'listByMetadatas' || empty($category)) {

        $metadatas = filter_input(INPUT_GET,'metadatas',FILTER_VALIDATE_INT, array('options' => array('default'=> '')));

        if (!empty($metadatas)) {
            $presentSearch = cSearch::Instance();
            $arrayIds = $presentSearch->cache->SearchContentsSelect('pk_content', $metadatas, 'photo', 100);
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
                $html_out .= "<div align=\"center\" ><p>No se encontró ningún contenido con todos los términos de su búsqueda.</p>" .
                    "<p>Su búsqueda - <b>" . $_REQUEST['metadatas'] . "</b> - no produjo ningún documento.</p></div><br/>";

                break;
            }
        } else {
            $szWhere = "TRUE";
            $metadatas ='';
        }

        if (empty($category)) {
            $photos = $cm->find('Photo', 'contents.fk_content_type = 8 AND photos.media_type="image" AND contents.content_status=1 AND ' . $szWhere, 'ORDER BY created DESC '.$limit);
        } else {
            $photos = $cm->find_by_category('Photo', $category, 'fk_content_type = 8 AND photos.media_type="image" AND contents.content_status=1 AND ' . $szWhere, 'ORDER BY created DESC '.$limit);

        }
        

        $params = array('page'=>$page, 'items'=>ITEMS_GALLERY, 
                        'total' => count($photos), 'function'=>'getGalleryImages',
                        'others'=>'"listByMetadatas", "'.$category.'", "'.$metadatas.'"' );
        
        if(count($photos)> ITEMS_GALLERY)
            array_pop($photos);

        $tpl->assign('photos',  $photos);

        $imagePager = Onm\Pager\SimplePager::getPager($params);
        $tpl->assign('imagePager', $imagePager);
        
    }elseif ($action == 'listByCategory') {

        $photos = $cm->find_by_category('Photo', $category,
                                        'contents.fk_content_type=8  and contents.content_status=1 and photos.media_type="image"',
                                        'ORDER BY created DESC '. $limit  );

        $params = array('page'=>$page, 'items'=>ITEMS_GALLERY,
                        'total' => count($photos), 'function'=>'getGalleryImages',
                        'others'=>'"listByCategory", "'.$category.'",""' );
        if(count($photos)> ITEMS_GALLERY)
           array_pop($photos);
        $tpl->assign('photos', $photos);
        
        $imagePager = Onm\Pager\SimplePager::getPager($params);
        $tpl->assign('imagePager', $imagePager);

    }
    
    // AJAX REQUEST
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest')) {

        $html_out.=$tpl->fetch('image/imageGallery.tpl');

        Application::ajax_out($html_out);
    }
}

