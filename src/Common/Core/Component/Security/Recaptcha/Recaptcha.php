<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Security\Recaptcha;

use ReCaptcha\ReCaptcha as BaseRecaptcha;

/**
 * The Recaptcha class provides methods to check recaptcha codes basing on
 * different configurations.
 */
class Recaptcha
{
    /**
     * The ReCaptcha service.
     *
     * @var ReCaptcha
     */
    protected $recaptcha;

    /**
     * The ReCaptcha secret key.
     *
     * @var string
     */
    protected $secretKey;

    /**
     * The ReCaptcha site key.
     *
     * @var string
     */
    protected $siteKey;

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the Recaptcha service.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Configures the recaptcha service with keys for backend.
     */
    public function configureForBackend()
    {
        $this->siteKey =
            $this->container->getParameter('api.recaptcha.site_key');
        $this->secretKey =
            $this->container->getParameter('api.recaptcha.secret_key');

        $this->recaptcha = new BaseRecaptcha($this->secretKey);

        return $this;
    }

    /**
     * Configures the recaptcha service with keys for frontend.
     */
    public function configureForFrontend()
    {
        $keys = $this->container->get('orm.manager')
            ->getRepository('Settings', 'instance')
            ->get('recaptcha');

        if (empty($keys)
            || !is_array($keys)
            || !array_key_exists('private_key', $keys)
            || !array_key_exists('public_key', $keys)
        ) {
            throw new \RuntimeException();
        }

        $this->secretKey = $keys['private_key'];
        $this->siteKey   = $keys['public_key'];

        $this->recaptcha  = new BaseRecaptcha($this->secretKey);

        return $this;
    }

    /**
     * Returns the HTML to include a recaptcha in templates.
     *
     * @return string The HTML to include a recaptcha in templates.
     */
    public function getHtml()
    {
        if (empty($this->recaptcha)) {
            return _('ReCaptcha service is not configured');
        }

        $html = '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=%s"></script>'
            . '<div class="g-recaptcha" data-sitekey="%s"></div>';

        return sprintf(
            $html,
            $this->container->get('core.locale')->getLocaleShort(),
            $this->siteKey
        );
    }

    /**
     * Checks if the recaptcha code was solved successfully.
     *
     * @param string $response The recaptcha response.
     * @param string $ip       The client IP.
     *
     * @return boolean True if the recaptcha code was solved successfully. False
     *                 otherwise.
     */
    public function isValid($response, $ip)
    {
        if (empty($this->recaptcha)) {
            throw new \RuntimeException();
        }

        return $this->recaptcha->verify($response, $ip)->isSuccess();
    }
}
