<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Displays, saves, modifies and removes subscribers.
 */
class SubscriberController extends Controller
{
    /**
     * Returns the data to create a new subscriber.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_CREATE')")
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

    /**
     * Deletes a subscriber.
     *
     * @param integer $id The subscriber id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_DELETE')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscriber')->deleteItem($id);

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.delete', ['id' => $id]);

        $msg->add(_('Item deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected subscribers.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.subscriber')->deleteList($ids);

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
     * Downloads the list of subscribers with metas.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function exportAction()
    {
        $items = $this->get('api.service.subscriber')->getList();

        $csvHeaders = [
            _('Name'), _('Username'), _('Activated'), _('Email'), _('Gender'),
            _('Date Birth'),  _('Postal Code'),  _('Registration date'),
        ];

        $output = implode(",", $csvHeaders);

        foreach ($items['items'] as &$item) {
            switch ($item->gender) {
                case 'male':
                    $gender = _('Male');
                    break;
                case 'female':
                    $gender = _('Female');
                    break;

                default:
                    $gender = empty($item->gender) ? _('Not defined') : _('Other');
                    break;
            }

            $row = [
                $item->name,
                $item->username,
                $item->activated,
                $item->email,
                $gender,
                !empty($item->birth_date) ? $item->birth_date : '',
                !empty($item->postal_code) ? $item->postal_code : '',
                !empty($item->register_date) ?
                    $item->register_date->format('Y-m-d H:i:s') : '',
            ];

            $output .= "\n" . implode(",", $row);
        }

        $response = new Response($output, 200);

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Subscribers list Export');
        $response->headers->set(
            'Content-Disposition',
            'attachment; filename=subscribers-' . date('Y-m-d') . '.csv'
        );
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Returns the list of subscribers.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_LIST')")
     */
    public function listAction(Request $request)
    {
        $ss       = $this->get('api.service.subscriber');
        $oql      = $request->query->get('oql', '');
        $response = $ss->getList($oql);

        $response['extra'] = $this->getExtraData($response['items']);
        $response['items'] = $ss->responsify($response['items']);

        return new JsonResponse($response);
    }

    /**
     * Returns the list of settings for CONTENT_SUBSCRIPTIONS extension.
     *
     * @return JsonResponse The response object.
     */
    public function listSettingsAction()
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('user_settings', []);

        return new JsonResponse([ 'settings' => $settings ]);
    }

    /**
     * Updates some properties for a subscriber.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function patchAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscriber')
            ->patchItem($id, $request->request->all());

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.update', ['id' => $id]);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Updates some properties for a list of subscribers.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        $updated = $this->get('api.service.subscriber')
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
     * Saves settings for CONTENT_SUBSCRIPTIONS extension.
     *
     * @param Request $request The request object.
     *
     * @return JsonResposne The response object.
     */
    public function saveSettingsAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $settings = $request->request->all();

        if (!is_array($settings) ||
            !array_key_exists('fields', $settings) ||
            !is_array($settings['fields'])
        ) {
            $settings = ['fields' => []];
        }

        try {
            $this->get('orm.manager')->getDataSet('Settings', 'instance')
                ->set('user_settings', $settings);

            $msg->add(_('Settings saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Saves a new subscriber.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $msg = $this->get('core.messenger');

        $user = $this->get('api.service.subscriber')
            ->createItem($request->request->all());
        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'api_v1_backend_subscriber_show',
                [ 'id' => $user->id ]
            )
        );

        return $response;
    }

    /**
     * Returns a subscriber.
     *
     * @param integer $id The subscriber id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function showAction($id)
    {
        $ss   = $this->get('api.service.subscriber');
        $item = $ss->getItem($id);

        return new JsonResponse([
            'item'  => $ss->responsify($item),
            'extra' => $this->getExtraData([ $item ])
        ]);
    }

    /**
     * Updates the subscriber information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('SUBSCRIBER_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.subscriber')
            ->updateItem($id, $request->request->all());

        // TODO: Remove when deprecated old user_repository
        $this->get('core.dispatcher')->dispatch('user.update', ['id' => $id]);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data.
     *
     * @param array $item The list of items.
     *
     * @return array The extra data.
     */
    private function getExtraData($items = null)
    {
        $client   = null;
        $ss       = $this->get('api.service.subscription');
        $photos   = [];
        $response = $ss->getList();

        $subscriptions = $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'pk_user_group' ])
            ->get();

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('user_settings', []);

        if (!empty($items)) {
            $ids = array_filter(array_map(function ($a) {
                return [ 'photo', $a->avatar_img_id ];
            }, $items), function ($a) {
                return !empty($a);
            });

            $photos = $this->get('entity_repository')->findMulti($ids);
            $photos = $this->get('data.manager.filter')
                ->set($photos)
                ->filter('mapify', [ 'key' => 'pk_photo' ])
                ->get();
        }

        $em = $this->get('orm.manager');

        if (!empty($this->get('core.instance')->getClient())) {
            $client = $em->getRepository('Client')
                ->find($this->get('core.instance')->getClient());

            $client = $em->getConverter('Client')->responsify($client);
        }

        return [
            'countries'     => Intl::getRegionBundle()->getCountryNames(),
            'client'        => $client,
            'photos'        => $photos,
            'settings'      => $settings,
            'subscriptions' => $ss->responsify($subscriptions)
        ];
    }
}
