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
 * Handles the actions for handling the pdf covers
 *
 * @package Backend_Controllers
 */
class CoverController extends Controller
{
    /**
     * Shows the list of the
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_ADMIN')")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => null ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->pk_content_category
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->pk_content_category
                ];
            }
        }

        return $this->render('covers/list.tpl', [
            'categories' => $categories,
            'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR
        ]);
    }

    /**
     * Displays the cover information form
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
                'locale'         => $this->get('core.locale')->getRequestLocale('frontend'),
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
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            $extra = $this->getExtraData();

            return new JsonResponse([
                'extra' => array_merge($extra, [
                    'KIOSKO_IMG_URL' => INSTANCE_MEDIA . KIOSKO_DIR,
                    'locale'         => $this->get('core.locale')->getRequestLocale('frontend'),
                    'tags'           => []
                ])
            ]);
        }

        $postInfo = $request->request;

        $coverData = [
            'title'          => $postInfo->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'          => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'type'           => (int) $postInfo->getDigits('type', 0),
            'category'       => (int) $postInfo->getDigits('category'),
            'content_status' => (int) $postInfo->getDigits('content_status', 1),
            'favorite'       => (int) $postInfo->getDigits('favorite', 1),
            'date'           => $postInfo->filter('date', null, FILTER_SANITIZE_STRING),
            'price'          => $postInfo->filter('price', null, FILTER_SANITIZE_NUMBER_FLOAT),
            'fk_publisher'   => (int) $this->getUser()->id,
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        $dateTime = new \DateTime($coverData['date']);

        $coverData['name'] = $dateTime->format('Ymd') . date('His') . '-'
            . $coverData['category'] . '.pdf';
        $coverData['path'] = $dateTime->format('Y/m/d') . '/';

        $path = INSTANCE_MEDIA_PATH . KIOSKO_DIR . $coverData['path'];

        try {
            // Create folder if it doesn't exist
            if (!file_exists($path)) {
                \Onm\FilesManager::createDirectory($path);
            }

            $uploadStatus = false;

            foreach ($request->files as $file) {
                // Move uploaded file
                $uploadStatus = $file->isValid() && $file->move(realpath($path), $coverData['name']);
            }

            if (!$uploadStatus) {
                throw new \Exception(
                    sprintf(
                        _('Unable to upload the file. Try to upload a file smaller than %d MB'),
                        (int) ini_get('upload_max_filesize')
                    )
                );
            }

            $kiosko = new \Kiosko();
            if (!$kiosko->create($coverData)) {
                throw new \Exception(_('Unable to create the file. Try again'));
            }

            return $this->redirect(
                $this->generateUrl('backend_cover_show', [ 'id' => $kiosko->id ])
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());

            return $this->redirect($this->generateUrl('backend_covers', [
                'category' => $postInfo->getDigits('category'),
            ]));
        }
    }

    /**
     * Updates the cover information provided by POST request
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
            $cover = new \Kiosko($id);

            if ($cover->id == null) {
                throw new \Exception(_('Cover id not valid.'));
            }

            if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
                && !$cover->isOwner($this->getUser()->id)
            ) {
                throw new \Exception(_("You can't modify this cover because you don't have enough privileges."));
            }

            $postReq = $request->request;

            $data = [
                'id'             => $postReq->getDigits('id', 0),
                'title'          => $postReq->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'description'          => $postReq->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'date'           => $postReq->filter('date', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'cover'          => $postReq->filter('cover', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'price'          => (float) $postReq
                    ->filter('price', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'content_status' => $postReq->getDigits('content_status', 0),
                'type'           => $postReq->getDigits('type', 0),
                'favorite'       => $postReq->getDigits('favorite', 0),
                'category'       => $postReq->getDigits('category', 0),
                'name'           => $cover->name,
                'thumb_url'      => $cover->thumb_url,
                'fk_user_last_editor' => $this->getUser()->id,
                'tag_ids'        => $request->request->get('tag_ids', '')
            ];

            if (!$request->request->get('cover') && !empty($cover->name)) {
                $coverFile  = $cover->kiosko_path . $cover->path . $cover->name;
                $coverThumb = $cover->kiosko_path . $cover->path . $cover->thumb_url;

                // Remove old files if fileinput changed
                if (file_exists($coverFile)) {
                    unlink($coverFile);
                }

                if (file_exists($coverThumb)) {
                    unlink($coverThumb);
                }

                $data['name']      = '';
                $data['thumb_url'] = '';
            }

            // Handle new file
            if ($request->files->get('cover')) {
                $data['name'] = date('His') . '-' . $data['category'] . '.pdf';

                $path = $cover->kiosko_path . $cover->path;

                // Create folder if it doesn't exist
                if (!file_exists($path)) {
                    \Onm\FilesManager::createDirectory($path);
                }

                $file = $request->files->get('cover');

                $uploadStatus = $file->isValid() && $file->move(realpath($path), $data['name']);

                if (!$uploadStatus) {
                    throw new \Exception(
                        sprintf(
                            _('Unable to upload the file. Try to upload a file smaller than %d MB'),
                            (int) ini_get('upload_max_filesize')
                        )
                    );
                }

                $cover->createThumb($data['name'], $cover->path);
            }

            $cover->update($data);

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
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page', 1);

        if (!empty($id)) {
            $cover = new \Kiosko($id);

            // Delete related and relations
            getService('related_contents')->deleteAll($id);

            $cover->delete($id, $this->getUser()->id);

            $this->get('session')->getFlashBag()->add(
                'successs',
                sprintf(_("Cover %s deleted successfully."), $cover->title)
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id to delete the cover.')
            );
        }

        return $this->redirect($this->generateUrl('backend_covers', [
            'category' => $cover->category,
            'page'     => $page
        ]));
    }

    /**
     * Save positions for widget
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('KIOSKO_MANAGER')
     *     and hasPermission('KIOSKO_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $positions = $request->query->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;

            foreach ($positions as $id) {
                $cover  = new \Kiosko($id);
                $result = $result && $cover->setPosition($pos);
                $pos++;
            }
        }

        if ($result) {
            $msg = _("Positions saved successfully.");
        } else {
            $msg = _("Unable to save the new positions. Please contact with your system administrator.");
        }

        return new Response($msg);
    }

    /**
     * Returns a list of extra data to use in  the create/edit item form
     *
     * @return array
     **/
    private function getExtraData()
    {
        $extra = [];

        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1'); // review this filter to search for commen and specific for kiosko

        $categories = array_filter($categories, function ($a) use ($security) {
            return $security->hasCategory($a->pk_content_category);
        });

        $extra['categories'] = $converter->responsify($categories);
        array_unshift($extra['categories'], [
            'pk_content_category' => null,
            'title'               => _('Select a category...')
        ]);

        return $extra;
    }

    /**
     * Cleans and formats the newsletter template values
     *
     * @param array $values the RAW values to clean
     *
     * @return array the cleaned values
     **/
    public function parseValues($values)
    {
        return $values;
    }
}
