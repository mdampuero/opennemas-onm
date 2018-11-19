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
    public function createAction()
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
    public function deleteAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));

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
    public function deleteSelectedAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('delete'));

        $ids     = $request->request->get('ids', []);
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
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    public function listAction(Request $request)
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
    public function patchAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('patch'));

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
    public function patchSelectedAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('patch'));

        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get($this->service)
            ->patchList($ids, $params);

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
    public function saveAction(Request $request)
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
     * Returns an item.
     *
     * @param integer $id The item id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('show'));

        $ss   = $this->get($this->service);
        $item = $ss->getItem($id);

        return new JsonResponse([
            'item'  => $ss->responsify($item),
            'extra' => $this->getExtraData($item)
        ]);
    }

    /**
     * Updates the item information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('update'));

        $msg = $this->get('core.messenger');

        $this->get($this->service)
            ->updateItem($id, $request->request->all());

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @param mixed $items The item when called in a single-item action or the
     *                     array of items when called in a list-of-items action.
     *
     * @return array The extra data.
     */
    protected function getExtraData($items = null)
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
}
