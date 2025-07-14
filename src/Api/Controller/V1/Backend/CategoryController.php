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

    protected $module = 'category';

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
        $extra = parent::getExtraData($items);
        return array_merge([
            'keys'   => $this->get($this->service)->getL10nKeys(),
            'locale' => $this->get('core.helper.locale')->getConfiguration(),
            'menu'   => $this->get('core.theme')->canCategoriesChangeMenu(),
            'stats'  => $this->get($this->service)->getStats($items),
            'types'  => $this->get('core.theme')->getTypesForCategories(),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ], $extra);
    }

    /**
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    public function getListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $us  = $this->get($this->service);
        $oql = $request->query->get('oql', '');

        $response = $us->getList($oql);

        // Filter to get only categories that have parents
        $categoriesWithParents = array_filter(
            array_map(function ($item) {
                return [
                    'id' => $item->id,
                    'parent_id' => $item->parent_id,
                ];
            }, $response['items']),
            function ($item) {
                return $item['parent_id'] !== null;
            }
        );

        // Extract just the parent IDs we want to look up
        $parentIds       = array_column($categoriesWithParents, 'parent_id');
        $uniqueParentIds = array_unique($parentIds);

        // Get the parent categories and create a map [id => parent_name]
        $parentMap = [];
        if (!empty($uniqueParentIds)) {
            $parentsResponse = $us->getListParents($uniqueParentIds);
            foreach ($parentsResponse['items'] as $parent) {
                $parentMap[$parent->id] = $parent->title;
            }
        }

        $itemsWithParents = array_map(function ($item) use ($parentMap) {
            if ($item->parent_id !== null && isset($parentMap[$item->parent_id])) {
                $item->parent = $parentMap[$item->parent_id];
            }
            return $item;
        }, $response['items']);

        return [
            'items'      => $us->responsify($itemsWithParents),
            'parents'    => $categoriesWithParents,
            'total'      => $response['total'],
            'extra'      => $this->getExtraData($response['items']),
            'o-filename' => $this->filename,
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
