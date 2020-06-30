<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

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
                _('Unable to find the password reset request. Please check the url we sent you in the email.')
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
                _('Unable to find the password reset request. Please check the url we sent you in the email.')
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
                $this->generateUrl('frontend_authentication_login')
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
