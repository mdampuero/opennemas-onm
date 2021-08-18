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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsstandController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'KIOSKO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_newsstand_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.newsstand';

    /**
     * {@inheritdoc}
     */
    public function saveItemAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $msg       = $this->get('core.messenger');
        $data      = $request->request->all();
        $file      = $request->files->get('path');
        $thumbnail = $request->files->get('thumbnail');

        $item = $this->get($this->service)->createItem($data, $file, $thumbnail);

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

        $msg       = $this->get('core.messenger');
        $data      = $request->request->all();
        $file      = $request->files->get('path');
        $thumbnail = $request->files->get('thumbnail');

        $this->get($this->service)->updateItem($id, $data, $file, $thumbnail);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getCategories($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = [];

        foreach ($items as $item) {
            $ids = array_merge($ids, $item->categories);
        }

        if (empty($ids)) {
            return [];
        }

        return $this->get('api.service.category')->responsify(
            $this->get('api.service.category')
                ->getListByIds($ids)['items']
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
        ]);
    }
}
