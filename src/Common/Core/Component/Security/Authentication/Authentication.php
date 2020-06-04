<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security\Authentication;

use Common\Core\Component\Exception\Security\InvalidRecaptchaException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfToken;

class Authentication
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The number of failed login attempts.
     *
     * @var integer
     */
    protected $failure = 0;

    /**
     * Initializes the Authentication service.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->recaptcha = $container->get('core.recaptcha');
        $this->session   = $container->get('session');
    }

    /**
     * Adds an authentication related error to the Authentication service.
     *
     * @param Exception $error The error Exception.
     */
    public function addError($error)
    {
        $this->session->set(Security::AUTHENTICATION_ERROR, $error);
    }

    /**
     * Authenticates an user programatically.
     *
     * @param User $user The user to authenticate.
     */
    public function authenticate($user)
    {
        $token = new UsernamePasswordToken($user, null, 'frontend', $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
        $this->session->set('user', $user);
        $this->session->set('_security_opennemas', serialize($token));
    }

    /**
     * Checks if the CSRF token is valid.
     *
     * @param string $id    The CSRF token id.
     * @param string $value The CSRF token value.
     *
     * @return boolean True if the token is valid. False otherwise.
     */
    public function checkCsrfToken($token)
    {
        $intention = $this->session->get('intention');
        $token     = new CsrfToken($intention, $token);

        $valid = $this->container->get('security.csrf.token_manager')
            ->isTokenValid($token);

        if (!$valid) {
            $this->session->set(
                Security::AUTHENTICATION_ERROR,
                new InvalidCsrfTokenException()
            );
        }

        return $valid;
    }

    /**
     * Checks if the recaptcha response is valid.
     *
     * @param string $response The recaptcha response.
     * @param string $ip       The client IP.
     * @param string $referer  The referer URL to find out if recaptcha has to
     *                         be checked with keys from settings or parameters.
     *
     * @return boolean True if the response is valid. False otherwise.
     */
    public function checkRecaptcha($response, $ip, $referer = '')
    {
        $this->recaptcha->configureFromParameters();

        if (!empty($referer) && $this->isRecaptchaForFrontend($referer)) {
            $this->recaptcha->configureFromSettings();
        }

        $valid = $this->recaptcha->isValid($response, $ip);

        if (!$valid) {
            $this->session->set(
                Security::AUTHENTICATION_ERROR,
                new InvalidRecaptchaException()
            );
        }

        return $valid;
    }

    /*
     * Executes actions when authentication results is a failure.
     */
    public function failure()
    {
        $this->session->set(
            'failed_login_attempts',
            $this->session->get('failed_login_attempts') + 1
        );
    }

    /**
     * Generates a new CSRF token.
     *
     * @return string A new CSRF token.
     */
    public function getCsrfToken()
    {
        return $this->container->get('security.csrf.token_manager')
            ->getToken($this->getIntention());
    }

    /**
     * Returns the error Exception in request or session.
     *
     * @return Exception The error Exception in request or session.
     */
    public function getError()
    {
        // Error in request
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            return $request->attributes
                ->get(Security::AUTHENTICATION_ERROR);
        }

        // Error in session
        $error = $this->session->get(Security::AUTHENTICATION_ERROR);

        return $error;
    }

    /**
     * Returns the error message basing on the current error.
     *
     * @return string The error message.
     */
    public function getErrorMessage()
    {
        if (!$this->hasError()) {
            return '';
        }

        $error = $this->getError();

        if ($error instanceof BadCredentialsException) {
            return _('Username or password incorrect.');
        }

        if ($error instanceof InvalidCsrfTokenException) {
            return _('Login token is not valid. Try to authenticate again.');
        }

        if ($error instanceof InvalidRecaptchaException) {
            return _('The reCAPTCHA was not entered correctly. Try to authenticate again.');
        }

        if ($error instanceof DisabledException) {
            return sprintf(_(
                '<strong>This account has not been verified.</strong>' .
                '<ul class="mb-0">' .
                '<li>To verify this account click on the link sent to your email.</li>' .
                '<li>If you have not received any message, check your spam box.</li>' .
                '<li>If you want a new link, click <a href="%s">here</a>.</li>' .
                '</ul>'
            ), $this->container->get('router')->generate('frontend_user_verify'));
        }

        return $error->getMessage();
    }

    /**
     * Returns the error message basing on the current error.
     *
     * @return string The error message.
     */
    public function getInternalErrorMessage()
    {
        if (!$this->hasError()) {
            return '';
        }

        $error = $this->getError();

        if ($error instanceof BadCredentialsException) {
            return 'security.authentication.failure.credentials';
        }

        if ($error instanceof InvalidCsrfTokenException) {
            return 'security.authentication.failure.csrf';
        }

        if ($error instanceof InvalidRecaptchaException) {
            return 'security.authentication.failure.recaptcha';
        }

        return $error->getMessage();
    }

    /**
     * Returns the HTML for a recaptcha configured from parameters.
     *
     * @return string The HTML for a recaptcha configured from parameters.
     */
    public function getRecaptchaFromParameters()
    {
        return $this->recaptcha->configureFromParameters()->getHtml();
    }

    /**
     * Returns the HTML for a recaptcha configured from settings.
     *
     * @return string The HTML for a recaptcha configured from settings.
     */
    public function getRecaptchaFromSettings()
    {
        return $this->recaptcha->configureFromSettings()->getHtml();
    }

    /**
     * Checks if there is an authentication error in request or session.
     *
     * @return boolean True if there is an authentication error in request or
     *                 session. False otherwise.
     */
    public function hasError()
    {
        return $this->container->get('request_stack')->getCurrentRequest()
            ->attributes->has(Security::AUTHENTICATION_ERROR)
                || !empty($this->container->get('session')
                    ->get(Security::AUTHENTICATION_ERROR));
    }

    /**
     * Checks if an user is authenticated.
     *
     * @return boolean True if there is an authenticated user. False otherwise.
     */
    public function isAuthenticated()
    {
        return !empty($this->container->get('core.user'));
    }

    /**
     * Checks if recaptcha is required basing on failed login attemps stored in
     * session.
     *
     * @return boolean True if recaptcha is required. False otherwise.
     */
    public function isRecaptchaRequired()
    {
        return $this->session->get('failed_login_attempts') >= 3;
    }

    /**
     * Executes actions when authentication results is a success.
     */
    public function success()
    {
        $this->session->set(Security::AUTHENTICATION_ERROR, null);
        $this->session->set('failed_login_attempts', 0);
    }

    /**
     * Returns the intention (id) to generate the token.
     *
     * @return string The intention to generate the token.
     */
    protected function getIntention()
    {
        $intention = time() . rand();

        $this->session->set('intention', $intention);

        return $intention;
    }

    /**
     * Checks if the current request is for frontend login.
     *
     * @param string $url The request URL.
     *
     * @return boolean True if the referer URL is for frontend. False otherwise.
     */
    protected function isRecaptchaForFrontend($url)
    {
        return !strpos($url, '/admin') && !strpos($url, '/manager');
    }
}
