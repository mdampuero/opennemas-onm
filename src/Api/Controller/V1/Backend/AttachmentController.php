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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AttachmentController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'FILE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_attachment_show';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.attachment';

    /**
     * {@inheritdoc}
     */
    public function saveAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $msg  = $this->get('core.messenger');
        $data = $request->request->all();
        $file = $request->files->get('path');

        $item = $this->get($this->service)->createItem($data, $file);

        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        if (!empty($this->getItemRoute)) {
            $response->headers->set('Location', $this->generateUrl(
                $this->getItemRoute,
                [ 'id' => $this->getItemId($item) ]
            ));
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('update'));

        $msg  = $this->get('core.messenger');
        $data = $request->request->all();
        $file = $request->files->get('path');

        $this->get($this->service)->updateItem($id, $data, $file);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list with the file name and id of files in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function autocompleteAction(Request $request)
    {
        $search   = $request->query->get('query');
        $order    = [ 'path' => 'desc' ];
        $criteria = [
            'content_type_name' => [ ['value' => 'attachment', 'operator' => '='] ],
            'in_litter'         => [ ['value' => 0, 'operator' => '='] ],
            'path'              => [ ['value' => '%' . $search . '%', 'operator' => 'like'] ],
            'join'              => [ [
                'table'      => 'attachments',
                'pk_content' => [ [ 'value' => 'pk_attachment', 'field' => true ] ]
            ] ]
        ];

        $results = $this->get('entity_repository')->findBy($criteria, $order, 10);
        $results = array_map(function ($file) {
            return ['id' => $file->id, 'filename' => basename($file->path)];
        }, $results);

        return new JsonResponse([ 'results' => $results ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items)
        ]);
    }
}
