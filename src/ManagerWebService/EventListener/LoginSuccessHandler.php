<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
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
    private $router;

    /**
     * @var Session
     */
    private $session;

    /**
     * Constructs a new handler.
     *
     * @param SecurityContext $context The security context.
     * @param Router          $router   The router service.
     * @param Session         $session The session.
     */
    public function __construct($context, $router, $session, $logger)
    {
        $this->context = $context;
        $this->router  = $router;
        $this->session = $session;
        $this->logger  = $logger;
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
        $user       = $token->getUser();
        $valid      = true;

        $attempts = 1;
        if ($this->session->get('failed_login_attempts')) {
            $attempts =  $this->session->get('failed_login_attempts') + 1;
        }

        // Validate recaptcha
        if ($request->get('challenge')) {
            // New captcha instance
            $captcha = getService('recaptcha')
                ->setRemoteIp($request->getClientIp());

            // Get reCaptcha validate response
            $valid = $captcha->check(
                $request->get('challenge'),
                $request->get('response')
            );
            $valid = $valid->isValid();
        }

        // Validate CSRF
        $isTokenValid = getService('form.csrf_provider')->isCsrfTokenValid(
            $this->session->get('intention'),
            $request->get('_token')
        );

        if (!$isTokenValid || !$valid) {
            // Log user out
            $this->context->setToken(null);
            $request->getSession()->invalidate();

            if (!$isTokenValid) {
                $this->session->getFlashBag()->add(
                    'error',
                    'Login token is not valid. Try to authenticate again.'
                );
                $this->logger->info("User ".$user->username." (ID:".$user->id.") tried to log in. Invalid token");
            }

            if (!$valid) {
                $this->session->getFlashBag()->add(
                    'error',
                    'The reCAPTCHA wasn\'t entered correctly. Try to authenticate'
                    . ' again.'
                );
                $this->logger->info("User ".$user->username." (ID:".$user->id.") tried to log in. Recaptcha failed.");
            }

            $this->session->set('failed_login_attempts', $attempts);

            $this->context->setToken(null);

            return new RedirectResponse(
                $this->router->generate('manager_ws_auth_login')
            );
        } else {
            $user = $token->getUser();

            $this->session->set('failed_login_attempts', 0);
            $this->session->set('user', $user);
            $this->session->set('user_language', $user->getMeta('user_language'));

            // Set last_login date
            $time = new \DateTime();
            $time->setTimezone(new \DateTimeZone('UTC'));
            $time = $time->format('Y-m-d H:i:s');

            if (!$user->isMaster()) {
                s::set('last_login', $time);
            }
            $this->logger->info("User ".$user->username." (ID:".$user->id.") has logged in.");

            return new JsonResponse(
                array(
                    'success' => true,
                    'user'    => $user
                )
            );
        }
    }
}
