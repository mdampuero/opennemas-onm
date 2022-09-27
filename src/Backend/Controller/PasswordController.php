<?php

namespace Backend\Controller;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
                sprintf(_(
                    'The password reset request cannot be found. '
                    . 'Please check the link we sent you in the email. '
                    . 'Remember that the link is for single use. '
                    . 'You can request a new one <a href="%s">here</a>.'
                ), $this->generateUrl('backend_password_reset'))
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

            $url = $this->get('router')->generate('backend_password_reset');

            return new RedirectResponse(
                $this->get('core.decorator.url')->prefixUrl($url)
            );
        }

        // Generate and update user with new token
        $token = md5(uniqid(mt_rand(), true));
        $user->merge([ 'token' => $token ]);
        $em->persist($user);

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ 'site_name', 'site_title' ]);

        $mailSubject = sprintf(
            _('Password reminder for %s'),
            $settings['site_title']
        );

        $url = $this->get('router')->generate(
            'backend_password_change',
            [ 'token' => $token ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $mailBody = $this->get('core.template.admin')
            ->render('login/emails/recoverpassword.tpl', [
                'user' => $user,
                'url'  => $this->get('core.decorator.url')->prefixUrl($url)
            ]);

        //  Build the message
        $message = \Swift_Message::newInstance();
        $message
            ->setSubject($mailSubject)
            ->setBody($mailBody, 'text/plain')
            ->setTo($user->email)
            ->setFrom([
                'no-reply@postman.opennemas.com' => $settings['site_name']
            ]);

        $headers = $message->getHeaders();
        $headers->addParameterizedHeader(
            'ACUMBAMAIL-SMTPAPI',
            $this->get('core.instance')->internal_name . ' - Backend Recover Password'
        );

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
                sprintf(_(
                    'The password reset request cannot be found. '
                    . 'Please check the link we sent you in the email. '
                    . 'Remember that the link is for single use. '
                    . 'You can request a new one <a href="%s">here</a>.'
                ), $this->generateUrl('backend_password_reset'))
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
                $this->generateUrl('backend_password_change', [ 'token' => $token ])
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
