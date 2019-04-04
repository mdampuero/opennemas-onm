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

/**
 * Handles the actions for the images
 *
 * @package Backend_Controllers
 */
class ImagesController extends Controller
{
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
        $years = [];

        $conn = $this->get('orm.manager')->getConnection('instance');

        $results = $conn->fetchAll(
            "SELECT DISTINCT(DATE_FORMAT(created, '%Y-%m')) as date_month FROM contents
            WHERE fk_content_type = 8 AND created IS NOT NULL ORDER BY date_month DESC"
        );

        foreach ($results as $value) {
            $date = \DateTime::createFromFormat('Y-n', $value['date_month']);
            $fmt  = new \IntlDateFormatter(CURRENT_LANGUAGE, null, null, null, null, 'MMMM');

            if (!is_null($fmt)) {
                $years[$date->format('Y')]['name']     = $date->format('Y');
                $years[$date->format('Y')]['months'][] = [
                    'name'  => ucfirst($fmt->format($date)),
                    'value' => $value['date_month']
                ];
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
        $id    = $request->query->getDigits('id');
        $photo = $this->get('entity_repository')->find('Photo', $id);

        if (is_null($photo->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a photo with the id "%d".'), $id)
            );

            return $this->redirect($this->generateUrl('backend_albums_list'));
        }

        $tags = [];

        if (!empty($photo->tags)) {
            $ts   = $this->get('api.service.tag');
            $tags = $ts->responsify($ts->getListByIds($photo->tags)['items']);
        }

        return $this->render('image/new.tpl', [
            'MEDIA_IMG_URL' => MEDIA_URL . MEDIA_DIR . DS . IMG_DIR,
            'photo'         => $photo,
            'locale'        => $this->get('core.helper.locale')
                ->getConfiguration()
        ]);
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
        $id    = $request->query->getDigits('id');
        $photo = $this->get('entity_repository')->find('Photo', $id);

        if (is_null($photo->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a photo with the id "%d".'), $id)
            );

            return $this->redirect($this->generateUrl('backend_albums_list'));
        }

        $photoData = [
            'id'             => filter_var($id, FILTER_SANITIZE_STRING),
            'title'          => filter_var($_POST['title'], FILTER_SANITIZE_STRING),
            'description'    => filter_var(
                $_POST['description'],
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_NO_ENCODE_QUOTES
            ),
            'author_name'    => filter_var($_POST['author_name'], FILTER_SANITIZE_STRING),
            'address'        => filter_var($_POST['address'], FILTER_SANITIZE_STRING),
            'category'       => filter_var($_POST['category'], FILTER_SANITIZE_STRING),
            'content_status' => 1,
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        $photo = new \Photo($id);

        if ($photo->update($photoData)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _("Photo updated successfully.")
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("There was a problem while updating the content.")
            );
        }

        return $this->redirect($this->generateUrl(
            'admin_photo_show',
            [ 'id' => $photo->id ]
        ));
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

        $photo->delete($id, $this->getUser()->id);

        return $this->redirect(
            $this->generateUrl(
                'admin_images',
                [
                    'category' => $photo->category,
                    'page'     => $page,
                ]
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

        if ($request->getMethod() != 'POST') {
            return new JsonResponse([], 200);
        }

        $files = isset($_FILES) ? $_FILES : null;
        $info  = [];

        foreach ($files as $file) {
            $photo = new \Photo();

            if (empty($file['tmp_name'])) {
                $info [] = [
                    'error' => _('Not valid file format or the file exceeds the max allowed file size.'),
                ];
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

            $data = [
                'local_file'        => $file['tmp_name'],
                'original_filename' => $file['name'],
                'title'             => $tempName,
                'description'       => $tempName,
                'fk_category'       => null,
                'category'          => null,
                'category_name'     => '',
                'tags'              => json_decode($request->request->get('tags', ''), true)
            ];

            try {
                $photo   = new \Photo();
                $photoId = $photo->createFromLocalFile($data);

                $photo = new \Photo($photoId);

                $info = $photo;
            } catch (Exception $e) {
                $info [] = [
                    'error'         => $e->getMessage(),
                ];
            }
        }

        $json = json_encode($info);
        $response->setContent($json);

        $response->headers->add([ 'Vary' => 'Accept' ]);

        $redirect = $request->request->filter('redirect', null, FILTER_SANITIZE_STRING);
        if (!empty($redirect)) {
            return $this->redirect(sprintf($redirect, rawurlencode($json)));
        }

        if (isset($_SERVER['HTTP_ACCEPT'])
            && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        ) {
            $response->headers->add(['Content-type' => 'application/json']);
        } else {
            $response->headers->add(['Content-type' => 'text/plain']);
        }
        return $response;
    }
}
