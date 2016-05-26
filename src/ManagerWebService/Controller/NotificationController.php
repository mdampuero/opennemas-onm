<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Framework\ORM\Entity\Notification;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{
    /**
     * Creates a new notification from the request.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function createAction(Request $request)
    {
        $notification = new Notification();

        foreach ($request->request as $key => $value) {
            if (!is_null($value)) {
                $notification->{$key} =
                    $request->request->filter($key, null, FILTER_SANITIZE_STRING);
            }
        }

        if (empty($notification->start)) {
            $notification->start = date('Y-m-d H:i:s');
        }

        $this->get('orm.manager')->persist($notification);

        $response = new JsonResponse(_('Notification saved successfully'), 201);

        // Add permanent URL for the current notification
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_notification_show',
                [ 'id' => $notification->id ]
            )
        );

        return $response;
    }

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
        $notification = $em->getRepository('manager.notification')->find($id);

        $em->remove($notification);

        return new JsonResponse(_('Notification deleted successfully.'));
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
        $error      = [];
        $messages   = [];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (!is_array($selected)
            || (is_array($selected) && count($selected) == 0)
        ) {
            return new JsonResponse(
                _('Unable to find the notifications for the given criteria'),
                404
            );
        }

        $em = $this->get('orm.manager');

        $criteria = [
            'id' => [
                [ 'value' => $selected, 'operator' => 'IN']
            ]
        ];

        $notifications = $em->getRepository('manager.notification')
            ->findBy($criteria);

        foreach ($notifications as $notification) {
            try {
                $em->remove($notification);
                $updated++;
            } catch (EntityNotFoundException $e) {
                $error[]    = $id;
                $messages[] = [
                    'message' => sprintf(_('Unable to find the instance with id "%s"'), $id),
                    'type'    => 'error'
                ];
            } catch (\Exception $e) {
                $error[]    = $id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error'
                ];
            }
        }

        if (count($updated) > 0) {
            $messages = [
                'message' => sprintf(_('%s notifications deleted successfully.'), count($updated)),
                'type'    => 'success'
            ];
        }

        // Return the proper status code
        if (count($error) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($error) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse(
            [ 'error' => $error, 'messages' => $messages ],
            $statusCode
        );
    }

    /**
     * Returns the list of notifications as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : [];
        $orderBy  = $request->query->filter('orderBy') ? : [];
        $extra    = $this->getTemplateParams();

        $order = array();
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        if (!empty($criteria)) {
            $criteria['union'] = 'OR';
        }

        $nr = $this->get('orm.manager')->getRepository('manager.notification');

        $notifications = $nr->findBy($criteria, $order, $epp, $page);

        $ids = [];
        foreach ($notifications as &$notification) {
            if (empty($notification->instances)) {
                $notification->instances = [];
            }

            $ids = array_merge($ids, $notification->instances);
            $notification = $notification->getData();
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

        $total = $nr->countBy($criteria);

        return new JsonResponse([
            'epp'     => $epp,
            'extra'   => $extra,
            'page'    => $page,
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
        $extra = $this->getTemplateParams();

        return new JsonResponse([ 'extra' => $extra ]);
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
        $em           = $this->get('orm.manager');
        $params       = $request->request->all();
        $notification = $em->getRepository('manager.notification')->find($id);

        foreach ($params as $key => $value) {
            $notification->{$key} = $value;
        }

        $em->persist($notification);

        return new JsonResponse(_('Notification saved successfully'));
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
        $error      = [];
        $messages   = [];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (is_array($selected) && count($selected) == 0) {
            return new JsonResponse(
                _('Unable to find the notifications for the given criteria'),
                404
            );
        }

        $em = $this->get('orm.manager');

        $criteria = [ 'id' => [ [ 'value' => $selected, 'operator' => 'IN'] ] ];

        $notifications = $em->getRepository('manager.notification')->findBy($criteria);

        foreach ($notifications as $notification) {
            try {
                foreach ($request->request->all() as $key => $value) {
                    if ($key !== 'selected') {
                        $notification->{$key} = $request->request->get($key);
                    }
                }

                $em->persist($notification);
                $updated[] = $notification->id;
            } catch (\Exception $e) {
                $error[]    = $notification->id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error',
                ];
            }
        }

        if (count($updated) > 0) {
            $messages[] = [
                'message' => sprintf(
                    _('%s notifications updated successfully.'),
                    count($updated)
                ),
                'type' => 'success'
            ];
        }

        if (count($error) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($error) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse(
            [ 'error' => $error, 'messages' => $messages, 'success' => $updated ],
            $statusCode
        );
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
        try {
            $notification = $this->get('orm.manager')
                ->getRepository('manager.notification')
                ->find($id);

            if (empty($notification->instances)) {
                $notification->instances = [];
            }

            $extra = $this->getTemplateParams();

            $instances = [];
            foreach ($notification->instances as $instance) {
                $name = $instance;

                if ($instance == 'all') {
                    $name = _('All');
                } elseif ($instance == 'manager') {
                    $name = 'Manager';
                }

                $instances[] = [ 'name' => $name, 'id' => $instance ];
            }

            $notification->instances = $instances;

            return new JsonResponse([
                'extra'        => $extra,
                'notification' => $notification->getData()
            ]);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the entity with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
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
        try {
            $em = $this->get('orm.manager');
            $notification = $em->getRepository('manager.notification')->find($id);

            $keys = array_unique(array_merge(
                array_keys($request->request->all()),
                array_keys($notification->getData())
            ));

            foreach ($keys as $key) {
                $notification->{$key} = $request->request->get($key);
            }


            if (empty($notification->start)) {
                $notification->start = date('Y-m-d H:i:s');
            }

            $em->persist($notification);

            return new JsonResponse(_('Notification saved successfully'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the instance with id "%s"'), $id),
                404
            );
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
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
            [ 'id' => 'manager', 'name' => 'Manager' ],
            [ 'id' => 'all', 'name' => _('All') ]
        ];

        $params['languages'] = [
            'en' => _('English'),
            'es' => _('Spanish'),
            'gl' => _('Galician'),
        ];

        foreach ($instances as $instance) {
            $params['instances'][] = [
                'id'   => $instance->internal_name,
                'name' => $instance->internal_name
            ];
        }

        return $params;
    }
}
