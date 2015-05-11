<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\EventListener;

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
     * @param SecurityContext $context The security context.
     * @param Router          $router  The router service.
     * @param Session         $session The session.
     */
    public function __construct($context, $router, $session)
    {
        $this->context = $context;
        $this->router  = $router;
        $this->session = $session;
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

        if ($request->get('recaptcha_challenge_field')) {
            // New captcha instance
            $captcha = getService('recaptcha')
                ->setRemoteIp($request->getClientIp());

            // Get reCaptcha validate response
            $valid = $captcha->check(
                $request->get('recaptcha_challenge_field'),
                $request->get('recaptcha_response_field')
            );
            $valid = $valid->isValid();
        }

        // Set session array
        $_SESSION['userid']           = $user->id;
        $_SESSION['realname']         = $user->name;
        $_SESSION['username']         = $user->username;
        $_SESSION['email']            = $user->email;
        $_SESSION['deposit']          = $user->deposit;
        $_SESSION['type']             = $user->type;
        $_SESSION['accesscategories'] = $user->getAccessCategoryIds();
        $_SESSION['updated']          = time();
        $_SESSION['user_language']    = $user->getMeta('user_language');
        $_SESSION['valid']            = $valid;
        $_SESSION['meta']             = $user->getMeta();
        $_SESSION['isAdmin']          = $user->isAdmin();
        $_SESSION['isMaster']         = $user->isMaster();

        $isTokenValid = getService('form.csrf_provider')->isCsrfTokenValid(
            $this->session->get('intention'),
            $request->get('_token')
        );

        $im = getService('instance_manager');
        $um = getService('user_repository');
        $cache = getService('cache');

        $database = $im->current_instance->getDatabaseName();
        $namespace = $im->current_instance->internal_name;

        $um->selectDatabase($database);
        $cache->setNamespace($namespace);
        $GLOBALS['application']->conn->selectDatabase($database);

        if (!$isTokenValid || $valid === false) {
            if (isset($_SESSION['failed_login_attempts'])) {
                $_SESSION['failed_login_attempts']++;
            } else {
                $_SESSION['failed_login_attempts'] = 1;
            }

            if (!$isTokenValid) {
                $this->session->getFlashBag()->add(
                    'error',
                    'Login token is not valid. Try to autenticate again.'
                );
            }

            if ($valid === false) {
                $this->session->getFlashBag()->add(
                    'error',
                    'The reCAPTCHA wasn\'t entered correctly. Try to authenticate'
                    . ' again.'
                );
            }

            return new RedirectResponse($request->headers->get('referer'));
        } else {
            unset($_SESSION['failed_login_attempts']);

            // Set last_login date
            $time = new \DateTime();
            $time->setTimezone(new \DateTimeZone('UTC'));
            $time = $time->format('Y-m-d H:i:s');

            if (!$user->isMaster()) {
                s::set('last_login', $time);
            }

            return new RedirectResponse($request->get('_referer'));
        }
    }
}
