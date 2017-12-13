<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Entity\Notification;
use Common\ORM\Entity\UserNotification;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{
    /**
     * Returns the list of instances as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listLatestAction(Request $request)
    {
        $date     = new \DateTime('now');
        $date     = $date->format('Y-m-d H:i:s');
        $epp      = $request->query->getDigits('epp', 10);
        $instance = $this->get('core.instance');
        $id       = $instance->internal_name;
        $theme    = $instance->settings['TEMPLATE_USER'];
        $page     = $request->query->getDigits('page', 1);

        $read = $this->get('core.dispatcher')->dispatch(
            'notifications.getRead',
            [
                'oql' => sprintf(
                    'instance_id = %s and user_id = %s and read_date !is null',
                    $instance->id,
                    $this->getUser()->id
                )
            ]
        );

        $view = $this->get('core.dispatcher')->dispatch(
            'notifications.getView',
            [
                'oql' => sprintf(
                    'instance_id = %s and user_id = %s and view_date !is null',
                    $instance->id,
                    $this->getUser()->id
                )
            ]
        );

        $oql = "(target ~ '%s' or target ~ '\"all\"' or target ~ '%s')"
            . " and enabled = 1 and start <= '%s' and (end is null or end > '%s')";

        if (!empty($read)) {
            $oql .= ' and id !in [' . implode(', ', array_keys($read)) . ']';
        }

        if (!$this->getUser()->isAdmin()) {
            $oql .= ' and (users is null or users = 0)';
        }

        $oql .= ' order by fixed desc limit %s';

        $oql = sprintf($oql, $id, $theme, $date, $date, $epp);

        $notifications = $this->get('core.dispatcher')->dispatch(
            'notifications.get',
            [ 'oql' => $oql ]
        );

        if (is_array($notifications)) {
            foreach ($notifications as &$notification) {
                $this->convertNotification($notification, $view);
            }
        }

        return new JsonResponse([
            'epp'     => $epp,
            'page'    => $page,
            'results' => $notifications,
            'total'   => count($notifications)
        ]);
    }

    /**
     * Returns the list of instances as JSON.
     *
     * @return JsonResponse The response object.
     */
    public function listAction()
    {
        $id    = $this->get('core.instance')->internal_name;
        $theme = $this->get('core.instance')->settings['TEMPLATE_USER'];
        $date  = date('Y-m-d H:i:s');

        $oql = "(target ~ '%s' or target ~ '\"all\"' or target ~ '%s')"
            . " and enabled = 1 and start <= '%s'"
            . " and (end is null or end > '%s')";

        if (!$this->getUser()->isAdmin()) {
            $oql .= ' and (users is null or users = 0)';
        }

        $oql .= 'order by start desc';
        $oql  = sprintf($oql, $id, $theme, $date, $date);

        $notifications = $this->get('core.dispatcher')->dispatch(
            'notifications.get',
            [
                'oql'   => $oql,
                'epp'   => null,
                'order' => [ 'fixed' => 'desc' ],
                'page'  => null
            ]
        );

        if (is_array($notifications) && !empty($notifications)) {
            foreach ($notifications as &$notification) {
                $this->convertNotification($notification);
            }
        }

        $total = $this->get('core.dispatcher')
            ->dispatch('notifications.count', [ 'oql' => $oql ]);

        return new JsonResponse([
            'epp'     => $total,
            'page'    => 1,
            'results' => $notifications,
            'total'   => $total
        ]);
    }

    /**
     * Updates some instance properties.
     *
     * @param Request $request The request object.
     * @param integer $id      The notification id.
     *
     * @return JsonResponse The response object.
     */
    public function patchAction(Request $request, $id)
    {
        $em           = $this->get('orm.manager');
        $un           = null;
        $userId       = $this->getUser()->id;
        $notification = $this->get('orm.manager')
            ->getRepository('Notification')
            ->find($id);

        try {
            $un = $em->getRepository('user_notification')->find([
                'instance_id'     => $this->get('core.instance')->id,
                'notification_id' => $id,
                'user_id'         => $userId
            ]);
        } catch (EntityNotFoundException $e) {
            $un                  = new UserNotification();
            $un->instance_id     = $this->get('core.instance')->id;
            $un->user_id         = $userId;
            $un->notification_id = $id;
        }

        $un->user = [
            'username' => $this->getUser()->username,
            'email'    => $this->getUser()->email
        ];

        foreach ($request->request->all() as $key => $value) {
            $date = new \Datetime($value);

            // Ignore read_date for fixed notifications
            if ($key !== 'read_date' || !$notification->fixed) {
                $un->{$key} = $date;
            }
        }

        try {
            $em->persist($un);

            return new JsonResponse(_('Notification marked as read successfully'));
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
    }

    /**
     * Updates some instance properties.
     *
     * @param Request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchSelectedAction(Request $request)
    {
        $params   = $request->request->all();
        $instance = $this->get('core.instance')->id;

        if (!array_key_exists('ids', $params)
            || empty($params['ids'])
            || !is_array($params['ids'])
        ) {
            return new JsonResponse(_('Invalid notifications'), 400);
        }

        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('UserNotification');
        $updated   = 0;
        $ids       = $params['ids'];

        unset($params['ids']);

        try {
            $oql = 'instance_id = "%s" and notification_id in [%s] and user_id = "%s"';
            $oql = sprintf($oql, $instance, implode(', ', $ids), $this->getUser()->id);

            $notifications = $em->getRepository('user_notification')
                ->findBy($oql);

            // Update read datetime for existing
            $read = [];
            foreach ($notifications as $notification) {
                $read[] = $notification->notification_id;

                $notification->user = [
                    'username' => $this->getUser()->username,
                    'email'    => $this->getUser()->email
                ];

                $notification->merge($converter->objectify($params));

                $em->persist($notification);
                $updated++;
            }

            // Create new UserNotification for missed
            $missed = array_diff($ids, $read);
            foreach ($missed as $id) {
                $un = new UserNotification();

                $un->instance_id     = $instance;
                $un->user            = [
                    'username' => $this->getUser()->username,
                    'email'    => $this->getUser()->email
                ];
                $un->user_id         = $this->getUser()->id;
                $un->notification_id = (int) $id;
                $un->merge($converter->objectify($params));

                $em->persist($un);
                $updated++;
            }

            return new JsonResponse(sprintf(
                _('%d notifications marked successfully'),
                $updated
            ));
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 400);
        }
    }

    /**
     * Converts a notification to use it in the response.
     *
     * @param Notification $notification The notification to covert.
     */
    private function convertNotification(&$notification, $view = null)
    {
        $notification = $notification->getData();

        if (!empty($notification['title'])
            && array_key_exists(CURRENT_LANGUAGE_SHORT, $notification['title'])
            && !empty($notification['title'][CURRENT_LANGUAGE_SHORT])
        ) {
            $notification['title'] =
                $notification['title'][CURRENT_LANGUAGE_SHORT];
        } else {
            $notification['title'] = $notification['title']['en'];
        }

        if (!empty($notification['body'])
            && array_key_exists(CURRENT_LANGUAGE_SHORT, $notification['body'])
            && !empty($notification['body'][CURRENT_LANGUAGE_SHORT])
        ) {
            $notification['body'] =
                $notification['body'][CURRENT_LANGUAGE_SHORT];
        } else {
            $notification['body'] = $notification['body']['en'];
        }

        $notification['read'] = 0;
        $notification['view'] = 0;

        if (!empty($view) && in_array($notification['id'], array_keys($view))) {
            $notification['view'] = 1;
        }

        $time = $notification['start']->getTimeStamp();

        $notification['day'] = $notification['start']->format('M, d');
        if (time() - $time < 172800) {
            $notification['day'] = _('Yesterday');
        }

        if (time() - $time < 86400) {
            $notification['day'] = _('Today');
        }

        $notification['time'] = $notification['start']->format('H:i');
        $notification['am']   = $notification['start']->format('a');
    }
}
