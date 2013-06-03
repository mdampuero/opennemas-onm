<?php
/**
 * Handles the actions for the images
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the actions for the images
 *
 * @package Backend_Controllers
 **/
class ImagesController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $request = $this->request;
        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);

        $this->ccm = \ContentCategoryManager::get_instance();
        $this->category = $request->query->filter('category', 'all', FILTER_SANITIZE_NUMBER_INT);
        $this->contentType = \ContentManager::getContentTypeIdFromName('album');
        list($this->parentCategories, $this->subcat, $this->datos_cat) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->pathUpload = MEDIA_PATH.DS.IMG_DIR.DS;
        $this->imgUrl     = MEDIA_URL.MEDIA_DIR.SS.IMG_DIR;

        $this->view->assign(
            array(
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->datos_cat,
                'MEDIA_IMG_URL' => $this->imgUrl,
            )
        );

        if ($this->category != 'GLOBAL'
            && $this->category != 0
            && array_key_exists($this->category, $this->ccm->categories)
        ) {
            $this->category_name = $this->ccm->categories[$this->category]->name;
        }
    }

    /**
     * Lists images from an specific category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $request           = $this->request;
        $page              = $request->query->getDigits('page', 1);
        $itemsPerPage      = s::get('items_per_page', 20);

        $_SESSION['desde'] = 'category_catalog';

        if ($this->category == 'all') {
            $filterCategory = null;
        } else {
            $filterCategory = $this->category;
        }
        $cm = new \ContentManager();
        list($countImages, $images) = $cm->getCountAndSlice(
            'photo',
            $filterCategory,
            'contents.in_litter != 1 AND contents.fk_content_type=8',
            'ORDER BY pk_content DESC',
            $page,
            $itemsPerPage
        );

        foreach ($images as &$image) {
            $image->category_name   = $image->loadCategoryName($image->id);
        }

        // Build the pager
        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countImages,
                'fileName'    => $this->generateUrl(
                    'admin_images',
                    array('category' => $this->category)
                ).'&page=%d',
            )
        );

        $adsModule = 'false';
        if (\Onm\Module\ModuleManager::isActivated('ADS_MANAGER')) {
            $adsModule = 'true';
        }


        return $this->render(
            'image/list.tpl',
            array(
                'pages'    => $pagination,
                'photos'   => $images,
                'category' => $this->category,
                'page'     => $page,
                'adsModule'=> $adsModule,
            )
        );
    }

    /**
     * Show statitics of disk usage in images
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function statisticsAction(Request $request)
    {
        unset($_SESSION['where']);

        $ccm = \ContentCategoryManager::get_instance();
        $fullcat     = $ccm->order_by_posmenu($ccm->categories);
        $photoSet    = $ccm->dataMediaByTypeGroup('media_type="image"');
        $photoSetJPG = $ccm->countMediaByTypeGroup('media_type="image" and type_img="jpg"');
        $photoSetGIF = $ccm->countMediaByTypeGroup('media_type="image" and type_img="gif"');
        $photoSetPNG = $ccm->countMediaByTypeGroup('media_type="image" and type_img="png"');
        $photoSetBN  = $ccm->countMediaByTypeGroup('media_type="image" and color="BN"');

        $statistics = array(
            'num_photos' => array(
                'jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0,
                'bn' => 0, 'color' => 0, 'size' => 0
            ),
            'num_sub_photos' => array(
                'jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0,
                'bn' => 0, 'color' => 0, 'size' => 0
            ),
            'num_especials' => array(
                'jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0,
                'bn' => 0, 'color' => 0, 'size' => 0
            ),
            'totals' =>  array(
                'jpg' => 0, 'gif' => 0, 'png' => 0, 'other' => 0,
                'bn' => 0, 'color' => 0, 'size' => 0
            ),
        );

        $num_sub_photos = array();
        foreach ($this->parentCategories as $k => $v) {
            if (isset($photoSet[$v->pk_content_category])) {
                $num_photos[$k]      = $photoSet[$v->pk_content_category];
                $num_photos[$k]->jpg = (isset($photoSetJPG[$v->pk_content_category]))
                                        ? $photoSetJPG[$v->pk_content_category]
                                        : 0;
                $num_photos[$k]->gif = (isset($photoSetGIF[$v->pk_content_category]))
                                        ? $photoSetGIF[$v->pk_content_category]
                                        : 0;
                $num_photos[$k]->png = (isset($photoSetPNG[$v->pk_content_category]))
                                        ? $photoSetPNG[$v->pk_content_category]
                                        : 0;
                $num_photos[$k]->other = $photoSet[$v->pk_content_category]->total
                                        - $num_photos[$k]->jpg
                                        - $num_photos[$k]->gif
                                        - $num_photos[$k]->png;
                $num_photos[$k]->BN    = (isset($photoSetBN[$v->pk_content_category]))
                                        ? $photoSetBN[$v->pk_content_category]
                                        : 0;
                $num_photos[$k]->color = $photoSet[$v->pk_content_category]->total
                                        - $num_photos[$k]->BN;
                // TOTALES
                $statistics['num_photos']['jpg']   += $num_photos[$k]->jpg;
                $statistics['num_photos']['gif']   += $num_photos[$k]->gif;
                $statistics['num_photos']['png']   += $num_photos[$k]->png;
                $statistics['num_photos']['other'] += $num_photos[$k]->other;
                $statistics['num_photos']['bn']    += $num_photos[$k]->BN;
                $statistics['num_photos']['color'] += $num_photos[$k]->color;
                $statistics['num_photos']['size']  += $num_photos[$k]->size;
            }

            $j=0;
            foreach ($fullcat as $child) {
                if ($v->pk_content_category == $child->fk_content_category
                    && isset($photoSet[$child->pk_content_category])
                ) {
                    $num_sub_photos[$k][$j] = $photoSet[$child->pk_content_category];
                    $num_sub_photos[$k][$j]->jpg = (isset($photoSetJPG[$child->pk_content_category]))
                                                    ? $photoSetJPG[$child->pk_content_category]
                                                    : 0;

                    $num_sub_photos[$k][$j]->gif = (isset($photoSetGIF[$child->pk_content_category]))
                                                    ? $photoSetGIF[$child->pk_content_category]
                                                    : 0;
                    $num_sub_photos[$k][$j]->png = (isset($photoSetPNG[$child->pk_content_category]))
                                                    ? $photoSetPNG[$child->pk_content_category]
                                                    : 0;
                    $num_sub_photos[$k][$j]->other = $photoSet[$child->pk_content_category]->total
                                                     - $num_sub_photos[$k][$j]->jpg
                                                     - $num_sub_photos[$k][$j]->gif
                                                     - $num_sub_photos[$k][$j]->png ;
                    $num_sub_photos[$k][$j]->BN    = (isset($photoSetBN[$child->pk_content_category]))
                                                    ? $photoSetBN[$child->pk_content_category]
                                                    : 0;
                    $num_sub_photos[$k][$j]->color = $photoSet[$child->pk_content_category]->total
                                                     - $num_sub_photos[$k][$j]->BN ;

                    // TOTALES
                    $statistics['num_sub_photos']['jpg']   += $num_sub_photos[$k][$j]->jpg;
                    $statistics['num_sub_photos']['gif']   += $num_sub_photos[$k][$j]->gif;
                    $statistics['num_sub_photos']['png']   += $num_sub_photos[$k][$j]->png;
                    $statistics['num_sub_photos']['other'] += $num_sub_photos[$k][$j]->other;
                    $statistics['num_sub_photos']['bn']    += $num_sub_photos[$k][$j]->BN;
                    $statistics['num_sub_photos']['color'] += $num_sub_photos[$k][$j]->color;
                    $statistics['num_sub_photos']['size']  += $num_sub_photos[$k][$j]->size;

                    $j++;
                }
            }
        }

        //Categorias especiales
        $j = 0;

        // FIXME: eliminar as dependencias xeradas por un mal
        // Eliminada categoria album del array $especials:  3 => 'album'
        $especials = null;
        $num_especials = null;
        if (\Onm\Module\ModuleManager::isActivated('ADS_MANAGER')) {
            $especials = array(2 => _('Advertisement'));
            foreach ($especials as $key => $cat) {
                $num_especials[$j] =  new \stdClass;
                $num_especials[$j]->id    = $key;
                $num_especials[$j]->title = $cat;
                $num_especials[$j]->total = (isset($photoSet[$key]->total))? $photoSet[$key]->total : 0;
                $num_especials[$j]->size  = (isset($photoSet[$key]->size))? $photoSet[$key]->size : 0;
                $num_especials[$j]->jpg   = (isset($photoSetJPG[$key]))? $photoSetJPG[$key] : 0;
                $num_especials[$j]->gif   = (isset($photoSetGIF[$key]))? $photoSetGIF[$key] : 0;
                $num_especials[$j]->png   = (isset($photoSetPNG[$key]))? $photoSetPNG[$key] : 0;
                $num_especials[$j]->other = $photoSet[$key]->total
                                            - $num_especials[$j]->jpg
                                            - $num_especials[$j]->gif
                                            - $num_especials[$j]->png;
                $num_especials[$j]->BN    = (isset($photoSetBN[$key]))? $photoSetBN[$key] : 0;
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
        }
        $photoFileTypes = array('jpg', 'gif', 'png', 'other', 'bn', 'color', 'size');
        foreach ($photoFileTypes as $type) {
            $statistics['totals'][$type] = $statistics['num_photos'][$type]  + $statistics['num_sub_photos'][$type]
                + $statistics['num_especials'][$type];
        }
        $statistics['totals']['total'] = $statistics['totals']['jpg']
            + $statistics['totals']['gif'] + $statistics['totals']['png']
            + $statistics['totals']['other'];

        return $this->render(
            'image/statistics.tpl',
            array(
                'totals'         => $statistics['totals'],
                'categorys'      => $this->parentCategories,
                'subcategorys'   => $this->subcat,
                'num_photos'     => $num_photos,
                'num_sub_photos' => $num_sub_photos,
                'especials'      => $especials,
                'num_especials'  => $num_especials,
                'category'       => 'statistics',
            )
        );
    }

    /**
     * Handles the form for configure the images module
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $configurations = array(
                'image_thumb_size'       => $request->request->get('image_thumb_size'),
                'image_inner_thumb_size' => $request->request->get('image_inner_thumb_size'),
                'image_front_thumb_size' => $request->request->get('image_front_thumb_size'),
            );

            foreach ($configurations as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Image module settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_images_config'));
        } else {
            $configurations = s::get(
                array(
                    'image_thumb_size',
                    'image_inner_thumb_size',
                    'image_front_thumb_size',
                )
            );

            return $this->render(
                'image/config.tpl',
                array(
                    'configs'   => $configurations,
                )
            );
        }
    }

    /**
     * Handles the form for searching images
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function searchAction(Request $request)
    {
        $page     = $request->query->getDigits('page', 1);
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $searchStringRAW = $request->query->filter('string_search', null, FILTER_SANITIZE_STRING);

        // If the form was not completed show the form
        if (empty($searchStringRAW)) {
            return $this->render(
                'image/search.tpl',
                array(
                    'category' => $category,
                )
            );
        } else {
            $cm     = new \ContentManager();
            $search = "";

            $itemsPerPage = s::get('items_per_page', 20);

            $searchCriteria['maxWidth']  = $request->query->getDigits('max_width', null);
            $searchCriteria['minWidth']  = $request->query->getDigits('min_width', null);
            $searchCriteria['maxHeight'] = $request->query->getDigits('max_height', null);
            $searchCriteria['minHeight'] = $request->query->getDigits('min_height', null);
            $searchCriteria['maxWeight'] = $request->query->getDigits('max_weight', null);
            $searchCriteria['minWeight'] = $request->query->getDigits('min_weight', null);
            $searchCriteria['type']      = $request->query->filter('type', '', FILTER_SANITIZE_STRING);
            $searchCriteria['color']     = $request->query->filter('color', '', FILTER_SANITIZE_STRING);
            $searchCriteria['author']    = $request->query->filter('author', '', FILTER_SANITIZE_STRING);
            $searchCriteria['starttime'] = $request->query->filter('starttime', '', FILTER_SANITIZE_STRING);
            $searchCriteria['endtime']   = $request->query->filter('endtime', '', FILTER_SANITIZE_STRING);

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
                $sqlWhere      []= "`contents`.`metadata` LIKE '{$searchStringSQL}'";
            }
            if ($category == 'all') {
                $category = null;
            }
            if (!empty($searchCriteria['maxWidth'])) {
                $sqlWhere []= '`photos`.`width` <= "'.$searchCriteria['maxWidth'].'"' ;
            }
            if (!empty($searchCriteria['minWidth'])) {
                $sqlWhere []= '`photos`.`width` >= "'.$searchCriteria['minWidth'].'"' ;
            }
            if (!empty($searchCriteria['maxHeight'])) {
                $sqlWhere []= '`photos`.`height` <= "'.$searchCriteria['maxHeight'].'"' ;
            }
            if (!empty($searchCriteria['minHeight'])) {
                $sqlWhere []= '`photos`.`height` >= "'.$searchCriteria['minHeight'].'"' ;
            }
            if (!empty($searchCriteria['author'])) {
                $sqlWhere []= '`photos`.`author_name` LIKE \'%'.addslashes($searchCriteria['author']).'%\'' ;
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

            list($countPhotos, $photos) = $cm->getCountAndSlice(
                'Photo',
                $category,
                'contents.fk_content_type=8 AND '.$sqlWhere,
                'ORDER BY  created DESC ',
                $page,
                $itemsPerPage
            );

            foreach ($photos as &$photo) {
                $photo->extension       = strtolower($photo->type_img);
                $photo->description_utf = html_entity_decode($photo->description);
                $photo->metadata_utf    = html_entity_decode($photo->metadata);
                $photo->category_name   = $photo->loadCategoryName($photo->id);
            }

            $pagination = \Pager::factory(
                array(
                    'mode'        => 'Sliding',
                    'perPage'     => $itemsPerPage,
                    'append'      => false,
                    'path'        => '',
                    'fileName'    => $this->generateUrl('admin_images_search').'?page=%d',
                    'delta'       => 4,
                    'clearIfVoid' => true,
                    'urlVar'      => 'page',
                    'totalItems'  => $countPhotos,
                )
            );

            $_SESSION['desde'] = 'search';
            $adsModule = 'false';
            if (\Onm\Module\ModuleManager::isActivated('ADS_MANAGER')) {
                $adsModule = 'true';
            }


            return $this->render(
                'image/search.tpl',
                array(
                    'photos'          => $photos,
                    'search_criteria' => $searchCriteria,
                    'search_string'   => $searchStringRAW,
                    'paginacion'      => $pagination,
                    'search'          => $search,
                    'pages'           => $pagination,
                    'category'        => $category,
                    'adsModule'       => $adsModule,
                )
            );
        }
    }

    /**
     * Show the page for upload new images
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function newAction(Request $request)
    {
        $request = $this->request;
        $category = $request->query->getDigits('category', '');

        $ccm = \ContentCategoryManager::get_instance();

        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);
        if (empty($category) || !array_key_exists($category, $ccm->categories)) {
            m::add(_('Please provide a valid category for upload images.'), m::ERROR);

            return $this->redirect(
                $this->generateUrl('admin_images', array('category' => $category,))
            );
        }

        $maxUpload      = (int) (ini_get('upload_max_filesize'));
        $maxPost        = (int) (ini_get('post_max_size'));
        $memoryLimit    = (int) (ini_get('memory_limit'));
        $maxAllowedSize = min($maxUpload, $maxPost, $memoryLimit);

        return $this->render(
            'image/create.tpl',
            array(
                'category'         => $category,
                'max_allowed_size' => $maxAllowedSize,
            )
        );
    }

    /**
     * Displays the image information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $request     = $this->request;
        $ids         = $request->query->get('id');
        $page        = $request->query->getDigits('page', 1);

        // Check if ids was passed as params
        if (!is_array($ids) || !(count($ids) > 0)) {
            m::add(_('Please provide a image id for show it.'), m::ERROR);

            return $this->redirect(
                $this->generateUrl('admin_images', array('category' => $category))
            );
        }

        $photos = array();
        foreach ($ids as $id) {
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            if (!empty($id)) {
                $photo = new \Photo($id);
                if (!empty($photo->id)) {
                    $photos []= $photo->readAllData($id);
                }
            }
        }
        // Check if passed ids fits photos in database, if not redirect to listing
        if (count($photos) <= 0) {
            m::add(_('Unable to find any photo with that id'));

            return $this->redirect(
                $this->generateUrl(
                    'admin_images',
                    array(
                        'category' => $category,
                        'page'     => $page,
                    )
                )
            );
        }

        return $this->render(
            'image/new.tpl',
            array(
                'photos'        => $photos,
                'MEDIA_IMG_URL' => $this->imgUrl,
            )
        );
    }

    /**
     * Updates the image information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $request   = $this->request;
        $photosRAW = $request->request->get('description');
        $action    = $request->request->filter('action', 'update');
        $page      = $request->request->getDigits('page', 1);

        $ids = array();
        $photosSaved = 0;
        foreach ($photosRAW as $id => $value) {
            $photoData = array(
                'id'          => filter_var($id, FILTER_SANITIZE_STRING),
                'title'       => filter_var($_POST['title'][$id], FILTER_SANITIZE_STRING),
                'description' => filter_var($_POST['description'][$id], FILTER_SANITIZE_STRING),
                'metadata'    => filter_var($_POST['metadata'][$id], FILTER_SANITIZE_STRING),
                'author_name' => filter_var($_POST['author_name'][$id], FILTER_SANITIZE_STRING),
                'date'        => filter_var($_POST['date'][$id], FILTER_SANITIZE_STRING),
                'color'       => filter_var($_POST['color'][$id], FILTER_SANITIZE_STRING),
                'address'     => filter_var($_POST['address'][$id], FILTER_SANITIZE_STRING),
                'category'    => filter_var($_POST['category'][$id], FILTER_SANITIZE_STRING),
                'available'   => 1
            );

            $photo = new \Photo($id);
            if ($photo->setData($photoData)) {
                $ids []= $id;
                $photosSaved++;
            }
        }

        if (count($ids) > 0) {
            m::add(sprintf(_('Data successfully saved for %d photos'), $photosSaved), m::SUCCESS);
        }

        if ($action == 'validate') {
            $queryIDs = implode('&id[]=', $ids);

            return $this->redirect($this->generateUrl('admin_image_show').'?id[]='.$queryIDs);
        } else {
            return $this->redirect(
                $this->generateUrl(
                    'admin_images',
                    array(
                        'category' => $_SESSION['category'],
                        'page'     => $page,
                    )
                )
            );
        }
    }

    /**
     * Deletes an image given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function deleteAction(Request $request)
    {
        $request = $this->get('request');
        $id   = $request->query->getDigits('id', null);
        $page = $request->query->getDigits('page', 1);

        $photo = new \Photo($id);
        if (is_null($photo->id)) {
            m::add(sprintf(_('Unable to find the photo with the id "%d"'), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_images'));
        }
        // $contents = $photo->isUsed($id);
        $photo->delete($id, $_SESSION['userid']);

        return $this->redirect(
            $this->generateUrl(
                'admin_images',
                array(
                    'category' => $photo->category,
                    'page'     => $page,
                )
            )
        );
    }

    /**
     * Uploads and creates
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $response = new Response();
        $response->headers->add(
            array(
                'Pragma' => 'text/plain',
                'Cache-Control' => 'private, no-cache',
                'Content-Disposition' => 'inline; filename="files.json"',
                'X-Content-Type-Options' => 'nosniff',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Methods' => 'OPTIONS, HEAD, GET, POST, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'X-File-Name, X-File-Type, X-File-Size',
            )
        );

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'HEAD':
            case 'GET':
                $array = array();
                echo json_encode($array);

                return $response;
                break;
            case 'POST':

                // check if category, and filesizes are properly setted and category_name is valid
                $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
                if (empty($category) || !array_key_exists($category, $this->ccm->categories)) {
                    // Raise an error with json
                }
                $category_name = $this->ccm->categories[$category]->name;

                $fileSizesSettings = s::get(
                    array(
                        'image_thumb_size',
                        'image_inner_thumb_size',
                        'image_front_thumb_size',
                    )
                );

                $upload = isset($_FILES['files']) ? $_FILES['files'] : null;
                $info = array();

                $photo = new \Photo();
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
                            $photo = new \Photo();
                            $photo = $photo->createFromLocalFileAjax($data);

                            $thumbnailUrl = $this->imgUrl.$photo->path_file."/"
                                            .$fileSizesSettings['image_thumb_size']['width']."-"
                                            .$fileSizesSettings['image_thumb_size']['height']
                                            ."-".$photo->name;

                            $info [] = array(
                                'id'            => $photo->id,
                                'name'          => $photo->name,
                                'url'           => $this->generateUrl('admin_image_show', array('id[]' => $photo->id)),
                                'thumbnail_url' => $thumbnailUrl,
                                'size'          => $photo->size,
                                'type'          => isset($_SERVER['HTTP_X_FILE_TYPE'])
                                                    ? $_SERVER['HTTP_X_FILE_TYPE']
                                                    : $upload['type'][$index],
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

                        $thumbnailUrl = $this->imgUrl.$photo->path_file."/"
                                        .$fileSizesSettings['image_thumb_size']['width']."-"
                                        .$fileSizesSettings['image_thumb_size']['height']
                                        ."-".$photo->name;

                        $info [] = array(
                            'id'            => $photo->id,
                            'name'          => $photo->name,
                            'url'           => $this->generateUrl('admin_image_show', array('id[]' => $photo->id)),
                            'thumbnail_url' => $thumbnailUrl,
                            'size'          => $photo->size,
                            'type'          => isset($_SERVER['HTTP_X_FILE_TYPE'])
                                                ? $_SERVER['HTTP_X_FILE_TYPE']
                                                : $upload['type'][$index],
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

                $json = json_encode($info);
                $response->setContent($json);

                $response->headers->add(array('Vary' =>'Accept'));

                $redirect = $request->request->filter('redirect', null, FILTER_SANITIZE_STRING);
                if (!empty($redirect)) {
                    return $this->redirect(sprintf($redirect, rawurlencode($json)));
                }
                if (isset($_SERVER['HTTP_ACCEPT']) &&
                    (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                    $response->headers->add(array('Content-type' => 'application/json'));
                } else {
                    $response->headers->add(array('Content-type' => 'text/plain'));
                }

                return $response;
                break;
            case 'DELETE':
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }

        return $response;
    }

    /**
     * Deletes multiple images at once
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function batchDeleteAction(Request $request)
    {

        $this->checkAclOrForward('IMAGE_DELETE');

        $request = $this->get('request');
        $category = $request->request->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page = $request->request->getDigits('page', 1);
        $selectedItems = $request->request->get('selected_fld');

        if (is_array($selectedItems)
            && count($selectedItems) > 0
        ) {
            foreach ($selectedItems as $element) {
                $photo = new \Photo($element);
                $photo->delete($element, $_SESSION['userid']);

                m::add(sprintf(_('Image "%s" deleted successfully.'), $photo->title), m::SUCCESS);
            }
        }

        return $this->redirect(
            $this->generateUrl(
                'admin_images',
                array(
                    'categoy' => $category,
                    'page'    => $page,
                )
            )
        );
    }

    /**
     * Shows a paginated list of images from a category
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function contentProviderGalleryAction(Request $request)
    {
        $metadata = $request->query->filter('metadatas', '', FILTER_SANITIZE_STRING);
        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);

        $itemsPerPage = 16;
        $numItems = $itemsPerPage + 1;

        if ($page == 1) {
            $limit    = "LIMIT {$numItems}";
        } else {
            $limit    = "LIMIT ".($page-1) * $itemsPerPage .', '.$numItems;
        }

        $cm = new \ContentManager();

        $szWhere = '';
        if (empty($metadata)) {
            $szWhere = "AND  (`metadata` LIKE '%$metadata%')";
        }

        if (empty($category)) {
            $photos = $cm->find(
                'Photo',
                'contents.fk_content_type = 8 AND photos.media_type="image" '
                .'AND contents.content_status=1 ' . $szWhere,
                'ORDER BY created DESC '.$limit
            );
        } else {
            $photos = $cm->find_by_category(
                'Photo',
                $category,
                'fk_content_type = 8 AND photos.media_type="image" AND contents.content_status=1 ' . $szWhere,
                'ORDER BY created DESC '.$limit
            );
        }

        if (empty($photos)) {
            return new Response(
                _("<div><p>Unable to find any image matching your search criteria.</p></div>")
            );
        }

        $total = count($photos);
        if ($total > $itemsPerPage) {
            array_pop($photos);
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $total,
                'url'   => $this->generateUrl(
                    'admin_images_content_provider_gallery',
                    array(
                        'category'  => $category,
                        'metadatas' => $metadata,
                    )
                )
            )
        );

        return $this->render(
            'image/image_gallery.ajax.tpl',
            array(
                'imagePager' => $pagination,
                'photos'     => $photos,
            )
        );
    }
}
