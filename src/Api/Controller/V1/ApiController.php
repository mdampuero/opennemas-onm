<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * The filename to include in the CSV report.
     *
     * @var type
     */
    protected $filename = null;

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = null;

    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = null;

    /**
     * Returns the list of paramters needed to create a new item.
     *
     * @return JsonResponse The response object.
     */
    public function createItemAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('create'));

        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Deletes an item.
     *
     * @param integer $id The subscriber id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteItemAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));
        $this->checkSecurityForContents('CONTENT_OTHER_DELETE', [ $id ]);

        $msg = $this->get('core.messenger');

        $this->get($this->service)->deleteItem($id);

        $msg->add(_('Item deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected items.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));

        $ids = $request->request->get('ids', []);

        $this->checkSecurityForContents('CONTENT_OTHER_DELETE', $ids);

        $msg     = $this->get('core.messenger');
        $deleted = $this->get($this->service)->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s items deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns an item.
     *
     * @param integer $id The item id.
     *
     * @return JsonResponse The response object.
     */
    public function getItemAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('show'));
        $this->checkSecurityForContents('CONTENT_OTHER_UPDATE', [ $id ]);

        $ss   = $this->get($this->service);
        $item = $ss->getItem($id);

        return new JsonResponse([
            'item'  => $ss->responsify($item),
            'extra' => $this->getExtraData($item)
        ]);
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

        return [
            'items'      => $us->responsify($response['items']),
            'total'      => $response['total'],
            'extra'      => $this->getExtraData($response['items']),
            'o-filename' => $this->filename,
        ];
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchItemAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('patch'));
        $this->checkSecurityForContents('CONTENT_OTHER_UPDATE', [ $id ]);

        $msg = $this->get('core.messenger');

        $this->get($this->service)
            ->patchItem($id, $request->request->all());
        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some properties for a list of items.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('patch'));

        $params = $request->request->all();
        $ids    = $params['ids'];

        $this->checkSecurityForContents('CONTENT_OTHER_UPDATE', $ids);

        $msg = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get($this->service)->patchList($ids, $params);

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
     * Saves a new item.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveItemAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $msg = $this->get('core.messenger');

        $item = $this->get($this->service)
            ->createItem($request->request->all());

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
     * Updates the item information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateItemAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('update'));
        $this->checkSecurityForContents('CONTENT_OTHER_UPDATE', [ $id ]);

        $msg = $this->get('core.messenger');

        $this->get($this->service)
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of authors for an item or a list of items.
     *
     * @param mixed $items The item or the list of items to get authors for.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        return $this->get('api.service.author')->responsify(
            $this->get('api.service.author')
                ->getList()['items']
        );
    }

    /**
     * Returns the list of tags for an item or a list of items.
     *
     * @param mixed $items The item or the list of items to get tags for.
     *
     * @return array The list of tags.
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
            if (!empty($item->categories)) {
                $ids = array_merge($ids, $item->categories);
                continue;
            }

            if (!empty($item->category_id)) {
                $ids[] = $item->category_id;
            }
        }

        if (empty($ids)) {
            return [];
        }

        return $this->get('api.service.category')->responsify(
            $this->get('api.service.category')
                ->getListByIds(array_unique($ids))['items']
        );
    }

    /**
     * Returns a list of extra data.
     *
     * @param mixed $items The item when called in a single-item action or the
     *                     array of items when called in a list-of-items action.
     *
     * @return array The extra data.
     */
    protected function getExtraData()
    {
        return [];
    }

    /**
     * Returns the item id.
     *
     * @param mixed $item The item.
     *
     * @return integer The item id.
     */
    protected function getItemId($item)
    {
        return $item->id;
    }

    /**
     * Returns the list of tags for an item or a list of items.
     *
     * @param mixed $items The item or the list of items to get tags for.
     *
     * @return array The list of tags.
     */
    protected function getTags($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (!is_array($items)) {
            $items = [ $items ];
        }

        $ids = [];

        foreach ($items as $item) {
            if (!empty($item->tags)) {
                $ids = array_unique(array_merge($ids, $item->tags));
            }
        }

        $ids = array_values(array_filter($ids, function ($a) {
            return !empty($a);
        }));

        if (empty($ids)) {
            return [];
        }

        return $this->get('api.service.tag')->responsify(
            $this->get('api.service.tag')->getListByIds($ids)['items']
        );
    }

    /**
     * Checks if the user has permission to delete or modify a list of contents.
     *
     * @param mixed $permission  The permission to check (CONTENT_OTHER_UPDATE, CONTENT_OTHER_DELETE).
     * @param array  $ids        The ids of the contents to modify or delete.
     *
     * @throws AccessDeniedException If the action can not be executed.
     */
    protected function checkSecurityForContents($permission, array $ids)
    {
        foreach ($ids as $id) {
            $this->checkSecurity($this->extension, $permission, $this->get($this->service)->getItem($id));
        }
    }
}
