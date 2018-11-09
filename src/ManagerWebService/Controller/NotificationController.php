<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Entity\Notification;
use League\Csv\Writer;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns, saves, modifies and removes notifications.
 */
class NotificationController extends Controller
{
    /**
     * Returns a list of targets basing on the request.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('NOTIFICATION_LIST')")
     */
    public function autocompleteAction(Request $request)
    {
        $target   = [];
        $query    = strtolower($request->query->get('query'));
        $security = $this->get('core.security');

        if ($security->hasPermission('MASTER')
            && (empty($query)
                || strpos(strtolower(_('All')), strtolower($query)) !== false)
        ) {
            $target[] = [ 'id' => 'all', 'name' => _('All') ];
        }

        if ($security->hasPermission('MASTER')
            && (empty($query)
                || strpos('manager', strtolower($query)) !== false)
        ) {
            $target[] = [ 'id' => 'manager', 'name' => 'manager' ];
        }

        $oql = '';
        if (!$security->hasPermission('MASTER')
            && $security->hasPermission('PARTNER')
        ) {
            $oql = sprintf('owner_id = "%s" ', $this->get('core.user')->id);
        }

        if (!empty($query)) {
            if (!empty($oql)) {
                $oql .= 'and ';
            }

            $oql .= '(internal_name ~ "%s" or name ~ "%s" or domains ~ "%s") ';
            $oql  = sprintf($oql, $query, $query, $query);
        }

        $oql .= 'order by internal_name asc limit 10';

        $instances = $this->get('orm.manager')->getRepository('instance')
            ->findBy($oql);

        foreach ($instances as $instance) {
            $target[] = [
                'id'      => $instance->internal_name,
                'name'    => $instance->internal_name,
                'domains' => $instance->getMainDomain()
            ];
        }

        $oql = 'order by uuid asc limit 10';

        if (!empty($query)) {
            $oql = 'uuid ~ "%s" ' . $oql;
            $oql  = sprintf($oql, $query);
        }

        if ($security->hasPermission('MASTER')) {
            $themes = $this->get('orm.manager')->getRepository('Theme')
                ->findBy($oql);

            foreach ($themes as $theme) {
                $target[] = [
                    'id'   => $theme->uuid,
                    'name' => $theme->uuid
                ];
            }
        }

        return new JsonResponse([ 'target' => $target ]);
    }

    /**
     * Deletes a notification.
     *
     * @param integer $id The notification id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('NOTIFICATION_DELETE')")
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
     *
     * @Security("hasPermission('NOTIFICATION_DELETE')")
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
     * Returns the report about the number of times a notification has been
     * read, view, clicked and opened.
     *
     * @param integer $id The notification id.
     *
     * @return Response The response object
     *
     * @Security("hasPermission('NOTIFICATION_REPORT')")
     */
    public function exportAction($id)
    {
        $sql = 'SELECT notification_id, title, instance_id, internal_name,'
                . 'contact_mail, count(read_date) as "read", count(view_date) as "view",'
                . ' count(click_date) as "clicked", count(open_date) as "opened"'
            . ' FROM user_notification, notification, instances'
            . ' WHERE notification_id = notification.id'
                . ' AND notification_id = ? AND instance_id = instances.id'
            . ' GROUP BY notification_id, instance_id';

        $data = $this->get('orm.manager')->getConnection('manager')
            ->fetchAll($sql, [ $id ]);
        $data = array_map(function ($a) {
            $title = unserialize($a['title']);

            if (!empty($title) && is_array($title)) {
                $a['title'] = array_shift($title);
            }

            $a['title'] = str_replace("\n", '', $a['title']);

            return $a;
        }, $data);

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setInputEncoding('utf-8');
        $writer->insertOne([ 'id', 'title', 'instance_id', 'instance',
            'contact', 'read', 'view', 'clicked', 'opened' ]);

        $writer->insertAll($data);

        $response = new Response();
        $response->setContent($writer);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename=report-notification-' . $id . '.csv');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Returns the report with information about how many times any notification
     * has been read, view, clicked and opened.
     *
     * @return Response The response object
     *
     * @Security("hasPermission('NOTIFICATION_REPORT')")
     */
    public function exportAllAction()
    {
        $data = $this->getNotificationCounters();
        $data = array_map(function ($a) {
            $title = unserialize($a['title']);

            if (!empty($title) && is_array($title)) {
                $a['title'] = array_shift($title);
            }

            $a['title'] = str_replace("\n", '', $a['title']);

            return $a;
        }, $data);

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setInputEncoding('utf-8');
        $writer->insertOne([ 'id', 'title', 'read', 'view', 'clicked', 'opened' ]);

        $writer->insertAll($data);

        $response = new Response();
        $response->setContent($writer);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename=report-notifications.csv');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }

    /**
     * Returns the list of notifications.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('NOTIFICATION_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $extra      = $this->getTemplateParams();
        $repository = $this->get('orm.manager')->getRepository('Notification');
        $converter  = $this->get('orm.manager')->getConverter('Notification');

        $total         = $repository->countBy($oql);
        $notifications = $repository->findBy($oql);

        $ids = array_map(function ($a) {
            return $a->id;
        }, $notifications);

        $counters = $this->getNotificationCounters($ids);

        $extra['stats'] = [];
        foreach ($counters as $counter) {
            $extra['stats'][$counter['notification_id']] = $counter;
        }

        $ids = [];
        foreach ($notifications as &$notification) {
            if (empty($notification->target)) {
                $notification->target = [];
            }

            $notification = $converter->responsify($notification->getData());
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
     *
     * @Security("hasPermission('NOTIFICATION_CREATE')")
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
     *
     * @Security("hasPermission('NOTIFICATION_UPDATE')")
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
     *
     * @Security("hasPermission('NOTIFICATION_UPDATE')")
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
     *
     * @Security("hasPermission('NOTIFICATION_CREATE')")
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
                'manager_ws_notification_show',
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
     *
     * @Security("hasPermission('NOTIFICATION_UPDATE')")
     */
    public function showAction($id)
    {
        $em           = $this->get('orm.manager');
        $converter    = $em->getConverter('Notification');
        $notification = $em->getRepository('Notification')->find($id);

        $extra        = $this->getTemplateParams();
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
     *
     * @return Response          The response object.
     *
     * @Security("hasPermission('NOTIFICATION_UPDATE')")
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
            $notification->start = new \DateTime();
        }

        $em->persist($notification);
        $msg->add(_('Notification saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns counters for all notifications.
     *
     * @return array The list of notification counters.
     */
    protected function getNotificationCounters($ids = [])
    {
        $sql = 'SELECT notification.id as notification_id, title, count(read_date) as "read",'
            . ' COUNT(view_date) as "view", COUNT(click_date) as "clicked",'
            . ' COUNT(open_date) as "opened"'
            . ' FROM notification LEFT JOIN user_notification ON notification.id = notification_id';

        if (!empty($ids)) {
            $sql .= sprintf(' WHERE notification_id in (%s)', implode(',', $ids));
        }

        $sql .= ' GROUP BY notification.id ORDER BY id DESC';

        return $this->get('orm.manager')->getConnection('manager')->fetchAll($sql);
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

        $params['target'] = [
            [ 'id' => 'manager', 'name' => 'manager' ],
            [ 'id' => 'all', 'name' => _('All') ]
        ];

        $params['languages'] = [
            'en' => _('English'),
            'es' => _('Spanish'),
            'gl' => _('Galician'),
        ];

        $themes = $this->get('orm.manager')->getRepository('Theme')->findBy();

        foreach ($themes as $theme) {
            $params['target'][] = [
                'id'   => $theme->uuid,
                'name' => $theme->uuid
            ];
        }

        return $params;
    }
}
