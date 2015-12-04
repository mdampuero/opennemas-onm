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
        $criteria = $request->query->filter('search') ? : [];
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);

        $id = $this->get('instance')->id;

        $unr = $this->get('orm.manager')->getRepository('user_notification');

        // Get readed notifications
        $notifications = $unr->findBy([
            'user_id' => [ [ 'value' => $this->getUser()->id ] ]
        ]);

        $read = [];
        foreach ($notifications as $un) {
            $read[$un->notification_id] = $un->read_time;
        }

        $date = new \DateTime('now');
        $date = $date->format('Y-m-d H:i:s');

        $criteria = 'instance_id IN (0, ' . $id . ')'
            . ' AND (start <= \'' . $date
            . '\') AND (end IS NULL OR end > \'' . $date . '\')';

        $nr = $this->get('orm.manager')->getRepository('manager.notification');

        $notifications = $nr->findBy($criteria, [ 'fixed' => 'desc' ], $epp, $page);

        foreach ($notifications as &$notification) {
            $notification = $notification->getData();

            $notification['read'] = 0;

            $notification['title'] = $notification['title'][CURRENT_LANGUAGE];
            $notification['body'] = $notification['body'][CURRENT_LANGUAGE];

            $date = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $notification['start']
            );

            $notification['day'] = $date->format('l');
            $time = $date->getTimeStamp();

            $notification['day'] = $date->format('M, d');
            if (time() - $time < 172800) {
                $notification['day'] = _('Yesterday');
            }

            if (time() - $time < 86400) {
                $notification['day'] = _('Today');
            }

            if (in_array($notification['id'], array_keys($read))
                && $notification['start'] <= $read[$notification['id']]
            ) {
                $notification['read'] = 1;
            }

            $notification['time'] = $date->format('H:i');
            $notification['am'] = $date->format('a');
        }

        $total = $nr->countBy($criteria);

        return new JsonResponse([
            'epp'     => $epp,
            'page'    => $page,
            'results' => $notifications,
            'total'   => $total,
            'extra'   => $this->getTemplateParams()
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
        $criteria = $request->query->filter('search') ? : [];
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);

        $id = $this->get('instance')->id;

        $criteria['instance_id'] = [
            [ 'value' => [ 0, $id ], 'operator' => 'IN' ]
        ];

        $criteria['start'] = [
            [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<' ]
        ];

        $nr = $this->get('orm.manager')->getRepository('manager.notification');

        $notifications = $nr->findBy($criteria, [ 'start' => 'desc' ], $epp, $page);

        foreach ($notifications as &$notification) {
            $notification = $notification->getData();

            $notification['title'] = $notification['title'][CURRENT_LANGUAGE];
            $notification['body'] = $notification['body'][CURRENT_LANGUAGE];

            $date = \DateTime::createFromFormat(
                'Y-m-d H:i:s',
                $notification['start']
            );

            $notification['day'] = $date->format('l');
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

        $total = $nr->countBy($criteria);

        return new JsonResponse([
            'epp'     => $epp,
            'page'    => $page,
            'results' => $notifications,
            'total'   => $total,
            'extra'   => $this->getTemplateParams()
        ]);
    }

    /**
     * Updates some instance properties.
     *
     * @param integer $id The notification id.
     *
     * @return JsonResponse The response object.
     */
    public function patchAction($id)
    {
        $em = $this->get('orm.manager');

        $userId = $this->getUser()->id;

        try {
            $un = $em->getRepository('user_notification')->find(
                [ 'notification_id' => $id, 'user_id' => $userId ]
            );

            $un->read = date('Y-m-d H:i:s');
            $em->persist($un);

            return new JsonResponse(_('Notification marked as read successfully'));
        } catch (EntityNotFoundException $e) {
            $un = new UserNotification();
            $un->user_id = $userId;
            $un->notification_id = $id;
            $un->read = date('Y-m-d H:i:s');

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
        $ids = $request->request->get('ids');

        if (empty($ids) || !is_array($ids)) {
            return new JsonResponse(_('Invalid notifications'), 400);
        }

        $em      = $this->get('orm.manager');
        $updated = 0;

        try {
            $criteria = [
                'notification_id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ],
                'user_id' => [ [ 'value' => $this->getUser()->id ] ]
            ];

            $notifications = $em->getRepository('user_notification')
                ->findBy($criteria);

            // Update read datetime for existing
            $read = [];
            foreach ($notifications as $notification) {
                $read[] = $notification->id;

                $notification->read_time = date('Y-m-d H:i:s');
                $em->persist($notification);
                $updated++;
            }

            // Create new UserNotification for missed
            $missed = array_diff($ids, $read);
            foreach ($missed as $id) {
                $un = new UserNotification();

                $un->user_id         = $this->getUser()->id;
                $un->notification_id = $id;
                $un->read_time       = date('Y-m-d H:i:s');

                $em->persist($un);
                $updated++;
            }

            return new JsonResponse(sprintf(
                _('%d notifications marked as read successfully'),
                $updated
            ));
        } catch (\Exception $e) {
            return new JsonResponse(_($e->getMessage()), 400);
        }
    }

    /**
     * Returns an array of parameters to use in template.
     *
     * @return array The array of parameters.
     */
    private function getTemplateParams()
    {
        $params = [
            'types' => [
                [ 'name' => 'All', 'value' => '' ],
                [ 'name' => 'Email', 'value' => 'email' ],
                [ 'name' => 'Help', 'value' => 'help' ],
                [ 'name' => 'Media', 'value' => 'media' ],
                [ 'name' => 'User', 'value' => 'user' ]
            ]
        ];

        return $params;
    }
}
