<?php
/**
 * Handles the actions for the newsletter subscriptors
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for the newsletter subscriptors
 *
 * @package Backend_Controllers
 **/
class NewsletterSubscriptorsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        //Check if module is activated in this onm instance
        \Onm\Module\ModuleManager::checkActivatedOrForward('NEWSLETTER_MANAGER');
    }

    /**
     * Lists all the newsletter subscriptors
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function listAction(Request $request)
    {
        $filters      = $request->query->get('filters');
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        // Build filters for sql
        list($where, $orderBy) = $this->buildFilter($filters);

        $user = new \Subscriptor();
        $users = $user->getUsers($where, ($itemsPerPage*($page-1)) . ',' . $itemsPerPage, $orderBy);

        $total = $user->countUsers($where);

        // Pager
        $pager = \Onm\Pager\Slider::create(
            $total,
            $itemsPerPage,
            $this->generateUrl(
                'admin_newsletter_subscriptors'
            )
        );

        return $this->render(
            'newsletter/subscriptions/list.tpl',
            array(
                'pager' => $pager,
                'users' => $users,
                'total' => $total,
            )
        );
    }

    /**
     * Handles the form for creating a new article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $user = new \Subscriptor();

            $data = array(
                'email'        => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
                'name'         => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
                'firstname'    => $request->request->filter('firstname', '', FILTER_SANITIZE_STRING),
                'lastname'     => $request->request->filter('lastname', '', FILTER_SANITIZE_STRING),
                'subscription' => $request->request->getDigits('subscription', 1),
                'status'       => $request->request->getDigits('status', 2),
            );

            // Check for repeated e-mail
            if ($user->exists_email($data['email'])) {
                m::add(_('Unable to create the new subscriptor. This email is already in use'), m::ERROR);
                return $this->redirect(
                    $this->generateUrl('admin_newsletter_subscriptor_create')
                );
            } else {
                if ($user->create($data)) {
                    m::add(_('Subscription successfully created.'), m::SUCCESS);
                } else {
                    m::add(sprintf(_('Unable to create the new subscriptor: %s', $user->_errors)), m::ERROR);
                }
            }

            $continue = $request->request->filter('continue', 0);
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl('admin_newsletter_subscriptor_show', array('id' => $user->id))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('admin_newsletter_subscriptors')
                );
            }
        } else {
            return $this->render('newsletter/subscriptions/new.tpl');
        }
    }

    /**
     * Handles the form for creating a new article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function updateAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $id = $request->query->getDigits('id');

            $data = array(
                'id'           => $id,
                'email'        => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
                'name'         => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
                'firstname'    => $request->request->filter('firstname', '', FILTER_SANITIZE_STRING),
                'lastname'     => $request->request->filter('lastname', '', FILTER_SANITIZE_STRING),
                'subscription' => $request->request->getDigits('subscription', 1),
                'status'       => $request->request->getDigits('status', 2),
            );

            $user = new \Subscriptor();
            if ($user->update($data, true)) {
                m::add(_('Subscription successfully updated.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the subscriptor information'), m::ERROR);
            }

            $continue = $request->request->filter('continue', 0);
            if ($continue) {
                return $this->redirect(
                    $this->generateUrl('admin_newsletter_subscriptor_show', array('id' => $user->id))
                );
            } else {
                return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
            }
        } else {
            return $this->render('newsletter/subscriptions/new.tpl');
        }
    }

    /**
     * Shows the information about an subscriptor
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $user = new \Subscriptor($id);

        if (is_null($user->id)) {
            m::add(sprintf(_('Unable to find the user with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
        }

        return $this->render(
            'newsletter/subscriptions/new.tpl',
            array('user' => $user,)
        );
    }

    /**
     * Deletes an subscriptor given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function deleteAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $user = new \Subscriptor($id);

        if (is_null($user->id)) {
            m::add(sprintf(_('Unable to find the user with the id "%d"'), $id));
        } else {
            $user->delete($id);

            m::add(sprintf(_('Subscritor with id "%d" deleted sucessfully'), $id));
        }

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
        } else {
            return new Response('Ok', 200);
        }
    }

    /**
     * Toggles the subscription state for a given subscription
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function toggleSubscriptionAction(Request $request)
    {
        $id   = $request->query->getDigits('id', null);

        $user = new \Subscriptor($id);

        $subscription = ($user->subscription + 1) % 2;
        $user->mUpdateProperty($user->id, 'subscription', $subscription);

        return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
    }

    /**
     * Toggles the activated state for a given subscription
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function toggleActivatedAction(Request $request)
    {
        $id   = $request->query->getDigits('id', null);

        $user = new \Subscriptor($id);

        $status = ($user->status == 2) ? 3: 2;
        $user->set_status($id, $status);

        return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
    }

    /**
     * Deletes multiple subscriptors at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function batchDeleteAction(Request $request)
    {
        $ids = $request->query->get('cid');

        if (is_array($ids) && count($ids) > 0) {
            $user = new \Subscriptor();
            $count = 0;
            foreach ($ids as $id) {
                if ($user->delete($id)) {
                    $count++;
                } else {
                    m::add(sprintf(_('Unable to delete the subscriptor with the id %d.'), $id), m::ERROR);
                }
            }

            m::add(sprintf(_('Successfully deleted %d subscriptors.'), $count), m::SUCCESS);
        } else {
            m::add(_('Please specify a subscriptor id for delete it.'), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
    }

    /**
     * Deletes multiple subscriptors at once given its ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('NEWSLETTER_ADMIN')")
     **/
    public function batchSubscribeAction(Request $request)
    {
        $ids = $request->query->get('cid');
        $state = $request->query->getDigits('subscribe', 1);

        if (is_array($ids) && count($ids) > 0) {
            $user = new \Subscriptor();

            foreach ($ids as $id) {
                $data[] = array(
                    'id'    => $id,
                    'value' => $state
                );
            }

            $user->mUpdateProperty($data, 'subscription');

            m::add(sprintf(_('Successfully changed subscribed state for %d subscriptors.'), count($ids)), m::SUCCESS);
        } else {
            m::add(_('Please specify a subscriptor id for change its subscribed state it.'), m::ERROR);
        }

        return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
    }

    /**
     * Builds the search filter
     *
     * @param array $filters the list of filters to take in place
     *
     * @return array a tuple with the where and the orderby SQL clause
     **/
    private function buildFilter($filters)
    {
        $orderBy = 'name, email';

        $fltr = array();
        if (isset($filters['text'])
            && !empty($filters['text'])
        ) {
            $fltr[] = "name LIKE '%".addslashes($filters['text'])."%' OR ".
                      "email LIKE '%".addslashes($filters['text'])."%'";
        }

        if (isset($filters['subscription']) && ($filters['subscription']>=0)) {
            $fltr[] = '`subscription`=' . $filters['subscription'];
        }

        if (isset($filters['status']) && ($filters['status']>=0)) {
            $fltr[] = '`status`=' . $filters['status'];
        }

        $where = null;
        if (count($fltr) > 0) {
            $where = implode(' AND ', $fltr);
        }

        return array($where, $orderBy);
    }
}
