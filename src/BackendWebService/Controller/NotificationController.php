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

use Framework\ORM\Exception\EntityNotFoundException;
use Framework\ORM\Entity\Notification;
use Framework\ORM\Entity\UserNotification;
use Onm\Framework\Controller\Controller;
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
        $date  = new \DateTime('now');
        $date  = $date->format('Y-m-d H:i:s');
        $epp   = $request->query->getDigits('epp', 10);
        $id    = $this->get('instance')->internal_name;
        $theme = $this->get('instance')->settings['TEMPLATE_USER'];
        $page  = $request->query->getDigits('page', 1);

        $read = $this->get('core.event_dispatcher')->dispatch(
            'notifications.getRead',
            [
                'instance_id' => $this->get('instance')->id,
                'user_id'     => $this->getUser()->id
            ]
        );

        $criteria = '(target LIKE \'%"' . $id . '"%\' OR '
            . 'target LIKE \'%"' . $theme . '"%\' OR '
            . 'target LIKE \'%"all"%\') AND enabled = 1 AND (start <= \''
            . $date . '\') AND (end IS NULL OR end > \'' . $date . '\')';

        if (!empty($read)) {
            $criteria .= ' AND id NOT IN ( ' . implode(', ', array_keys($read))
                . ' )';
        }

        if (!$this->getUser()->isAdmin()) {
            $criteria .= ' AND users != 1';
        }

        $notifications = $this->get('core.event_dispatcher')->dispatch(
            'notifications.get',
            [
                'criteria' => $criteria,
                'epp'      => $epp,
                'order'    => [ 'fixed' => 'desc' ],
                'page'     => $page
            ]
        );

        if (is_array($notifications)) {
            foreach ($notifications as &$notification) {
                $this->convertNotification($notification);
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
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $id    = $this->get('instance')->internal_name;
        $date  = date('Y-m-d H:i:s');
        $theme = $this->get('instance')->settings['TEMPLATE_USER'];
        $epp   = $request->query->get('epp', 10);
        $page  = $request->query->get('page', 1);

        $criteria = 'target LIKE \'%"' . $id . '"%\' OR '
            . 'target LIKE \'%"' . $theme . '"%\' OR '
            .  'target LIKE \'%"all"%\' AND enabled = 1 AND (start <= \''
            . $date . '\') AND (end IS NULL OR end > \'' . $date . '\')';

        if (!$this->getUser()->isAdmin()) {
            $criteria .= ' AND users != 1';
        }

        $notifications = $this->get('core.event_dispatcher')->dispatch(
            'notifications.get',
            [
                'criteria' => $criteria,
                'epp'      => $epp,
                'order'    => [ 'fixed' => 'desc' ],
                'page'     => $page
            ]
        );

        if (is_array($notifications)) {
            foreach ($notifications as &$notification) {
                $this->convertNotification($notification);
            }
        }

        $total = $this->get('core.event_dispatcher')
            ->dispatch('notifications.count', [ 'criteria' => $criteria ]);

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
        $em     = $this->get('orm.manager');
        $un     = null;
        $userId = $this->getUser()->id;

        try {
            $un = $em->getRepository('manager.UserNotification')->find([
                'instance_id'     => $this->get('instance')->id,
                'notification_id' => $id,
                'user_id'         => $userId
            ]);
        } catch (EntityNotFoundException $e) {
            $un = new UserNotification();
            $un->instance_id     = $this->get('instance')->id;
            $un->user_id         = $userId;
            $un->user            = $this->getUser();
            $un->notification_id = $id;
        }

        foreach ($request->request->all() as $key => $value) {
            $date = new \Datetime($value);
            $un->{$key} = $date->format('Y-m-d H:i:s');
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
        $instance = $this->get('instance')->id;
        $ids      = $request->request->get('ids');

        if (!array_key_exists('ids', $params)
            || empty($params['ids'])
            || !is_array($params['ids'])
        ) {
            return new JsonResponse(_('Invalid notifications'), 400);
        }

        $em      = $this->get('orm.manager');
        $updated = 0;
        $ids     = $params['ids'];

        unset($params['ids']);

        try {
            $criteria = [
                'instance_id'     => [ [ 'value' => $instance ] ],
                'notification_id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ],
                'user_id'         => [ [ 'value' => $this->getUser()->id ] ]
            ];

            $notifications = $em->getRepository('manager.UserNotification')
                ->findBy($criteria);

            // Update read datetime for existing
            $read = [];
            foreach ($notifications as $notification) {
                $read[] = $notification->notification_id;

                $notification->user = $this->getUser()->username;

                foreach ($params as $key => $value) {
                    $date = new \Datetime($value);
                    $notification->{$key} = $date->format('Y-m-d H:i:s');
                }

                $em->persist($notification);
                $updated++;
            }

            // Create new UserNotification for missed
            $missed = array_diff($ids, $read);
            foreach ($missed as $id) {
                $un = new UserNotification();

                $un->instance_id     = $instance;
                $un->user            = $this->getUser()->username;
                $un->user_id         = $this->getUser()->id;
                $un->notification_id = $id;

                foreach ($params as $key => $value) {
                    $date = new \Datetime($value);
                    $un->{$key} = $date->format('Y-m-d H:i:s');
                }

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
    private function convertNotification(&$notification)
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
            $notification['body']  = $notification['body']['en'];
        }

        $notification['read']  = 0;

        $date = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $notification['start']
        );

        $time = $date->getTimeStamp();

        $notification['day'] = $date->format('M, d');
        if (time() - $time < 172800) {
            $notification['day'] = _('Yesterday');
        }

        if (time() - $time < 86400) {
            $notification['day'] = _('Today');
        }

        $notification['time'] = $date->format('H:i');
        $notification['am'] = $date->format('a');
    }
}
