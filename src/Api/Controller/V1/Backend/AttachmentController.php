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

class AttachmentController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'FILE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_attachment_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'ATTACHMENT_CREATE',
        'delete' => 'ATTACHMENT_DELETE',
        'patch'  => 'ATTACHMENT_UPDATE',
        'update' => 'ATTACHMENT_UPDATE',
        'list'   => 'ATTACHMENT_ADMIN',
        'save'   => 'ATTACHMENT_CREATE',
        'show'   => 'ATTACHMENT_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.attachment';

    /**
     * {@inheritdoc}
     */
    public function saveItemAction(Request $request)
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
    public function updateItemAction(Request $request, $id)
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
