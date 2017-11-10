<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the newsletters
 *
 * @package Backend_Controllers
 */
class NewsletterSubscribersController extends Controller
{
    /**
     * Lists nwesletters and perform searches across them
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');
        $sb  = new \Subscriber();

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $subscribers = $sb->getUsers($criteria, $epp * ($page - 1) . ',' . $epp, $order);
        $subscribers = \Onm\StringUtils::convertToUtf8($subscribers);

        $total = $sb->countUsers($criteria);

        return new JsonResponse([
            'results' => $subscribers,
            'total'   => $total,
        ]);
    }

    /**
     * Deletes an newsletter subscriptor given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $errors = $success = [];
        if (!empty($id)) {
            $user   = new \Subscriber($id);
            $result = $user->delete($id);

            if ($user->id && $result) {
                $success[] = [
                    'id'      => $id,
                    'message' => sprintf(_('Subscritor with id "%d" deleted successfully'), $id),
                    'type'    => 'success'
                ];
            } else {
                $errors[] = [
                    'id'      => $id,
                    'message' => sprintf(_('Unable to find the user with the id "%d"'), $id),
                    'type'    => 'error'
                ];
            }
        } else {
            $errors[] = [
                'message' => _('You must provide an id to delete a newsletter subscriber.'),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([ 'messages' => array_merge($success, $errors) ]);
    }

    /**
     * Toggles the subscription state for a given subscription
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function toggleSubscriptionAction(Request $request)
    {
        $id           = $request->query->getDigits('id', null);
        $user         = new \Subscriber($id);
        $subscription = ($user->subscription + 1) % 2;
        $toggled      = $user->setSubscriptionStatus($user->id, $subscription);

        if ($toggled) {
            $messages = [
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            ];
        } else {
            $messages = [
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([
            'subscription' => $subscription,
            'messages'     => $messages
        ]);
    }

    /**
     * Toggles the activated state for a given subscription
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function toggleActivatedAction(Request $request)
    {
        $id      = $request->query->getDigits('id', null);
        $user    = new \Subscriber($id);
        $status  = ($user->status == 2) ? 3 : 2;
        $toggled = $user->setStatus($id, $status);

        if ($toggled) {
            $messages = [
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            ];
        } else {
            $messages = [
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([
            'status'   => $status,
            'messages' => $messages
        ]);
    }

    /**
     * Deletes multiple subscriptors at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function batchDeleteAction(Request $request)
    {
        $ids     = $request->request->get('selected');
        $errors  = [];
        $success = [];
        $updated = [];

        if (is_array($ids) && count($ids) > 0) {
            $user = new \Subscriber();

            foreach ($ids as $id) {
                if ($user->delete($id)) {
                    $updated[] = $id;
                } else {
                    $errors[] = [
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    ];
                }
            }
        }

        if ($updated > 0) {
            $success[] = [
                'id'      => $updated,
                'message' => sprintf(
                    sprintf(_('Successfully deleted %d subscribers.'), count($updated))
                ),
                'type'    => 'success'
            ];
        }

        return new JsonResponse([
            'messages'  => array_merge($success, $errors)
        ]);
    }

    /**
     * Deletes multiple subscribers at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function batchSubscribeAction(Request $request)
    {
        $ids   = $request->request->get('ids');
        $state = $request->request->getDigits('value', 1);

        if (!is_array($ids) || count($ids) == 0) {
            return new JsonResponse([
                'messages' => [
                    'id'      => 500,
                    'message' => _('Please specify a subscriber id for change its subscribed state it.'),
                    'type'    => 'error'
                ]
            ]);
        }

        $user = new \Subscriber();

        foreach ($ids as $id) {
            $user->setSubscriptionStatus($id, $state);
        }

        return new JsonResponse([
            'subscribed' => $state,
            'messages' => [
                [
                    'id'      => count($ids),
                    'message' => sprintf(_('Successfully changed subscribed state for %d subscribers.'), count($ids)),
                    'type'    => 'success'
                ]
            ]
        ]);
    }

    /**
     * Deletes multiple subscriptors at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function batchActivatedAction(Request $request)
    {
        $ids   = $request->request->get('ids');
        $state = $request->request->getDigits('value', 1);

        if (!is_array($ids) || count($ids) == 0) {
            return new JsonResponse([
                'messages' => [
                    'id'      => 500,
                    'message' => _('Please specify a subscriber id to change the activated state.'),
                    'type'    => 'error'
                ]
            ]);
        }

        $user = new \Subscriber();

        foreach ($ids as $id) {
            $user->setStatus($id, $state);
        }

        return new JsonResponse([
            'status' => $state,
            'messages' => [
                [
                    'id'      => count($ids),
                    'message' => sprintf(_('Successfully changed activated state for %d subscribers.'), count($ids)),
                    'type'    => 'success'
                ]
            ]
        ]);
    }
}
