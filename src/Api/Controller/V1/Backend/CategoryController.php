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

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CATEGORY_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_category_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'CATEGORY_CREATE',
        'delete' => 'CATEGORY_DELETE',
        'empty'  => 'CATEGORY_UPDATE',
        'list'   => 'CATEGORY_ADMIN',
        'move'   => 'CATEGORY_UPDATE',
        'patch'  => 'CATEGORY_UPDATE',
        'save'   => 'CATEGORY_CREATE',
        'show'   => 'CATEGORY_UPDATE',
        'update' => 'CATEGORY_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.category';

    /**
     * Removes all contents assigned to the category.
     *
     * @param integer $id The category id.
     *
     * @return JsonResponse The response object.
     */
    public function emptyItemAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('empty'));

        $msg = $this->get('core.messenger');

        $this->get($this->service)->emptyItem($id);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Removes all contents assigned to the categories in the list.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function emptyListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('empty'));

        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $emptied = $this->get($this->service)->emptyList($ids);

        if ($emptied > 0) {
            $msg->add(
                sprintf(_('%s items updated successfully'), $emptied),
                'success'
            );
        }

        if ($emptied !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be updated successfully'),
                count($ids) - $emptied
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Moves all contents assigned to the category to the target category.
     *
     * @param Request $request The request object.
     * @param integer $id      The category id.
     *
     * @return JsonResponse The response object.
     */
    public function moveItemAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('move'));

        $target = $request->request->get('target', null);
        $msg    = $this->get('core.messenger');

        $this->get($this->service)->moveItem($id, $target);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Moves all contents assigned to the categories in the list to the target
     * category.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function moveListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('move'));

        $ids    = $request->request->get('ids', []);
        $target = $request->request->get('target', null);
        $msg    = $this->get('core.messenger');
        $moved  = $this->get($this->service)->moveList($ids, $target);

        if ($moved > 0) {
            $msg->add(
                sprintf(_('%s items updated successfully'), $moved),
                'success'
            );
        }

        if ($moved !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be updated successfully'),
                count($ids) - $moved
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        return [
            'keys'   => $this->get($this->service)->getL10nKeys(),
            'locale' => $this->get('core.helper.locale')->getConfiguration(),
            'menu'   => $this->get('core.theme')->canCategoriesChangeMenu(),
            'stats'  => $this->get($this->service)->getStats($items),
            'types'  => $this->get('core.theme')->getTypesForCategories()
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item->id;
    }
}
