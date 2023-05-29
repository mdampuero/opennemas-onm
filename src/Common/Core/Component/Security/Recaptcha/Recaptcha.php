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
     * The service version.
     *
     * @var Version
     */
    protected $version;

    /**
     * Array that handle the last response.
     *
     * @var Mixed
     */
    protected $lastResponse;

    /**
     * Initializes the Recaptcha service.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->version   = 3;
        // By default we will use the platform recaptcha keys configured via
        // paramters.yml
        $this->recaptchaKeys = [
            'siteKey'   => $container->getParameter(sprintf('api.recaptcha.v%d.site_key', $this->version)),
            'secretKey' => $container->getParameter(sprintf('api.recaptcha.v%d.secret_key', $this->version)),
        ];
    }

    /**
     * Checks if the recaptcha code was solved successfully.
     *
     * @param integer $version The recaptcha version.
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
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
        $this->recaptchaKeys = [
            'siteKey'   => $this->container->getParameter(sprintf('api.recaptcha.v%d.site_key', $this->version)),
            'secretKey'   => $this->container->getParameter(sprintf('api.recaptcha.v%d.secret_key', $this->version)),
        ];

        if ($this->version == 3) {
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
        }

        $this->recaptcha = new BaseRecaptcha($this->recaptchaKeys['secretKey']);
        if ($this->version == 3) {
            $this->setMinimumScore(0.5);
        }

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

        if ($this->version == 2) {
            $html = '<div class="recaptcha">'
            . '<script src="https://www.google.com/recaptcha/api.js?hl=%s"></script>'
            . '<div class="g-recaptcha" data-sitekey="%s"></div>'
            . '</div>';

            return sprintf(
                $html,
                $this->container->get('core.locale')->getLocaleShort(),
                $this->recaptchaKeys['siteKey']
            );
        }

        $html = '<script src="https://www.google.com/recaptcha/api.js?render=%s"></script>'
            . '<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">'
            . '<input type="hidden" name="action" value="validate_captcha">'
            . '<script>'
                . 'grecaptcha.ready(function() {'
                    . 'grecaptcha.execute("%s", {action:"validate_captcha"})'
                            . '.then(function(token) {'
                        . 'document.getElementById("g-recaptcha-response").value = token;'
                    . '});'
                . '});'
            . '</script>';

        return sprintf(
            $html,
            $this->recaptchaKeys['siteKey'],
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

        $this->lastResponse = $response;

        if ($response->getHostName() !== $requestHostName) {
            return false;
        }
        return $response->isSuccess();
    }

    /**
     * Get the last response object
     *
     * @return Mixed Last response object.
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Change the minimum score to pass google recaptcha
     *
     * @param string $score The recaptcha score.
     */
    public function setMinimumScore($score)
    {
        $this->recaptcha->setScoreThreshold($score);
    }
}
