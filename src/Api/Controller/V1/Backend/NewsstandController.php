<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for handling the newstand
 *
 * @package Backend_Controllers
 */
class NewsstandController extends Controller
{
    /**
     * Returns the data to create a new newsletter.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_CREATE')")
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Displays the kiosko information form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $content = new \Kiosko($id);

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$content->isOwner($this->getUser()->id)
        ) {
            return new JsonResponse(
                _("You can't modify this cover because you don't have enough privileges."),
                400
            );
        }

        if (is_null($content->id)) {
            return new JsonResponse(
                sprintf(_('Unable to find the article with the id "%d"'), $id),
                400
            );
        }

        $auxTagIds        = $content->getContentTags($content->id);
        $content->tag_ids = array_key_exists($content->id, $auxTagIds) ?
            $auxTagIds[$content->id] :
            [];

        $extra = $this->getExtraData();

        return new JsonResponse([
            'item' => $content,
            'extra' => array_merge($extra, [
                'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
                'tags'           => $this->get('api.service.tag')
                    ->getListByIdsKeyMapped($content->tag_ids)['items']
            ])
        ]);
    }

    /**
     * Handles the creation of new covers
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $postInfo = $request->request;
        $dateTime = new \DateTime();

        $data = [
            'title'          => $postInfo->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $postInfo->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'type'           => (int) $postInfo->getDigits('type', 0),
            'category'       => (int) $postInfo->getDigits('category', 0),
            'content_status' => (int) $postInfo->getDigits('content_status', 1),
            'favorite'       => (int) $postInfo->getDigits('favorite', 1),
            'date'           => $postInfo->filter('date', null, FILTER_SANITIZE_STRING),
            'price'          => $postInfo->filter('price', 0.0, FILTER_SANITIZE_NUMBER_FLOAT),
            'fk_publisher'   => (int) $this->getUser()->id,
            'tag_ids'        => $request->request->get('tag_ids', ''),
            'name'           => '',
            'path'           => $dateTime->format('Y/m/d') . '/',
        ];

        $content = new \Kiosko();

        try {
            $cover     = $request->files->get('cover');
            $thumbnail = $request->files->get('thumbnail');

            // Handle new file
            if ($cover && $thumbnail) {
                $data['name'] = $dateTime->format('YmdHis') . '.pdf';

                $uploadStatus = $content->saveFiles($data['path'], $data['name'], $cover, $thumbnail);

                if (!$uploadStatus) {
                    throw new \Exception(
                        sprintf(
                            _('Unable to upload the file. Try to upload a file smaller than %d MB'),
                            (int) ini_get('upload_max_filesize')
                        )
                    );
                }
            }

            if (!$content->create($data)) {
                throw new \Exception(_('Unable to create the cover. Try again'));
            }

            $msg->add(_('Item saved successfully'), 'success', 201);

            $response = new JsonResponse($msg->getMessages(), $msg->getCode());
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'api_v1_backend_newsstand_show',
                    [ 'id' => $content->id ]
                )
            );

            return $response;
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates the kiosko information provided by POST request
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $id = $request->query->getDigits('id');

        try {
            $content = new \Kiosko($id);

            if ($content->id == null) {
                throw new \Exception(_('Cover id not valid.'));
            }

            if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
                && !$content->isOwner($this->getUser()->id)
            ) {
                throw new \Exception(_("You can't modify this cover because you don't have enough privileges."));
            }

            $postReq = $request->request;

            $data = [
                'id'             => $postReq->getDigits('id', 0),
                'title'          => $postReq->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'description'    => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'date'           => $postReq->filter('date', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'cover'          => $postReq->filter('cover', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'price'          => (float) $postReq
                    ->filter('price', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'content_status' => $postReq->getDigits('content_status', 0),
                'type'           => $postReq->getDigits('type', 0),
                'favorite'       => $postReq->getDigits('favorite', 0),
                'category'       => $postReq->getDigits('category', 0),
                'name'           => $content->name,
                'thumb_url'      => $content->thumb_url,
                'fk_user_last_editor' => $this->getUser()->id,
                'tag_ids'        => $request->request->get('tag_ids', '')
            ];

            $cover     = $request->files->get('cover');
            $thumbnail = $request->files->get('thumbnail');

            // If the user doesnt send a new cover and unsets the old,
            // then remove the old file
            if ((!$cover && empty($request->request->get('name')))) {
                $content->removeFiles();

                $data['name']      = '';
                $data['thumb_url'] = '';
            }

            // If the user uploads a new cover and a thumbnail
            // then save them
            if ($cover && $thumbnail) {
                $dateTime = new \DateTime();

                $data['name'] = $dateTime->format('Ymdhis') . '.pdf';

                $uploadStatus = $content->saveFiles($content->path, $data['name'], $cover, $thumbnail);

                if (!$uploadStatus) {
                    throw new \Exception(
                        sprintf(
                            _('Unable to upload the file. Try to upload a file smaller than %d MB'),
                            (int) ini_get('upload_max_filesize')
                        )
                    );
                }
            }

            $content->update($data);

            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes a video given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $msg = $this->get('core.messenger');

        $content = new \Kiosko($id);
        if (empty($content->id)) {
            $msg->add(_('Content not found'), 'error');

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if ($content->delete($id, $this->getUser()->id)) {
            $msg->add(_('Item sent to trash successfully'), 'success');
        } else {
            $msg->add(_('Unable to send to trash the content'), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param Request $request     The request object.
     * @param string  $contentType Content type name.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('KIOSKO_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $em      = $this->get('entity_repository');
        $total   = true;
        $results = $em->findBy($criteria, $order, $epp, $page, 0, $total);

        $results = \Onm\StringUtils::convertToUtf8($results);

        return new JsonResponse([
            'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
            'extra'          => $this->getExtraData(true),
            'items'          => $results,
            'total'          => $total,
        ]);
    }

    /**
     * Updates some properties for an user.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('KIOSKO_MANAGER')")
     */
    public function patchAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $content = new \Kiosko($id);
        if (empty($content->id)) {
            $msg->add(_('Content not valid'), 'error');

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        if ($content->patch($request->request->all())) {
            $msg->add(_('Item saved successfully'), 'success');
        } else {
            $msg->add(_('Unable to update the content'), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some user group properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('KIOSKO_MANAGER')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $updated = 0;
        foreach ($ids as $id) {
            $content = new \Kiosko($id);
            if (empty($content->id)) {
                $msg->add(_('Content not valid'), 'error');

                return new JsonResponse($msg->getMessages(), $msg->getCode());
            }

            if ($content->patch($params)) {
                $updated++;
            }
        }

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s items updated successfully'), $updated),
                'success'
            );
        }

        if ($updated !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be updated successfully'),
                count($ids) - $updated
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data to use in  the create/edit item form
     *
     * @return array
     */
    private function getExtraData()
    {
        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1'); // review this filter to search for commen and specific for kiosko

        $categories = array_filter($categories, function ($a) use ($security) {
            return $security->hasCategory($a->pk_content_category);
        });

        $extra = [
            'tags'       => [],
            'categories' => $converter->responsify($categories),
        ];

        return array_merge($extra, $this->getLocaleData('frontend'));
    }
}
