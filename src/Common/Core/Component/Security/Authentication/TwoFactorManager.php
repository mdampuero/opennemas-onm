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

use Opennemas\Orm\Core\EntityManager;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Common\Core\Component\Template\Template;
use Common\Core\Component\Url\UrlDecorator;

/**
 * Manages the second factor authentication lifecycle.
 */
class TwoFactorManager
{
    const SESSION_KEY = 'two_factor_authentication';
    const COOKIE_NAME = '__onm_two_factor';
    const CODE_TTL = 600; // 10 minutes
    const COOKIE_TTL = 2592000; // 30 days

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Template
     */
    protected $template;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UrlDecorator
     */
    protected $urlDecorator;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var EntityManager
     */
    protected $orm;

    /**
     * @var string
     */
    protected $secret;

    /**
     * Initializes the manager.
     */
    public function __construct(
        SessionInterface $session,
        Swift_Mailer $mailer,
        Template $template,
        LoggerInterface $logger,
        UrlDecorator $urlDecorator,
        UrlGeneratorInterface $router,
        EntityManager $orm,
        $secret
    ) {
        $this->session      = $session;
        $this->mailer       = $mailer;
        $this->template     = $template;
        $this->logger       = $logger;
        $this->urlDecorator = $urlDecorator;
        $this->router       = $router;
        $this->orm          = $orm;
        $this->secret       = (string) $secret;
    }

    /**
     * Determines if the given user has to pass the two factor challenge.
     */
    public function shouldChallenge(Request $request, $user)
    {
        if (!$this->supportsUser($user)) {
            return false;
        }

        $cookie = $request->cookies->get(self::COOKIE_NAME);

        if (empty($cookie)) {
            return true;
        }

        $payload = $this->decodeCookie($cookie);

        if (empty($payload)) {
            return true;
        }

        $userId = $this->getUserId($user);

        if ($userId === null) {
            return false;
        }

        if ((int) $payload['id'] !== (int) $userId) {
            return true;
        }

        $passwordHash = $this->getPasswordHash($user);

        if ($passwordHash === '') {
            return false;
        }

        $signature = $this->buildSignature($userId, $passwordHash);

        return !hash_equals($signature, $payload['signature']);
    }

    /**
     * Initializes a new two factor challenge for the given user.
     */
    public function initiate(Request $request, UserInterface $user, $target)
    {
        if (!$this->supportsUser($user)) {
            return false;
        }
        
        $code = $this->generateCode();
        $secure = $request->isSecure();
        
        $userId = $this->getUserId($user);
        $passwordHash = $this->getPasswordHash($user);
        $email = $this->getUserEmail($user);
        
        if ($userId === null || empty($email)) {
            $this->logger->error('2FA - Could not be initiated due to missing user data.');
            return false;
        }
        
        $data = [
            'user_id'       => $userId,
            'password_hash' => $passwordHash,
            'email'         => $email,
            'code'          => $code,
            'expires_at'    => time() + self::CODE_TTL,
            'target'        => $target,
            'secure'        => $secure,
        ];

        $this->session->set(self::SESSION_KEY, $data);

        return $this->sendCode($data['email'], $code);
    }

    /**
     * Returns whether there is a pending challenge.
     */
    public function isPending()
    {
        return !empty($this->session->get(self::SESSION_KEY));
    }

    /**
     * Returns the current target url or null.
     */
    public function getTarget()
    {
        $data = $this->session->get(self::SESSION_KEY);

        if (empty($data)) {
            return null;
        }

        return $data['target'];
    }

    /**
     * Returns the masked email address for the current challenge.
     */
    public function getMaskedEmail()
    {
        $data = $this->session->get(self::SESSION_KEY);

        if (empty($data) || empty($data['email'])) {
            return '';
        }

        $email = $data['email'];

        if (strpos($email, '@') === false) {
            return $email;
        }

        list($local, $domain) = explode('@', $email, 2);
        $length = strlen($local);

        if ($length <= 2) {
            $maskedLocal = substr($local, 0, 1) . str_repeat('*', max($length - 1, 0));
        } else {
            $maskedLocal = substr($local, 0, 1)
                . str_repeat('*', $length - 2)
                . substr($local, -1);
        }

        return $maskedLocal . '@' . $domain;
    }

    /**
     * Verifies the provided code.
     */
    public function verify($code)
    {
        $data = $this->session->get(self::SESSION_KEY);

        if (empty($data)) {
            return false;
        }

        if ($data['expires_at'] < time()) {
            return false;
        }

        $normalized = $this->normalizeCode($code);

        if ($normalized === '') {
            return false;
        }

        return hash_equals($data['code'], $normalized);
    }

    /**
     * Generates and sends a new verification code using the stored challenge data.
     */
    public function resend()
    {
        $data = $this->session->get(self::SESSION_KEY);

        if (empty($data) || empty($data['email'])) {
            return false;
        }

        $data['code'] = $this->generateCode();
        $data['expires_at'] = time() + self::CODE_TTL;

        $this->session->set(self::SESSION_KEY, $data);

        return $this->sendCode($data['email'], $data['code']);
    }

    /**
     * Finalizes the challenge by issuing the trusted device cookie.
     */
    public function complete(Response $response)
    {
        $data = $this->session->get(self::SESSION_KEY);

        if (empty($data)) {
            return;
        }

        $cookieValue = $this->encodeCookie([
            'id'        => $data['user_id'],
            'signature' => $this->buildSignature($data['user_id'], $data['password_hash']),
        ]);

        $expire = time() + self::COOKIE_TTL;
        $sameSite = defined(Cookie::class . '::SAMESITE_LAX') ? Cookie::SAMESITE_LAX : null;
        $cookie = new Cookie(
            self::COOKIE_NAME,
            $cookieValue,
            $expire,
            '/',
            null,
            !empty($data['secure']),
            true,
            false,
            $sameSite
        );

        $response->headers->setCookie($cookie);
        $this->clear();
    }

    /**
     * Clears the pending challenge.
     */
    public function clear()
    {
        $this->session->remove(self::SESSION_KEY);
    }

    /**
     * Returns the absolute url for the verification page.
     */
    public function getVerificationUrl()
    {
        $url = $this->router->generate('backend_authentication_two_factor');

        return $this->urlDecorator->prefixUrl($url);
    }

    /**
     * Returns the raw session data.
     */
    public function getSessionData()
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    /**
     * Normalizes a verification code.
     */
    protected function normalizeCode($code)
    {
        $digits = preg_replace('/\D+/', '', (string) $code);
        $digits = substr($digits, 0, 6);

        if ($digits === '') {
            return '';
        }

        return str_pad($digits, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Sends the verification code via email.
     */
    protected function sendCode($email, $code)
    {
        if (empty($email)) {
            $this->logger->error('2FA -  cannot be sent without an email address.');

            return false;
        }

        try {
            $settings = $this->orm
                ->getDataSet('Settings', 'instance')
                ->get(['site_name', 'site_title']);
        } catch (\Exception $exception) {
            $settings = [ 'site_name' => 'OpenNemas', 'site_title' => 'OpenNemas' ];
        }

        $subject = sprintf(
            _('Your verification code for %s'),
            $settings['site_title']
        );

        $body = $this->template->render('login/emails/twofactor.tpl', [
            'code'     => $code,
            'siteName' => $settings['site_title'],
        ]);

        $message = Swift_Message::newInstance();
        $message
            ->setSubject($subject)
            ->setBody($body, 'text/plain')
            ->setTo($email)
            ->setFrom([
                'no-reply@postman.opennemas.com' => $settings['site_name']
            ]);

        try {
            $this->mailer->send($message);
            $this->logger->info(sprintf('2FA -  code sent to %s', $email));

            return true;
        } catch (\Exception $exception) {
            $this->logger->error('Unable to send the two factor authentication email: ' . $exception->getMessage());

            return false;
        }
    }

    /**
     * Generates a random verification code.
     */
    protected function generateCode()
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Encodes the cookie payload.
     */
    protected function encodeCookie(array $data)
    {
        return base64_encode(json_encode($data));
    }

    /**
     * Decodes the cookie payload.
     */
    protected function decodeCookie($value)
    {
        $decoded = base64_decode($value, true);

        if ($decoded === false) {
            return null;
        }

        $data = json_decode($decoded, true);

        if (!is_array($data) || empty($data['id']) || empty($data['signature'])) {
            return null;
        }

        return $data;
    }

    /**
     * Checks whether the user is eligible for the two factor challenge.
     */
    protected function supportsUser(UserInterface $user)
    {
        $roles = method_exists($user, 'getRoles') ? $user->getRoles() : [];

        return in_array('ROLE_BACKEND', $roles);
    }

    /**
     * Returns the persistent identifier for the user.
     */
    protected function getUserId(UserInterface $user)
    {
        return isset($user->id) ? $user->id : null;
    }

    /**
     * Returns the hashed password of the user.
     */
    protected function getPasswordHash(UserInterface $user)
    {
        return method_exists($user, 'getPassword') ? (string) $user->getPassword() : '';
    }

    /**
     * Returns the email of the user if available.
     */
    protected function getUserEmail(UserInterface $user)
    {
        return isset($user->email) ? $user->email : null;
    }

    /**
     * Builds the cookie signature for the given values.
     */
    protected function buildSignature($userId, $password)
    {
        return hash_hmac('sha256', $userId . '|' . $password, $this->secret);
    }
}
