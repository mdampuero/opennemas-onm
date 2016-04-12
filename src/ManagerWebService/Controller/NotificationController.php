<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\ORM\Entity\Notification;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns, saves, modifies and removes notifications.
 */
class NotificationController extends Controller
{
    /**
     * Deletes a notification.
     *
     * @param integer $id The notification id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $em = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $notification = $em->getRepository('Notification')->find($id);

        $em->remove($notification);
        $msg->add(_('Notification deleted successfully.'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected notifications.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || count($ids) === 0) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $notifications = $em->getRepository('Notification')->findBy($oql);

        $deleted = 0;
        foreach ($notifications as $notification) {
            try {
                $em->remove($notification);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s notifications deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of notifications.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('Notification');
        $converter = $this->get('orm.manager')->getConverter('Notification');

        $total         = $repository->countBy($oql);
        $notifications = $repository->findBy($oql);

        $ids = [];
        foreach ($notifications as &$notification) {
            if (empty($notification->instances)) {
                $notification->instances = [];
            }

            $ids = array_merge($ids, $notification->instances);
            $notification = $converter->responsify($notification->getData());
        }

        $ids       = array_unique(array_diff($ids, [ -1, 0 ]));
        $instances = [];
        if (!empty($ids)) {
            $instances = $this->get('instance_manager')->findBy([
                'id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ]
            ]);
        }

        $extra['instances'] = [
            '-1' => [ 'name' => _('Manager'), 'value' => -1 ],
            '0'  => [ 'name' => _('All'), 'value' => 0 ]
        ];

        foreach ($instances as $instance) {
            $extra['instances'][$instance->id] = [
                'name'  => $instance->internal_name,
                'value' => $instance->id,
            ];
        }

        return new JsonResponse([
            'extra'   => $extra,
            'results' => $notifications,
            'total'   => $total,
        ]);
    }

    /**
     * Returns the data to create a new notification.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        return new JsonResponse([
            'extra' => $this->getTemplateParams()
        ]);
    }

    /**
     * Updates some notification properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Notification')
            ->objectify($request->request->all());

        $notification = $em->getRepository('Notification')->find($id);
        $notification->merge($data);

        $em->persist($notification);

        $msg->add(_('Notification saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchSelectedAction(Request $request)
    {
        $params = $request->request->all();
        $ids    = $params['ids'];
        $msg    = $this->get('core.messenger');

        unset($params['ids']);

        if (!is_array($ids) || count($ids) === 0) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em   = $this->get('orm.manager');
        $oql  = sprintf('id in [%s]', implode(',', $ids));
        $data = $em->getConverter('Notification')->objectify($params);

        $notifications = $em->getRepository('Notification')->findBy($oql);

        $updated = 0;
        foreach ($notifications as $notification) {
            try {
                $notification->merge($data);
                $em->persist($notification);
                $updated++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error', 409);
            }
        }

        if ($updated > 0) {
            $msg->add(
                sprintf(_('%s notifications saved successfully'), $updated),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Creates a new notification from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Notification')
            ->objectify($request->request->all());

        $notification = new Notification($data);

        if (empty($notification->start)) {
            $notification->start = new \Datetime('now');
        }

        $em->persist($notification);
        $msg->add(_('Notification saved successfully'), 'success', 201);


        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_notification_show',
                [ 'id' => $notification->id ]
            )
        );

        return $response;
    }

    /**
     * Returns an instance as JSON.
     *
     * @param integer  $id The instance id.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        $em           = $this->get('orm.manager');
        $converter    = $em->getConverter('Notification');
        $notification = $em->getRepository('Notification')->find($id);

        $extra = $this->getTemplateParams();
        $im    = $this->get('instance_manager');

        $instances = [];
        foreach ($notification->instances as $id) {
            if ($id == 0) {
                $instances[] = [ 'name' => _('All'), 'id' => $id ];
            } elseif ($id == -1) {
                $instances[] = [ 'name' => 'Manager', 'id' => $id ];
            } else {
                $instances[] = [ 'name' => $im->find($id)->internal_name, 'id' => $id ];
            }
        }

        $notification->instances = $instances;
        $notification = $converter->responsify($notification->getData());

        return new JsonResponse([
            'extra'        => $extra,
            'notification' => $notification
        ]);
    }

    /**
     * Updates the instance information gives its id
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The instance id.
     * @return Response          The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Notification')
            ->objectify($request->request->all());

        $notification = $em->getRepository('Notification')->find($id);
        $notification->merge($data);

        if (empty($notification->start)) {
            $notification->start = date('Y-m-d H:i:s');
        }

        $em->persist($notification);
        $msg->add(_('Notification saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function getTemplateParams()
    {
        $params = [
            'icons' => [
                'comment' => [ 'name' => _('Comments'), 'value' => 'comment' ],
                'email'   => [ 'name' => _('Email'), 'value' => 'envelope' ],
                'help'    => [ 'name' => _('Help'), 'value' => 'support' ],
                'info'    => [ 'name' => _('Information'), 'value' => 'info' ],
                'media'   => [ 'name' => _('Media'), 'value' => 'database' ],
                'user'    => [ 'name' => _('Users'), 'value' => 'user' ]
            ]
        ];

        $instances = $this->get('instance_manager')->findBy([]);

        $params['instances'] = [
            [ 'name' => 'Manager', 'id' => -1 ],
            [ 'name' => _('All'), 'id' => 0 ]
        ];

        $params['languages'] = [
            'en' => _('English'),
            'es' => _('Spanish'),
            'gl' => _('Galician'),
        ];

        foreach ($instances as $instance) {
            $params['instances'][] = [
                'name'  => $instance->internal_name,
                'id' => $instance->id
            ];
        }

        return $params;
    }
}
