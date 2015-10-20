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

use Framework\ORM\Entity\Notification;
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
    public function listAction(Request $request)
    {
        $criteria = $request->query->filter('search') ? : [];
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);

        $id = $this->get('instance')->id;

        $criteria['instance_id'] = [
            [ 'value' => [ 0, $id ], 'operator' => 'IN' ]
        ];

        // Filter to get latest notifications
        if (is_array($criteria)
            && array_key_exists('fixed', $criteria)
            && array_key_exists('is_read', $criteria)
        ) {
            $criteria = 'instance_id IN (0, ' . $id . ')AND (fixed = '
                . $criteria['fixed'][0]['value'] . ' OR is_read = '
                . $criteria['is_read'][0]['value'] .')';
        }

        $nr = $this->get('orm.manager')->getRepository('manager.notification');

        $notifications = $nr->findBy($criteria, [ 'start' => 'desc' ], $epp, $page);

        foreach ($notifications as &$notification) {
            $notification = $notification->getData();

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

        try {
            $notification = $em->getRepository('manager.notification')->find($id);
            $notification->is_read = true;

            $em->persist($notification);

            return new JsonResponse(_('Notification marked as read successfully'));
        } catch (InstanceNotFoundException $e) {
            return new JsonResponse(
                sprintf(_('Unable to find the notification with id "%s"'), $id),
                404
            );
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
            $criteria = [ 'id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ] ];

            $notifications = $em->getRepository('manager.notification')
                ->findBy($criteria);

            foreach ($notifications as $notification) {
                $notification->is_read = true;
                $em->persist($notification);
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
