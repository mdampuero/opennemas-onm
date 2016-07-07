<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Frontend\EventListeners;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

use Onm\Settings as s;
use \Privileges;

/**
 * Handler to load user data when an user logs in the system successfully.
 */
class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var SecurityContext
     */
    private $context;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Session
     */
    private $session;

    /**
     * Constructs a new handler.
     *
     * @param SecurityContext $context   The security context.
     * @param Router          $router    The router service.
     * @param Session         $session   The session.
     * @param Recaptcha       $recaptcha The Google Recaptcha.
     */
    public function __construct($context, $router, $session, $recaptcha)
    {
        $this->context   = $context;
        $this->router    = $router;
        $this->session   = $session;
        $this->recaptcha = $recaptcha;
    }

    /**
     * This is called when an interactive authentication attempt succeeds.
     *
     * @param Request        $request The request object.
     * @param TokenInterface $token   The security token.
     *
     * @return Response The response to return.
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token
    ) {
        $user  = $token->getUser();
        $valid = true;

        // Check reCaptcha if is set
        $response = $request->get('g-recaptcha-response');
        if (!is_null($response)) {
            $recaptcha = $this->recaptcha->getOnmRecaptcha();
            $resp = $recaptcha->verify(
                $request->get('g-recaptcha-response'),
                $request->getClientIp()
            );
            $valid = $resp->isSuccess();
        }

        // Set session array
        $_SESSION['userid']           = $user->id;
        $_SESSION['realname']         = $user->name;
        $_SESSION['username']         = $user->username;
        $_SESSION['email']            = $user->email;

        $this->session->set('user', $user);
        $this->session->set('user_language', $user->getMeta('user_language'));

        $isTokenValid = getService('form.csrf_provider')->isCsrfTokenValid(
            $this->session->get('intention'),
            $request->get('_token')
        );

        // Check token, user type and reCaptcha
        if (!$isTokenValid || $valid === false) {
            if (isset($_SESSION['failed_login_attempts'])) {
                $_SESSION['failed_login_attempts']++;
            } else {
                $_SESSION['failed_login_attempts'] = 1;
            }

            if (!$isTokenValid) {
                $this->session->getFlashBag()->add(
                    'error',
                    _('Login token is not valid. Try to authenticate again.')
                );
            }

            if ($valid === false) {
                $this->session->getFlashBag()->add(
                    'error',
                    _('The reCAPTCHA was not entered correctly. Try to authenticate'
                    . ' again.')
                );
            }

            $this->context->setToken(null);

            return new RedirectResponse($request->headers->get('referer'));
        } else {
            $im = getService('instance_manager');
            $um = getService('user_repository');
            $cache = getService('cache');

            $database = $im->current_instance->getDatabaseName();
            $namespace = $im->current_instance->internal_name;

            $um->selectDatabase($database);
            $cache->setNamespace($namespace);
            $GLOBALS['application']->conn->selectDatabase($database);

            unset($_SESSION['failed_login_attempts']);

            return new RedirectResponse($request->get('_referer'));
        }
    }
}
