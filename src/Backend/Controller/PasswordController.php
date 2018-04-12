<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PasswordController extends Controller
{
    /**
     * Displays a form to update the user's password.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function changeAction(Request $request)
    {
        $token = $request->query->get('token', null);

        try {
            if (empty($token)) {
                throw new \InvalidArgumentException('Empty token');
            }

            $this->get('orm.manager')->getRepository('User')
                ->findOneBy("token = '$token'");
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to find the password reset request. Please check the url we sent you in the email.')
            );

            return new RedirectResponse(
                $this->generateUrl('backend_authentication_login')
            );
        }

        return $this->render('password/change.tpl', [
            'locale'  => $this->get('core.locale')->getLocale(),
            'locales' => $this->get('core.locale')->getAvailableLocales(),
            'token'   => $token,
        ]);
    }

    /**
     * Displays a form to request a password recovery.
     *
     * @return Response The response object.
     */
    public function resetAction()
    {
        return $this->render('password/reset.tpl', [
            'locale'  => $this->get('core.locale')->getLocale(),
            'locales' => $this->get('core.locale')->getAvailableLocales()
        ]);
    }

    /**
     * Sends an email with a link to reset user password.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function requestAction(Request $request)
    {
        $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
        $em    = $this->get('orm.manager');

        try {
            $user = $em->getRepository('User')->findOneBy("email = '$email'");
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()
                ->add('error', _('Unable to find an user with that email.'));

            return new RedirectResponse(
                $this->get('router')->generate('backend_password_reset')
            );
        }

        // Generate and update user with new token
        $token = md5(uniqid(mt_rand(), true));
        $user->merge([ 'token' => $token ]);
        $em->persist($user);

        $mailSubject = sprintf(
            _('Password reminder for %s'),
            $this->get('setting_repository')->get('site_title')
        );

        $mailBody = $this->renderView('login/emails/recoverpassword.tpl', [
            'user' => $user,
            'url'  => $this->get('router')->generate(
                'backend_password_change',
                [ 'token' => $token ],
                true
            ),
        ]);

        //  Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($mailSubject)
            ->setBody($mailBody, 'text/plain')
            ->setTo($user->email)
            ->setFrom([
                'no-reply@postman.opennemas.com' =>
                    $this->get('setting_repository')->get('site_name')
            ]);

        try {
            $mailer = $this->get('mailer');
            $mailer->send($message);

            $this->get('application.log')->info(
                "Email sent. Backend restore user password (to: " . $user->email . ")"
            );

            $this->view->assign([ 'mailSent' => true, 'user' => $user ]);
        } catch (\Exception $e) {
            // Log this error
            $this->get('application.log')->error(
                "Unable to send the recover password email for the "
                . "user {$user->id}: " . $e->getMessage()
            );

            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to send your recover password email. Please try it later.')
            );
        }

        return $this->render('password/request.tpl');
    }

    /**
     * Updates the password for an user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function updateAction(Request $request)
    {
        $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
        $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);
        $session        = $request->getSession();
        $token          = $request->query->get('token', null);

        try {
            if (empty($token)) {
                throw new \InvalidArgumentException('Empty token');
            }

            $user = $this->get('orm.manager')->getRepository('User')
                ->findOneBy("token = '$token'");
        } catch (\Exception $e) {
            $session->getFlashBag()->add(
                'error',
                _('Unable to find the password reset request. Please check the url we sent you in the email.')
            );

            return new RedirectResponse(
                $this->generateUrl('backend_authentication_login')
            );
        }

        if ($password !== $passwordVerify) {
            $session->getFlashBag()->add(
                'error',
                _('Password and confirmation must be equal.')
            );

            return new RedirectResponse(
                $this->generateUrl('backend_authentication_login')
            );
        }

        $user->password = md5($password);
        $user->token    = null;

        try {
            $this->get('orm.manager')->persist($user);

            $session->getFlashBag()
                ->add('success', _('Password successfully updated'));
        } catch (\Exception $e) {
            $this->get('error.log')->error($e->getMessage());
            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to update your password.')
            );
        }

        return new RedirectResponse(
            $this->generateUrl('backend_authentication_login')
        );
    }
}
