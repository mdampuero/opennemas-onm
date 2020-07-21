<?php

namespace Frontend\Controller;

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
                sprintf(_(
                    'The password reset request cannot be found. '
                    . 'Please check the link we sent you in the email. '
                    . 'Remember that the link is for single use. '
                    . 'You can request a new one <a href="%s">here</a>.'
                ), $this->generateUrl('frontend_user_recover'))
            );

            return new RedirectResponse(
                $this->generateUrl('frontend_authentication_login')
            );
        }

        return $this->render('password/change.tpl', [ 'token' => $token ]);
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
                ), $this->generateUrl('frontend_user_recover'))
            );

            return new RedirectResponse(
                $this->generateUrl('frontend_authentication_login')
            );
        }

        if ($password !== $passwordVerify) {
            $session->getFlashBag()->add(
                'error',
                _('Password and confirmation must be equal.')
            );

            return new RedirectResponse(
                $this->generateUrl('frontend_password_change', [ 'token' => $token ])
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
            $this->generateUrl('frontend_authentication_login')
        );
    }
}
