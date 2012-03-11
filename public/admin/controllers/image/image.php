<?php
/*
 * This file is part of the Onm package.
 *
 * (c)  Fran Dieguez <fran@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Onm\Settings as s,
    Onm\Message as m;

// Setup app
require_once(dirname(__FILE__).'/../../../bootstrap.php');
require_once(SITE_ADMIN_PATH.'session_bootstrap.php');
require_once(SITE_CORE_PATH.'privileges_check.class.php');

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

Acl::checkOrForward('IMAGE_ADMIN');

if(!extension_loaded('imagick')) {
    throw new Exception("Imagick isn't installed in this server, if you are in a Debian based system please installa php5-imagick package");
}

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING, array('options' => array(
    'default' => filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING, array('options' => array('default' => 'statistics'))
))));
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT, array('options' => array('default' => 0)));

$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);
$path_upload = MEDIA_PATH.DS.IMG_DIR.DS;
$img_url     = MEDIA_URL.SS.MEDIA_DIR.SS.IMG_DIR.SS;

$tpl->assign('MEDIA_IMG_URL',  $img_url );

$ccm = ContentCategoryManager::get_instance();
$category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
$contentType = Content::getIDContentType('album');
list($parentCategories, $subcat, $datos_cat) = $ccm->getArraysMenu($category,$contentType);
$tpl->assign('subcat', $subcat);
$tpl->assign('allcategorys', $parentCategories);
$tpl->assign('datos_cat', $datos_cat);


if (
    $category != 'GLOBAL'
    && $category != 0
    && array_key_exists($category, $ccm->categories)
) {
    $category_name = $ccm->categories[$category]->name;
}

switch($action) {

    case 'statistics':

        unset($_SESSION['where']);

        $ccm = ContentCategoryManager::get_instance();
        $nameCat = 'GLOBAL'; //Se mete en litter pq category 0
        $fullcat = $ccm->order_by_posmenu($ccm->categories);
        $photoSet = $ccm->data_media_by_type_group('media_type="image"');
        $photoSetJPG = $ccm->countMediaByTypeGroup('media_type="image" and type_img="jpg"');
        $photoSetGIF = $ccm->countMediaByTypeGroup('media_type="image" and type_img="gif"');
        $photoSetPNG = $ccm->countMediaByTypeGroup('media_type="image" and type_img="png"');
        $photoSetBN = $ccm->countMediaByTypeGroup('media_type="image" and color="BN"');

        $statistics = array(
            'num_photos' => array('jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0, 'bn' => 0, 'color' => 0, 'size' => 0),
            'num_sub_photos' => array('jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0, 'bn' => 0, 'color' => 0, 'size' => 0),
            'num_especials' => array('jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0, 'bn' => 0, 'color' => 0, 'size' => 0),
            'totals' =>  array('jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0, 'bn' => 0, 'color' => 0, 'size' => 0),
        );

        $num_sub_photos = array();
        foreach($parentCategories as $k => $v) {
            if(isset($photoSet[$v->pk_content_category])) {
                $num_photos[$k] = $photoSet[$v->pk_content_category];
                $num_photos[$k]->jpg = (isset($photoSetJPG[$v->pk_content_category]))? $photoSetJPG[$v->pk_content_category] : 0;
                $num_photos[$k]->gif = (isset($photoSetGIF[$v->pk_content_category]))? $photoSetGIF[$v->pk_content_category] : 0;
                $num_photos[$k]->png = (isset($photoSetPNG[$v->pk_content_category]))? $photoSetPNG[$v->pk_content_category] : 0;
                $num_photos[$k]->other = $photoSet[$v->pk_content_category]->total - $num_photos[$k]->jpg - $num_photos[$k]->gif - $num_photos[$k]->png;
                $num_photos[$k]->BN = (isset($photoSetBN[$v->pk_content_category]))? $photoSetBN[$v->pk_content_category] : 0;
                $num_photos[$k]->color = $photoSet[$v->pk_content_category]->total - $num_photos[$k]->BN;
                // TOTALES
                $statistics['num_photos']['jpg']   += $num_photos[$k]->jpg;
                $statistics['num_photos']['gif']   += $num_photos[$k]->gif;
                $statistics['num_photos']['png']   += $num_photos[$k]->png;
                $statistics['num_photos']['other'] += $num_photos[$k]->other;
                $statistics['num_photos']['bn']    += $num_photos[$k]->BN;
                $statistics['num_photos']['color'] += $num_photos[$k]->color;
                $statistics['num_photos']['size'] += $num_photos[$k]->size;
            }

            $j=0;
            foreach($fullcat as $child) {
                if(($v->pk_content_category == $child->fk_content_category) &&
                    isset($photoSet[$child->pk_content_category])
                ) {
                    $num_sub_photos[$k][$j] = $photoSet[$child->pk_content_category];
                    $num_sub_photos[$k][$j]->jpg = (isset($photoSetJPG[$child->pk_content_category]))? $photoSetJPG[$child->pk_content_category] : 0;

                    $num_sub_photos[$k][$j]->gif = (isset($photoSetGIF[$child->pk_content_category]))? $photoSetGIF[$child->pk_content_category] : 0;
                    $num_sub_photos[$k][$j]->png = (isset($photoSetPNG[$child->pk_content_category]))? $photoSetPNG[$child->pk_content_category] : 0;
                    $num_sub_photos[$k][$j]->other = $photoSet[$child->pk_content_category]->total - $num_sub_photos[$k][$j]->jpg  - $num_sub_photos[$k][$j]->gif  - $num_sub_photos[$k][$j]->png ;
                    $num_sub_photos[$k][$j]->BN = (isset($photoSetBN[$child->pk_content_category]))? $photoSetBN[$child->pk_content_category] : 0;
                    $num_sub_photos[$k][$j]->color = $photoSet[$child->pk_content_category]->total - $num_sub_photos[$k][$j]->BN ;
                    // TOTALES

                    $statistics['num_sub_photos']['jpg']   += $num_sub_photos[$k][$j]->jpg;
                    $statistics['num_sub_photos']['gif']   += $num_sub_photos[$k][$j]->gif;
                    $statistics['num_sub_photos']['png']   += $num_sub_photos[$k][$j]->png;
                    $statistics['num_sub_photos']['other'] += $num_sub_photos[$k][$j]->other;
                    $statistics['num_sub_photos']['bn']    += $num_sub_photos[$k][$j]->BN;
                    $statistics['num_sub_photos']['color'] += $num_sub_photos[$k][$j]->color;
                    $statistics['num_sub_photos']['size']   += $num_sub_photos[$k][$j]->size;

                    $j++;
                }
            }
        }

        //Categorias especiales
        $j = 0;

        // FIXME: eliminar as dependencias xeradas por un mal
        // Eliminada categoria album del array $especials:  3 => 'album'
        $especials = array(2 => _('Advertisement'));
        foreach($especials as $key=>$cat) {
            $num_especials[$j] =  new stdClass;
            $num_especials[$j]->id = $key;
            $num_especials[$j]->title = $cat;
            $num_especials[$j]->total = (isset($photoSet[$key]->total))? $photoSet[$key]->total : 0;
            $num_especials[$j]->size = (isset($photoSet[$key]->size))? $photoSet[$key]->size : 0;
            $num_especials[$j]->jpg = (isset($photoSetJPG[$key]))? $photoSetJPG[$key] : 0;
            $num_especials[$j]->gif = (isset($photoSetGIF[$key]))? $photoSetGIF[$key] : 0;
            $num_especials[$j]->png = (isset($photoSetPNG[$key]))? $photoSetPNG[$key] : 0;
            $num_especials[$j]->other = $photoSet[$key]->total - $num_especials[$j]->jpg  - $num_especials[$j]->gif  - $num_especials[$j]->png  ;
            $num_especials[$j]->BN = (isset($photoSetBN[$key]))? $photoSetBN[$key] : 0;
            $num_especials[$j]->color = $photoSet[$key]->total - $num_especials[$j]->BN ;

            // TOTALES
            $statistics['num_especials']['jpg']   += $num_especials[$j]->jpg;
            $statistics['num_especials']['gif']   += $num_especials[$j]->gif;
            $statistics['num_especials']['png']   += $num_especials[$j]->png;
            $statistics['num_especials']['other'] += $num_especials[$j]->other;
            $statistics['num_especials']['bn']    += $num_especials[$j]->BN;
            $statistics['num_especials']['color'] += $num_especials[$j]->color;
            $statistics['num_especials']['size']  += $num_especials[$j]->size;

            $j++;
        }

        $photoFileTypes = array('jpg', 'gif', 'png', 'other', 'bn', 'color', 'size');
        foreach ($photoFileTypes as $type) {
            $typeUpper = strtoupper($type);
            $statistics['totals'][$type] = $statistics['num_photos'][$type]  + $statistics['num_sub_photos'][$type]
                + $statistics['num_especials'][$type];
        }
        $statistics['totals']['total'] = $statistics['totals']['jpg']
            + $statistics['totals']['gif'] + $statistics['totals']['png']
            + $statistics['totals']['other'];

        $tpl->assign('totals', $statistics['totals']);

        $tpl->assign('categorys', $parentCategories);
        $tpl->assign('subcategorys', $subcat);
        $tpl->assign('num_photos', $num_photos);
        $tpl->assign('num_sub_photos', $num_sub_photos);
        $tpl->assign('especials', $especials);
        $tpl->assign('num_especials', $num_especials);
        $tpl->assign('category', $category);
        $tpl->display('image/statistics.tpl');

        $_SESSION['desde'] = 'statistics';

        break;

    case 'category_catalog':

        $cm = new ContentManager();

        list($photos, $pager) = $cm->find_pages(
            'Photo', 'contents.fk_content_type=8 and photos.media_type="image"',
            'ORDER BY  created DESC ', $page, 40, $category
        );

        foreach ($photos as &$photo) {
            $extension = strtolower($photo->type_img);
            $photo->description_utf = html_entity_decode(($photo->description));
            $photo->metadata_utf = html_entity_decode($photo->metadata);
        }

        $_SESSION['desde'] = 'category_catalog';
        $tpl->assign('paginacion', $pager);
        $tpl->assign('photos', $photos);
        $tpl->assign('category', $category);
        $tpl->display('image/category_catalog.tpl');

        break;

    case 'today_catalog':

        $cm = new ContentManager();

        list($photos, $pager)= $cm->find_pages(
            'Photo',
            'contents.fk_content_type=8 and photos.media_type="image" and created >=' .
            'DATE_SUB(CURDATE(), INTERVAL 1 DAY)'.' ',
            'ORDER BY created DESC ',
            $page, 40, $category
        );

        foreach ($photos as &$photo) {
            $photo->description_utf = html_entity_decode(($photo->description));
            $photo->metadata_utf = html_entity_decode($photo->metadata);
            $extension = strtolower($photo->type_img);
        }

        $_SESSION['desde'] = 'today_catalog';
        $tpl->assign('paginacion', $pager);
        $tpl->assign('photos', $photos);
        $tpl->assign('action', $action);
        $tpl->assign('category', $category);
        $tpl->display('image/today_catalog.tpl');

        break;

    case 'search':

        $searchStringRAW = filter_input(INPUT_GET, 'string_search');
        // If the form was not completed show the form
        if (empty($searchStringRAW)) {

            $tpl->assign('action', $action);
            $tpl->assign('category', $category);
            $tpl->display('image/search.tpl');

        } else {

            $cm = new ContentManager();
            $search = "";

            $items_page = 18;

            $searchCriteria['category']  = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['maxWidth']  = filter_input(INPUT_GET, 'max_width', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['minWidth']  = filter_input(INPUT_GET, 'min_width', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['maxHeight'] = filter_input(INPUT_GET, 'max_height', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['minHeight'] = filter_input(INPUT_GET, 'min_height', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['maxWeight'] = filter_input(INPUT_GET, 'max_weight', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['minWeight'] = filter_input(INPUT_GET, 'min_weight', FILTER_SANITIZE_NUMBER_INT);
            $searchCriteria['type']      = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
            $searchCriteria['color']     = filter_input(INPUT_GET, 'color', FILTER_SANITIZE_STRING);
            $searchCriteria['author']    = filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING);
            $searchCriteria['starttime'] = filter_input(INPUT_GET, 'starttime', FILTER_SANITIZE_STRING);
            $searchCriteria['endtime']   = filter_input(INPUT_GET, 'endtime', FILTER_SANITIZE_STRING);

            $sqlWhere    = array();

            // If search string was provided split it by tokens and build the LIKE based SQL
            if (!empty($searchStringRAW)) {
                $searchString    = preg_split('/[\s\,]+/', $searchStringRAW);
                $searchStringSQL = '';
                foreach ($searchString as &$token) {
                    $token = addslashes($token);
                    $token = "%{$token}%";
                }
                $searchStringSQL = implode('" OR `contents`.`metadata` LIKE "', $searchString);
                $sqlWhere        []= "`contents`.`metadata` LIKE '{$searchStringSQL}'";
            }

            if (!empty($searchCriteria['category']) && $category !='all' ) {
                $sqlWhere []= 'contents.fk_category'.$category;
            }
            if (!empty($searchCriteria['author'])) {
                $sqlWhere []= '`photos`.`author_name` LIKE \'%'.addslashes($searchCriteria['author']).'%\'' ;
            }
            if (!empty($searchCriteria['maxWidth'])) {
                $sqlWhere []= '`photos`.`width` <= "'.addslashes($searchCriteria['maxWidth']).'"' ;
            }
            if (!empty($searchCriteria['minWidth'])) {
                $sqlWhere []= '`photos`.`width` >= "'.addslashes($searchCriteria['minWidth']).'"' ;
            }
            if (!empty($searchCriteria['maxHeight'])) {
                $sqlWhere []= '`photos`.`height` <= "'.addslashes($searchCriteria['maxHeight']).'"' ;
            }
            if (!empty($searchCriteria['minHeight'])) {
                $sqlWhere []= '`photos`.`height` >= "'.addslashes($searchCriteria['minHeight']).'"' ;
            }
            if (!empty($searchCriteria['endtime'])) {
                $sqlWhere []= '`photos`.`date` <= "'.addslashes($searchCriteria['endtime']).'"' ;
            }
            if (!empty($searchCriteria['starttime'])) {
                $sqlWhere []= '`photos`.`date` >= "'.addslashes($searchCriteria['starttime']).'"' ;
            }
            if (!empty($searchCriteria['minWeight'])) {
                $sqlWhere []= '`photos`.`size` <= "'.addslashes($searchCriteria['maxWeight']).'" ' ;
            }
            if (!empty($searchCriteria['minWeight'])) {
                $sqlWhere []= '`photos`.`size` >= "'.addslashes($searchCriteria['minWeight']).'"' ;
            }
            if (!empty($searchCriteria['tipo'])) {
                $sqlWhere []= '`photos`.`type_img` = "'.addslashes($searchCriteria['type']).'"' ;
            }
            if (!empty($searchCriteria['color'])) {
                $sqlWhere []= '`photos`.`color` = "'.addslashes($searchCriteria['color']).'"' ;
            }

            $sqlWhere = implode(' AND ', $sqlWhere);

            list($photos, $pager) = $cm->find_pages(
                'Photo', 'contents.fk_content_type=8 and photos.media_type="image" AND '.$sqlWhere,
                'ORDER BY  created DESC ', $page, $items_page, $category
            );

            foreach ($photos as &$photo) {
                $extension              = strtolower($photo->type_img);
                $photo->description_utf = html_entity_decode($photo->description);
                $photo->metadata_utf    = html_entity_decode($photo->metadata);
            }

            $pages = Pager::factory(array(
                'mode'        => 'Sliding',
                'perPage'     => $items_page,
                'append'      => false,
                'path'        => '',
                'fileName'    => $_SERVER['REQUEST_URI'].'&page=%d',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $pager->_totalItems,
            ));

            $_SESSION['desde'] = 'search';
            $tpl->assign('photos', $photos);
            $tpl->assign('search_criteria', $searchCriteria);
            $tpl->assign('search_string', $searchStringRAW);
            $tpl->assign('paginacion', $pager);
            $tpl->assign('search', $search);
            $tpl->assign('pages', $pages);

            $tpl->assign('category', $category);
            $tpl->display('image/search.tpl');
        }

        break;

    case 'config':

        if (isset($_POST['submit'])) {

            unset($_POST['action']);
            unset($_POST['submit']);

            foreach ($_POST as $key => $value ) {
                s::set($key, $value);
            }

            m::add(_('Image module settings saved successfully.'), m::SUCCESS);
            Application::forward($_SERVER['SCRIPT_NAME']);

        } else {
            $configurationsKeys = array(
                'image_thumb_size',
                'image_inner_thumb_size',
                'image_front_thumb_size',
            );

            $configurations = s::get($configurationsKeys);

            $tpl->assign(array(
                'configs'   => $configurations,
            ));

            $tpl->display('image/config.tpl');
        }

        break;

    case 'delete':

        $forceDelete = filter_input(INPUT_GET, 'force');
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $foto = new Photo($id);
        $contents = $foto->is_used($id);

        if (count($contents) > 0 && ($forceDelete != 'yes')) {
            $cm = new ContentManager();
            $related_contents = $cm->getContents($contents);
            $tpl->assign('related_contents', $related_contents);
            $tpl->assign('foto', $foto);
            $tpl->assign('id',$id);
            $tpl->assign('form_action', $_SERVER['REQUEST_URI']);
            $tpl->display('image/delete_relations.tpl');
        } else {
            $foto->delete($id, $_SESSION['userid']);
            Application::forward(
                $_SERVER['SCRIPT_NAME'] . '?action=' . $_SESSION['desde']
                .'&category=' . $foto->category . '&page=' . $page
            );
        }
        break;

    case 'create':

        $tpl->assign('action', $action);
        $tpl->assign('category', $category);
        $tpl->display('image/create.tpl');
        break;

    case 'create_improved':

        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
        if (empty($category) || !array_key_exists($category, $ccm->categories)) {
            m::add(_('Please provide a valid category for upload images.'));
            Application::forward(
                $_SERVER['SCRIPT_NAME'] . '?action=today_catalog'
                .'&category=' . $category
            );
        }

        $maxUpload = (int)(ini_get('upload_max_filesize'));
        $maxPost = (int)(ini_get('post_max_size'));
        $memoryLimit = (int)(ini_get('memory_limit'));
        $maxAllowedSize = min($maxUpload, $maxPost, $memoryLimit);

        $tpl->assign('action', $action);
        $tpl->assign('category', $category);
        $tpl->assign('max_allowed_size', $maxAllowedSize);
        $tpl->display('image/create_improved.tpl');
        break;

    case 'show':

        $photos = array();
        foreach ($_GET['id'] as $id) {
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            if (!empty($id)) {
                $photo = new Photo($id);
                if (!empty($photo->id)) {
                    $photos []= $photo->read_alldata($id);
                    $image_url = $path_upload . $photo->path_file . $photo->name;
                }
            }
        }
        if (count($photos) <= 0) {
            m::add(_('Unable to find any photo with that id'));
            Application::forward(
                $_SERVER['SCRIPT_NAME'] . '?action=today_catalog&category=' .
                $_SESSION['category'] . '&page=' . $page
            );
        }

        // For redirectintg the user to the proper category when he save the photo data
        $_SESSION['category'] = $category;

        $tpl->assign('photos', $photos);
        $tpl->assign('MEDIA_IMG_URL',  $img_url);
        $tpl->display('image/show.tpl');

        break;

    case 'validate':
    case 'update':

        $photosRAW = $_POST['description'];

        $photos = $ids = array();
        foreach ($photosRAW as $id => $value) {
            $ids []=  $id;
            $photos[$id] = array(
                'id' => filter_var($id, FILTER_SANITIZE_STRING),
                'title' => filter_var($_POST['title'][$id], FILTER_SANITIZE_STRING),
                'description' => filter_var($_POST['description'][$id], FILTER_SANITIZE_STRING),
                'metadata' => filter_var($_POST['metadata'][$id], FILTER_SANITIZE_STRING),
                'author_name' => filter_var($_POST['author_name'][$id], FILTER_SANITIZE_STRING),
                'date' => filter_var($_POST['date'][$id], FILTER_SANITIZE_STRING),
                'color' => filter_var($_POST['color'][$id], FILTER_SANITIZE_STRING),
                'address' => filter_var($_POST['address'][$id], FILTER_SANITIZE_STRING),
                'category' => filter_var($_POST['category'][$id], FILTER_SANITIZE_STRING),
            );
        }

        $photosSaved = 0;
        foreach ($photos as $id => $photoData) {
            $photo = new Photo($id);

            if ($photo->set_data($photoData)) {
                $photosSaved++;
            }
        }
        m::add(sprintf(_('Data successfully saved for %d photos'), $photosSaved));
        $queryIDs = implode('&id[]=', $ids);
        if ($action == 'validate') {
            Application::forward(
                $_SERVER['SCRIPT_NAME'] . '?action=show' .
                '&id[]=' . $queryIDs
            );
        } else {
            Application::forward(
                $_SERVER['SCRIPT_NAME'] . '?action=today_catalog&category=' .
                $_SESSION['category'] . '&page=' . $page
            );
        }
        $page = (isset($_REQUEST['page']))? $_REQUEST['page']: 0;
        break;

    case 'upload_photos':

        // check if category, and filesizes are properly setted and category_name is valid
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        if (empty($category) || !array_key_exists($category, $ccm->categories)) {
            m::add(_('Please provide a valid category for upload images.'));
            Application::forward(
                $_SERVER['SCRIPT_NAME'] . '?action=today_catalog'
                .'&category=' . $category
            );
        }
        $category_name = $ccm->categories[$category]->name;

        $fileSizesSettings = s::get(array(
            'image_thumb_size',
            'image_inner_thumb_size',
            'image_front_thumb_size',
        ));

        if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {

            $fallos = "";
            $uploads = array();
            for ($i=0; $i < count($_FILES["file"]["name"]); $i++) {

                $originalfileName = $_FILES["file"]["name"][$i];

                if (!empty($originalfileName)) {
                     // Check upload directory
                    $dirDate = date("/Y/m/d/");
                    $uploadDir = $path_upload.$dirDate ;

                    if(!is_dir($uploadDir)) { FilesManager::createDirectory($uploadDir); }

                    // Generation of the new name (format YYYYMMDDHHMMSSmmmmmm)
                    $datos = pathinfo($originalfileName);
                    $extension = strtolower($datos['extension']);
                    $t = gettimeofday(); //Sacamos los microsegundos
                    $micro = intval(substr($t['usec'], 0, 5)); //Le damos formato de 5digitos a los microsegundos
                    $targetFileName = date("YmdHis") . $micro . "." . $extension;

                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploadDir.$targetFileName)) {

                        // Getting information from photo file
                        $imageInformation  = new MediaItem( $uploadDir.$targetFileName );

                        $data = array(
                            'title'        => $originalfileName,
                            'name'         => $targetFileName,
                            'path_file'    => $dirDate,
                            'fk_category'  => $category,
                            'category'     => $category,
                            'nameCat'      => $category_name,
                            'created'      => $imageInformation->atime,
                            'changed'      => $imageInformation->mtime,
                            'date'         => $imageInformation->mtime,
                            'size'         => round($imageInformation->size/1024, 2),
                            'width'        => $imageInformation->width,
                            'height'       => $imageInformation->height,
                            'type_img'     => $extension,
                            'media_type'   => 'image',
                            'author_name'  => '',
                            'pk_author'    => $_SESSION['userid'],
                            'fk_publisher' => $_SESSION['userid'],
                            'description'  => '',
                            'metadata'     => '',
                        );

                        $photo = new Photo();
                        $photoID = $photo->create($data);

                        if ($photoID) {
                            if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)) {
                                // miniatura
                                $thumb = new Imagick($uploadDir.$targetFileName);

                                //ARTICLE INNER
                                $thumb->thumbnailImage($fileSizesSettings['image_inner_thumb_size']['width'], $fileSizesSettings['image_inner_thumb_size']['height'], true);
                                $thumb->writeImage($uploadDir . $fileSizesSettings['image_inner_thumb_size']['width'] . '-' . $fileSizesSettings['image_inner_thumb_size']['height'] . '-' . $targetFileName);

                                //FRONTPAGE
                                $thumb->thumbnailImage($fileSizesSettings['image_front_thumb_size']['width'], $fileSizesSettings['image_front_thumb_size']['height'], true);
                                $thumb->writeImage($uploadDir . $fileSizesSettings['image_front_thumb_size']['width'] . '-' . $fileSizesSettings['image_front_thumb_size']['height'] . '-' . $targetFileName);

                                //THUMBNAIL
                                $thumb->thumbnailImage($fileSizesSettings['image_thumb_size']['width'], $fileSizesSettings['image_thumb_size']['height'], true);
                                $thumb->writeImage($uploadDir . $fileSizesSettings['image_thumb_size']['width'] . '-' . $fileSizesSettings['image_thumb_size']['height'] . '-' . $targetFileName);
                            }
                        }

                        $uploads[] = $photoID;

                    } else {

                        $fallos .= " '" . $nameFile . "' ";
                    }
                } //if empty
            } //for

            $uploads = implode('&id[]=',  $uploads);

            if ($uploads){
                Application::forward(
                    $_SERVER['SCRIPT_NAME'] . '?action=show'
                    .'&id[]='.$uploads
                );
            }
        }

        Application::forward(
            $_SERVER['SCRIPT_NAME'] . '?action=today_catalog'
            .'&category=' . $category
            .'&page=' . $page
        );
        break;

    case 'upload_photos_improved':


        header('Pragma: no-cache');
        header('Cache-Control: private, no-cache');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'HEAD':
            case 'GET':
                $array = array();
                echo json_encode($array);
                break;
            case 'POST':

                // check if category, and filesizes are properly setted and category_name is valid
                $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
                if (empty($category) || !array_key_exists($category, $ccm->categories)) {
                    // Raise an error with json
                }
                $category_name = $ccm->categories[$category]->name;

                $fileSizesSettings = s::get(array(
                    'image_thumb_size',
                    'image_inner_thumb_size',
                    'image_front_thumb_size',
                ));

                $upload = isset($_FILES['files']) ? $_FILES['files'] : null;
                $info = array();

                $photo = new Photo();
                if ($upload && is_array($upload['tmp_name'])) {
                    foreach ($upload['tmp_name'] as $index => $value) {
                        $data = array(
                            'local_file'        => $upload['tmp_name'][$index],
                            'original_filename' => $upload['name'][$index],
                            'title'             => '',
                            'fk_category'       => $category,
                            'category'          => $category,
                            'category_name'     => $category_name,
                            'description'       => '',
                            'metadata'          => '',
                        );
                        try {
                            $photo = new Photo();
                            $photo = $photo->createFromLocalFileAjax($data);
                            $info [] = array(
                                'id'            => $photo->id,
                                'name'          => $photo->name,
                                'url'           => $_SERVER['PHP_SELF']."?action=show&id[]=".$photo->id,
                                'thumbnail_url' => $img_url.$photo->path_file."/".$fileSizesSettings['image_thumb_size']['width']."-".$fileSizesSettings['image_thumb_size']['height']."-".$photo->name,
                                'size'          => $photo->size,
                                'type'          => isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                                'error'         => '',
                                'delete_url'    => '',
                                "delete_type"   => "DELETE",
                            );
                        } catch (Exception $e) {
                            $info [] = array(
                                'error'         => $e->getMessage(),
                            );
                        }
                    }
                } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
                    $data = array(
                        'local_file'    => $upload['tmp_name'],
                        'original_filename' => $upload['name'][$index],
                        'title'         => '',
                        'fk_category'   => $category,
                        'category'      => $category,
                        'category_name' => $category_name,
                        'description'   => '',
                        'metadata'      => '',
                    );

                    try {

                    $photo = new Photo();
                    $photo = $photo->createFromLocalFileAjax($data);

                        $info [] = array(
                            'id'            => $photo->id,
                            'name'          => $photo->name,
                            'url'           => $_SERVER['PHP_SELF']."?action=show&id[]=".$photo->id,
                            'thumbnail_url' => $img_url.$photo->path_file."/".$fileSizesSettings['image_thumb_size']['width']."-".$fileSizesSettings['image_thumb_size']['height']."-".$photo->name,
                            'size'          => $photo->size,
                            'type'          => isset($_SERVER['HTTP_X_FILE_TYPE']) ? $_SERVER['HTTP_X_FILE_TYPE'] : $upload['type'][$index],
                            'error'         => '',
                            'delete_url'    => '',
                            "delete_type"   => "DELETE",
                        );
                    } catch (Exception $e) {
                        $info [] = array(
                            'error'         => $e->getMessage(),
                        );
                    }
                }

                header('Vary: Accept');
                $json = json_encode($info);
                $redirect = isset($_REQUEST['redirect']) ?
                    stripslashes($_REQUEST['redirect']) : null;
                if ($redirect) {
                    header('Location: '.sprintf($redirect, rawurlencode($json)));
                    return;
                }
                if (isset($_SERVER['HTTP_ACCEPT']) &&
                    (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                    header('Content-type: application/json');
                } else {
                    header('Content-type: text/plain');
                }
                echo $json;
                break;

            case 'DELETE':
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
        die();

        if (count($_FILES["file"]["name"]) >= 1 && !empty($_FILES["file"]["name"][0]) ) {

            $fallos = "";
            $uploads = array();
            for ($i=0; $i < count($_FILES["file"]["name"]); $i++) {

                $originalfileName = $_FILES["file"]["name"][$i];

                if (!empty($originalfileName)) {
                     // Check upload directory
                    $dirDate = date("/Y/m/d/");
                    $uploadDir = $path_upload.$dirDate ;

                    if(!is_dir($uploadDir)) { FilesManager::createDirectory($uploadDir); }

                    // Generation of the new name (format YYYYMMDDHHMMSSmmmmmm)
                    $datos = pathinfo($originalfileName);
                    $extension = strtolower($datos['extension']);
                    $t = gettimeofday(); //Sacamos los microsegundos
                    $micro = intval(substr($t['usec'], 0, 5)); //Le damos formato de 5digitos a los microsegundos
                    $targetFileName = date("YmdHis") . $micro . "." . $extension;

                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $uploadDir.$targetFileName)) {

                        // Getting information from photo file
                        $imageInformation  = new MediaItem( $uploadDir.$targetFileName );

                        $data = array(
                            'title'        => $originalfileName,
                            'name'         => $targetFileName,
                            'path_file'    => $dirDate,
                            'fk_category'  => $category,
                            'category'     => $category,
                            'nameCat'      => $category_name,
                            'created'      => $imageInformation->atime,
                            'changed'      => $imageInformation->mtime,
                            'date'         => $imageInformation->mtime,
                            'size'         => round($imageInformation->size/1024, 2),
                            'width'        => $imageInformation->width,
                            'height'       => $imageInformation->height,
                            'type_img'     => $extension,
                            'media_type'   => 'image',
                            'author_name'  => '',
                            'pk_author'    => $_SESSION['userid'],
                            'fk_publisher' => $_SESSION['userid'],
                            'description'  => '',
                            'metadata'     => '',
                        );

                        $photo = new Photo();
                        $photoID = $photo->create($data);

                        if ($photoID) {
                            if(preg_match('/^(jpeg|jpg|gif|png)$/', $extension)) {
                                // miniatura
                                $thumb = new Imagick($uploadDir.$targetFileName);

                                //ARTICLE INNER
                                $thumb->thumbnailImage($fileSizesSettings['image_inner_thumb_size']['width'], $fileSizesSettings['image_inner_thumb_size']['height'], true);
                                $thumb->writeImage($uploadDir . $fileSizesSettings['image_inner_thumb_size']['width'] . '-' . $fileSizesSettings['image_inner_thumb_size']['height'] . '-' . $targetFileName);

                                //FRONTPAGE
                                $thumb->thumbnailImage($fileSizesSettings['image_front_thumb_size']['width'], $fileSizesSettings['image_front_thumb_size']['height'], true);
                                $thumb->writeImage($uploadDir . $fileSizesSettings['image_front_thumb_size']['width'] . '-' . $fileSizesSettings['image_front_thumb_size']['height'] . '-' . $targetFileName);

                                //THUMBNAIL
                                $thumb->thumbnailImage($fileSizesSettings['image_thumb_size']['width'], $fileSizesSettings['image_thumb_size']['height'], true);
                                $thumb->writeImage($uploadDir . $fileSizesSettings['image_thumb_size']['width'] . '-' . $fileSizesSettings['image_thumb_size']['height'] . '-' . $targetFileName);
                            }
                        }

                        $uploads[] = $photoID;

                    } else {

                        $fallos .= " '" . $nameFile . "' ";
                    }
                } //if empty
            } //for

        }
        break;

    // Must be rewritten with jQuery Modal boxes as current js is a mess
    case 'mdelete':
        $msg="Las photos ";
        if($_REQUEST['id']==6 && isset($_SESSION['cat'])){ //Eliminar todos
            $cm = new ContentManager();
            $photos = $cm->find_by_category('Photo', $_SESSION['cat'] , 'fk_content_type=8 AND   photos.media_type="image"', 'ORDER BY created DESC');

            if(count($photos)>0){
                foreach ($photos as $art){
                    $photo = new Photo($art->id);
                    $photo->delete($art->id,$_SESSION['userid'] );
                }
            }


            Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$_SESSION['cat'].'&page='.$_REQUEST['page']);
        }else{
            $fields = $_REQUEST['selected_fld'];
            if(isset($fields) && count($fields)>0) {
                $nodels=array();
                $alert='';
                if(is_array($fields)) {
                    foreach($fields as $i ) {
                        $photo = new Photo($i);
                        $photo->delete($i,$_SESSION['userid'] );
                    }

                }
            }
        }

        $msg.=" tiene relacionados.  !Eliminelos uno a uno!";

        Application::forward($_SERVER['SCRIPT_NAME'].'?action='.$_SESSION['desde'].'&category='.$photo->category.'&alert='.$alert.'&msg='.$msg.'&page='.$_REQUEST['page']);

        break;

}
