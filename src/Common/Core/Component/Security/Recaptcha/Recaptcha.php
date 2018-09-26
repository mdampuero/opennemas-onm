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
     * Array that contains the site_key and secret_keys.
     *
     * @var string
     */
    protected $recaptchaKeys;

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

        // By default we will use the platform recaptcha keys configured via
        // paramters.yml
        $this->recaptchaKeys = [
            'siteKey'   => $container->getParameter('api.recaptcha.site_key'),
            'secretKey' => $container->getParameter('api.recaptcha.secret_key'),
        ];
    }

    /**
     * Configures the recaptcha service with keys for backend.
     */
    public function configureFromParameters()
    {
        $this->recaptcha = new BaseRecaptcha($this->recaptchaKeys['secretKey']);

        return $this;
    }

    /**
     * Configures the recaptcha service with keys for frontend.
     */
    public function configureFromSettings()
    {
        $instanceKeys = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('recaptcha');

        $keys = array_merge([
            'public_key' => '',
            'private_key' => '',
        ], is_array($instanceKeys) ? $instanceKeys : []);

        // Use the instance keys only if they are "valid" aka not empty
        if (!empty($keys['private_key'])
            && !empty($keys['public_key'])
        ) {
            $this->recaptchaKeys = [
                'siteKey'   => $keys['public_key'],
                'secretKey' => $keys['private_key'],
            ];
        }

        $this->recaptcha = new BaseRecaptcha($this->recaptchaKeys['secretKey']);

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

        $html = '<script src="https://www.google.com/recaptcha/api.js?hl=%s"></script>'
            . '<div class="g-recaptcha" data-sitekey="%s"></div>';

        return sprintf(
            $html,
            $this->container->get('core.locale')->getLocaleShort(),
            $this->recaptchaKeys['siteKey']
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
    public function isValid($responseCode, $ip)
    {
        if (empty($this->recaptcha)) {
            throw new \RuntimeException();
        }

        $response        = $this->recaptcha->verify($responseCode, $ip);
        $requestHostName = $this->container->get('request_stack')->getCurrentRequest()->getHost();

        if ($response->getHostName() !== $requestHostName) {
            return false;
        }

        return $response->isSuccess();
    }
}
