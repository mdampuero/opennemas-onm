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
     * @var [type]
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
     * @param Session         $session The session.
     */
    public function __construct($context, $router, $session)
    {
        $this->context = $context;
        $this->router  = $router;
        $this->session = $session;

        // Load reCaptcha lib
        require_once 'recaptchalib.php';
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
        $group      = array();
        $user       = $token->getUser();
        $userGroups = $user->id_user_group;
        $valid      = true;

        foreach ($userGroups as $group) {
            $groups[] = \UserGroup::getGroupName($group);
        }

        if ($request->get('challenge')) {
            // Get reCaptcha validate response
            $valid = recaptcha_check_answer(
                '6LfLDtMSAAAAAGTj40fUQCrjeA1XkoVR2gbG9iQs',
                $request->getClientIp(),
                $request->get('challenge'),
                $request->get('response')
            );

            $valid = $valid->is_valid;
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

        $isTokenValid = getService('form.csrf_provider')->isCsrfTokenValid(
            $this->session->get('intention'),
            $request->get('_token')
        );

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

            return new RedirectResponse(
                $this->router->generate('manager_ws_auth_login')
            );
        } else {
            unset($_SESSION['failed_login_attempts']);

            // Set last_login date
            $time = new \DateTime();
            $time->setTimezone(new \DateTimeZone('UTC'));
            $time = $time->format('Y-m-d H:i:s');

            s::set('last_login', $time);

            return new JsonResponse(
                array(
                    'success' => true,
                    'user'    => $token->getUser()
                )
            );
        }
    }
}
