<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the images
 *
 * @package Backend_Controllers
 */
class ImagesController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $this->ccm         = \ContentCategoryManager::get_instance();
        $this->category    = $this->get('request_stack')->getCurrentRequest()
            ->query->filter('category', 'all', FILTER_SANITIZE_NUMBER_INT);
        $this->contentType = \ContentManager::getContentTypeIdFromName('album');
        list($this->parentCategories, $this->subcat, $this->datos_cat) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->pathUpload = MEDIA_PATH.DS.IMG_DIR.DS;
        $this->imgUrl     = MEDIA_URL.MEDIA_DIR.SS.IMG_DIR;

        $this->view->assign([
            'subcat'        => $this->subcat,
            'allcategorys'  => $this->parentCategories,
            'datos_cat'     => $this->datos_cat,
            'MEDIA_IMG_URL' => $this->imgUrl,
        ]);

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
     * @return Response          The response object.
     *
     * @Security("hasExtension('IMAGE_MANAGER')
     *     and hasPermission('PHOTO_ADMIN')")
     */
    public function listAction()
    {
        $years = array();

        $conn = $this->get('orm.manager')->getConnection('instance');

        $results = $conn->fetchAll(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%m')) as date_month FROM contents
            WHERE fk_content_type = 8 AND created IS NOT NULL ORDER BY date_month DESC"
        );

        foreach ($results as $value) {
            $date = \DateTime::createFromFormat('Y-n', $value['date_month']);
            $fmt = new \IntlDateFormatter(CURRENT_LANGUAGE, null, null, null, null, 'MMMM');

            if (!is_null($fmt)) {
                $years[$date->format('Y')]['name'] = $date->format('Y');
                $years[$date->format('Y')]['months'][]= array(
                    'name'  => ucfirst($fmt->format($date)),
                    'value' => $value['date_month']
                );
            }
        }

        return $this->render('image/list.tpl', [ 'years' => array_values($years)]);
    }

    /**
     * Displays the image information given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('IMAGE_MANAGER')
     *     and hasPermission('PHOTO_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $ids         = $request->query->get('id');
        $page        = $request->query->getDigits('page', 1);

        // Check if ids was passed as params
        if (!is_array($ids) || !(count($ids) > 0)) {
            $ids = (int) $ids;
            if ($ids <= 0) {
                $this->get('session')->getFlashBag()->add('error', _('Please provide a image id for show it.'));

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
                $photo->getPhotoMetaData();

                if (!is_null($photo->pk_photo)) {
                    $photos []= $photo;
                }
            }
        }

        // Check if passed ids fits photos in database, if not redirect to listing
        if (count($photos) <= 0) {
            $this->get('session')->getFlashBag()->add('error', _('Unable to find any photo with that id'));

            return $this->redirect(
                $this->generateUrl('admin_images', [ 'page' => $page ])
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
     * @Security("hasExtension('IMAGE_MANAGER')
     *     and hasPermission('PHOTO_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $photosRAW = $request->request->get('description');

        $ids = array();
        $photosSaved = 0;
        foreach (array_keys($photosRAW) as $id) {
            $photoData = array(
                'id'             => filter_var($id, FILTER_SANITIZE_STRING),
                'title'          => filter_var($_POST['title'][$id], FILTER_SANITIZE_STRING),
                'description'    => filter_var($_POST['description'][$id], FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'metadata'       => \Onm\StringUtils::normalizeMetadata(filter_var($_POST['metadata'][$id], FILTER_SANITIZE_STRING)),
                'author_name'    => filter_var($_POST['author_name'][$id], FILTER_SANITIZE_STRING),
                'address'        => filter_var($_POST['address'][$id], FILTER_SANITIZE_STRING),
                'category'       => filter_var($_POST['category'][$id], FILTER_SANITIZE_STRING),
                'content_status' => 1
            );

            $photo = new \Photo($id);

            $ids []= $id;

            if ($photo->update($photoData)) {
                $photosSaved++;
            }
        }

        if (count($ids) > 0) {
            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Data successfully saved for %d photos'), $photosSaved)
            );
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
     * @Security("hasExtension('IMAGE_MANAGER')
     *     and hasPermission('PHOTO_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id   = $request->query->getDigits('id', null);
        $page = $request->query->getDigits('page', 1);

        $photo = new \Photo($id);
        if (is_null($photo->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the photo with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_images'));
        }
        // $contents = $photo->isUsed($id);
        $photo->delete($id, $this->getUser()->id);

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
     * @Security("hasExtension('IMAGE_MANAGER')
     *     and hasPermission('PHOTO_CREATE')")
     */
    public function createAction(Request $request)
    {
        $response = new Response();
        $response->headers->add([
            'Pragma'                       => 'text/plain',
            'Cache-Control'                => 'private, no-cache',
            'Content-Disposition'          => 'inline; filename="files.json"',
            'X-Content-Type-Options'       => 'nosniff',
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'OPTIONS, HEAD, GET, POST, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'X-File-Name, X-File-Type, X-File-Size',
        ]);

        switch ($request->getMethod()) {
            case 'HEAD':
            case 'GET':
                return  $response->setContent(json_encode([]));
                break;
            case 'POST':
                // check if category, and filesizes are properly setted and category_name is valid
                $category = $request->request->getDigits('category', 0);
                if (empty($category) || !array_key_exists($category, $this->ccm->categories)) {
                    $category_name = '';
                } else {
                    $category_name = $this->ccm->categories[$category]->name;
                }

                $files = isset($_FILES) ? $_FILES : null;
                $info = array();

                foreach ($files as $file) {
                    $photo = new \Photo();

                    if (empty($file['tmp_name'])) {
                        $info [] = array(
                            'error' => _('Not valid file format or the file exceeds the max allowed file size.'),
                        );
                        continue;
                    }

                    $tempName = pathinfo($file['name'], PATHINFO_FILENAME);

                    // Check if the image has an IPTC title an use it as original title
                    $size = getimagesize($file['tmp_name'], $imageInfo);
                    if (isset($imageInfo['APP13'])) {
                        $iptc = iptcparse($imageInfo["APP13"]);
                        if (isset($iptc['2#120'])) {
                            $tempName = str_replace("\000", "", $iptc["2#120"][0]);
                        }
                    }

                    $fm = $this->get('data.manager.filter');

                    $data = array(
                        'local_file'        => $file['tmp_name'],
                        'original_filename' => $file['name'],
                        'title'             => $tempName,
                        'description'       => $tempName,
                        'fk_category'       => $category,
                        'category'          => $category,
                        'category_name'     => $category_name,
                        'metadata'          => $fm->filter('tags', $tempName),
                    );

                    try {
                        $photo = new \Photo();
                        $photoId = $photo->createFromLocalFile($data);

                        $photo = new \Photo($photoId);

                        $info = $photo;
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
}
