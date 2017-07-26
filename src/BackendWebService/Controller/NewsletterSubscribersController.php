<?php
/**
 * Handles the actions for the newsletters
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
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
        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $page            = $request->query->getDigits('page', 1);
        $search          = $request->query->get('search', '');

        // Build filters for sql
        list($where, $orderBy) = $this->buildFilter($search);

        $sb = new \Subscriber();
        $subscribers = $sb->getUsers($where, ($elementsPerPage*($page-1)) . ',' . $elementsPerPage, $orderBy);
        $subscribers = \Onm\StringUtils::convertToUtf8($subscribers);

        $total = $sb->countUsers($where);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $subscribers,
                'total'             => $total,
            )
        );
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
            $user = new \Subscriber($id);
            $result = $user->delete($id);

            if ($user->id && $result) {
                $success[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Subscritor with id "%d" deleted successfully'), $id),
                    'type'    => 'success'
                );
            } else {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to find the user with the id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'message' => _('You must provide an id to delete a newsletter subscriber.'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors),
            )
        );
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
        $id   = $request->query->getDigits('id', null);

        $user = new \Subscriber($id);

        $subscription = ($user->subscription + 1) % 2;
        $toggled = $user->setSubscriptionStatus($user->id, $subscription);

        if ($toggled) {
            $messages = array(
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            );
        } else {
            $messages = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'subscription' => $subscription,
                'messages'       => $messages
            )
        );
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
        $id   = $request->query->getDigits('id', null);

        $user = new \Subscriber($id);

        $status = ($user->status == 2) ? 3: 2;
        $toggled = $user->setStatus($id, $status);

        if ($toggled) {
            $messages = array(
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            );
        } else {
            $messages = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'status'   => $status,
                'messages' => $messages
            )
        );
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
        $ids = $request->request->get('selected');
        $errors  = array();
        $success = array();
        $updated = array();

        if (is_array($ids) && count($ids) > 0) {
            $user = new \Subscriber();
            $count = 0;
            foreach ($ids as $id) {
                if ($user->delete($id)) {
                    $updated[] = $id;
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(
                    sprintf(_('Successfully deleted %d subscribers.'), count($updated))
                ),
                'type'    => 'success'
            );
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
        $ids = $request->request->get('ids');
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
        $ids = $request->request->get('ids');
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

    /**
     * Builds the search filter
     *
     * @param array $filters the list of filters to take in place
     *
     * @return array a tuple with the where and the orderby SQL clause
     */
    private function buildFilter($filters)
    {
        $orderBy = 'name, email';


        $fltr = array();
        if (isset($filters['title'])
            && !empty($filters['title'])
        ) {
            $fltr[] = "(name LIKE '".addslashes($filters['title'][0]['value'])."' OR ".
                      "email LIKE '".addslashes($filters['title'][0]['value'])."')";
        }

        if (isset($filters['subscription']) && ($filters['subscription'][0]['value']>=0)) {
            $fltr[] = '`subscription`=' . $filters['subscription'][0]['value'];
        }

        if (isset($filters['status']) && ($filters['status'][0]['value']>=0)) {
            $fltr[] = '`status`=' . $filters['status'][0]['value'];
        }

        $where = null;
        if (count($fltr) > 0) {
            $where = implode(' AND ', $fltr);
        }

        return array($where, $orderBy);
    }
}
