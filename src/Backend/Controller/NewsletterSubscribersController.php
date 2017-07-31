<?php
/**
 * Handles the actions for the newsletter subscribers
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
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the newsletter subscribers
 *
 * @package Backend_Controllers
 */
class NewsletterSubscribersController extends Controller
{
    /**
     * Lists all the newsletter subscribers
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        return $this->render('newsletter/subscriptions/list.tpl');
    }

    /**
     * Handles the form for creating a new article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('newsletter/subscriptions/new.tpl');
        }
        $user = new \Subscriber();

        $data = array(
            'email'        => $request->request->filter('email', '', FILTER_SANITIZE_STRING),
            'name'         => $request->request->filter('name', '', FILTER_SANITIZE_STRING),
            'firstname'    => $request->request->filter('firstname', '', FILTER_SANITIZE_STRING),
            'lastname'     => $request->request->filter('lastname', '', FILTER_SANITIZE_STRING),
            'subscription' => $request->request->getDigits('subscription', 1),
            'status'       => $request->request->getDigits('status', 2),
        );

        // Check for repeated e-mail
        if ($user->existsEmail($data['email'])) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to create the new subscriber. This email is already in use')
            );

            return $this->redirect(
                $this->generateUrl('admin_newsletter_subscriptor_create')
            );
        } else {
            if ($user->create($data)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Subscription successfully created.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    sprintf(_('Unable to create the new subscriber: %s', $user->_errors))
                );
            }
        }

        return $this->redirect(
            $this->generateUrl('admin_newsletter_subscriptor_show', array('id' => $user->id))
        );
    }

    /**
     * Handles the form for creating a new article
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function updateAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->render('newsletter/subscriptions/new.tpl');
        }

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

        $user = new \Subscriber();
        if ($user->update($data, true)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Subscription successfully updated.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to update the subscriber information')
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_newsletter_subscriptor_show', [ 'id' => $user->id ])
        );
    }

    /**
     * Shows the information about an subscriptor
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWSLETTER_MANAGER')
     *     and hasPermission('NEWSLETTER_ADMIN')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $user = new \Subscriber($id);

        if (is_null($user->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the user with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_newsletter_subscriptors'));
        }

        return $this->render(
            'newsletter/subscriptions/new.tpl',
            array('user' => $user,)
        );
    }
}
