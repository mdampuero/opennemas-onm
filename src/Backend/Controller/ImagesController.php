<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * Common code for all the actions.
     */
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('IMAGE_MANAGER');

        $this->ccm         = \ContentCategoryManager::get_instance();
        $this->category    = $this->get('request')->query->filter('category', 'all', FILTER_SANITIZE_NUMBER_INT);
        $this->contentType = \ContentManager::getContentTypeIdFromName('album');
        list($this->parentCategories, $this->subcat, $this->datos_cat) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->pathUpload = MEDIA_PATH.DS.IMG_DIR.DS;
        $this->imgUrl     = MEDIA_URL.MEDIA_DIR.SS.IMG_DIR;

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'subcat'        => $this->subcat,
                'allcategorys'  => $this->parentCategories,
                'datos_cat'     => $this->datos_cat,
                'MEDIA_IMG_URL' => $this->imgUrl,
                'timezone'      => $timezone->getName()
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
     * Lists images from an specific category.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return $this->render('image/list.tpl');
    }

    /**
     * Handles the form for configure the images module.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_ADMIN')")
     */
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
     * Show the page for upload new images.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_CREATE')")
     */
    public function newAction(Request $request)
    {
        $maxUpload      = (int) (ini_get('upload_max_filesize'));
        $maxPost        = (int) (ini_get('post_max_size'));
        $memoryLimit    = (int) (ini_get('memory_limit'));
        $maxAllowedSize = min($maxUpload, $maxPost, $memoryLimit);

        return $this->render(
            'image/create.tpl',
            array(
                'max_allowed_size' => $maxAllowedSize,
            )
        );
    }

    /**
     * Displays the image information given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $ids         = $request->query->get('id');
        $page        = $request->query->getDigits('page', 1);

        // Check if ids was passed as params
        if (!is_array($ids) || !(count($ids) > 0)) {
            $ids = (int) $ids;
            if ($ids <= 0) {
                m::add(_('Please provide a image id for show it.'), m::ERROR);

                return $this->redirect(
                    $this->generateUrl('admin_images', array('category' => $category))
                );
            }
            $ids = array($ids);
        }

        $photos = array();
        foreach ($ids as $id) {
            $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
            if (!empty($id)) {
                $photo = new \Photo($id);
                $photo->getMetaData();

                $photos []= $photo;
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
     * Updates the image information given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $photosRAW = $request->request->get('description');

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
                'address'     => filter_var($_POST['address'][$id], FILTER_SANITIZE_STRING),
                'category'    => filter_var($_POST['category'][$id], FILTER_SANITIZE_STRING),
                'content_status'   => 1
            );

            $photo = new \Photo($id);

            $ids []= $id;

            if ($photo->update($photoData)) {
                $photosSaved++;
            }
        }

        if (count($ids) > 0) {
            m::add(sprintf(_('Data successfully saved for %d photos'), $photosSaved), m::SUCCESS);
        }

        $queryIDs = implode('&id[]=', $ids);

        return $this->redirect($this->generateUrl('admin_image_show').'?id[]='.$queryIDs);
    }

    /**
     * Deletes an image given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_DELETE')")
     */
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
     * Uploads and creates.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_CREATE')")
     */
    public function createAction(Request $request)
    {
        $response = new Response();
        $response->headers->add(
            array(
                'Pragma'                       => 'text/plain',
                'Cache-Control'                => 'private, no-cache',
                'Content-Disposition'          => 'inline; filename="files.json"',
                'X-Content-Type-Options'       => 'nosniff',
                'Access-Control-Allow-Origin'  => '*',
                'Access-Control-Allow-Methods' => 'OPTIONS, HEAD, GET, POST, PUT, DELETE',
                'Access-Control-Allow-Headers' => 'X-File-Name, X-File-Type, X-File-Size',
            )
        );

        switch ($request->getMethod()) {
            case 'HEAD':
            case 'GET':

                return  $response->setContent(json_encode(array()));
                break;
            case 'POST':

                // check if category, and filesizes are properly setted and category_name is valid
                $category = $request->request->getDigits('category', 0);
                if (empty($category) || !array_key_exists($category, $this->ccm->categories)) {
                    $category_name = '';
                } else {
                    $category_name = $this->ccm->categories[$category]->name;
                }

                $upload = isset($_FILES['files']) ? $_FILES['files'] : null;
                $info = array();

                $photo = new \Photo();
                if ($upload && is_array($upload['tmp_name'])) {
                    foreach ($upload['tmp_name'] as $index => $value) {

                        if (empty($upload['tmp_name'][$index])) {
                            $info [] = array(
                                'error' => _('Not valid file format or the file exceeds the max allowed file size.'),
                            );
                            continue;
                        }
                        $tempName = pathinfo($upload['name'][$index], PATHINFO_FILENAME);

                        // Check if the image has an IPTC title an use it as original title
                        $size = getimagesize($upload['tmp_name'][$index], $imageInfo);
                        if (isset($imageInfo['APP13'])) {
                            $iptc = iptcparse($imageInfo["APP13"]);
                            if (isset($iptc['2#120'])) {
                                $tempName = str_replace("\000", "", $iptc["2#120"][0]);
                            }
                        }

                        $data = array(
                            'local_file'        => $upload['tmp_name'][$index],
                            'original_filename' => $upload['name'][$index],
                            'title'             => $tempName,
                            'description'       => $tempName,
                            'fk_category'       => $category,
                            'category'          => $category,
                            'category_name'     => $category_name,
                            'metadata'          => \Onm\StringUtils::get_tags($tempName),
                        );

                        try {
                            $photo = new \Photo();
                            $photoId = $photo->createFromLocalFile($data);

                            $photo = new \Photo($photoId);

                            $info [] = array(
                                'id'            => $photo->id,
                                'name'          => $photo->name,
                                'size'          => $photo->size,
                                'error'         => '',
                                'delete_url'    => '',
                                "delete_type"   => "DELETE",
                                'type'          => isset($_SERVER['HTTP_X_FILE_TYPE'])
                                                    ? $_SERVER['HTTP_X_FILE_TYPE']
                                                    : $upload['type'][$index],
                                'url'           => $this->generateUrl('admin_image_show', array('id[]' => $photo->id)),
                                'thumbnail_url' => $this->generateUrl(
                                    'asset_image',
                                    array(
                                        'real_path'  => $this->imgUrl.$photo->path_file."/".$photo->name,
                                        'parameters' => urlencode('thumbnail,150,150'),
                                    )
                                ),
                            );
                        } catch (Exception $e) {
                            $info [] = array(
                                'error'         => $e->getMessage(),
                            );
                        }
                    }
                } elseif ($upload || isset($_SERVER['HTTP_X_FILE_NAME'])) {
                    $tempName = pathinfo($upload['name'], PATHINFO_FILENAME);

                    // Check if the image has an IPTC title an use it as original title
                    $size = getimagesize($upload['tmp_name'], $imageInfo);
                    if (isset($imageInfo['APP13'])) {
                        $iptc = iptcparse($imageInfo["APP13"]);
                        if (isset($iptc['2#120'])) {
                            $tempName = str_replace("\000", "", $iptc["2#120"][0]);
                        }
                    }

                    $data = array(
                        'local_file'        => $upload['tmp_name'],
                        'original_filename' => $upload['name'],
                        'title'             => $tempName,
                        'description'       => $tempName,
                        'fk_category'       => $category,
                        'category'          => $category,
                        'category_name'     => $category_name,
                        'metadata'          => \Onm\StringUtils::get_tags($tempName),
                    );

                    try {
                        $photo = new \Photo();
                        $photoId = $photo->createFromLocalFile($data);

                        $photo = new \Photo($photoId);

                        $info [] = array(
                            'id'            => $photo->id,
                            'name'          => $photo->name,
                            'size'          => $photo->size,
                            'error'         => '',
                            'delete_url'    => '',
                            "delete_type"   => "DELETE",
                            'url'           => $this->generateUrl('admin_image_show', array('id[]' => $photo->id)),
                            'thumbnail_url' => $this->generateUrl(
                                'asset_image',
                                array(
                                    'real_path'  => $this->imgUrl.$photo->path_file."/".$photo->name,
                                    'parameters' => urlencode('thumbnail,150,150'),
                                )
                            ),
                            'type'          => isset($_SERVER['HTTP_X_FILE_TYPE'])
                                                ? $_SERVER['HTTP_X_FILE_TYPE']
                                                : $upload['type'],
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
     * Shows a paginated list of images from a category.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('PHOTO_ADMIN')")
     */
    public function contentProviderGalleryAction(Request $request)
    {
        $metadata   = $request->query->filter('metadatas', '', FILTER_SANITIZE_STRING);
        $categoryId = $request->query->getDigits('category', 0);
        $page       = $request->query->getDigits('page', 1);

        $itemsPerPage = 16;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'photo')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        if (!empty($metadata)) {
            $tokens = \Onm\StringUtils::get_tags($metadata);
            $tokens = explode(', ', $tokens);

            $filters['metadata'] = array(array('value' => $tokens, 'operator' => 'LIKE'));
            $filters['metadata']['union'] = 'OR';
        }

        $photos      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countPhotos = $em->countBy($filters);

        if (empty($photos)) {
            return new Response(
                _("<div><p>Unable to find any image matching your search criteria.</p></div>")
            );
        }

        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $countPhotos,
                'url'   => $this->generateUrl(
                    'admin_images_content_provider_gallery',
                    array(
                        'category'  => $categoryId,
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
