<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\EventListener;

use Common\Core\Component\Core\GlobalVariables;
use Common\Core\Component\Template\Template;
use Common\Core\Component\Locale\Locale;
use Common\Model\Entity\Instance;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class HttpCacheHeadersListener
{
    /**
     * The current instance.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The Locale service.
     *
     * @var Locale
     */
    protected $locale;

    /**
     * The Template service
     *
     * @var Template
     */
    protected $template;

    /**
     * Initializes the VarnishListener.
     *
     * @param Instance        $instance The current instance.
     * @param Locale          $locale   The Locale service.
     * @param Template        $template The Template service.
     * @param GlobalVariables $globals  The global variables service.
     */
    public function __construct(?Instance $instance, Locale $locale, Template $template, GlobalVariables $globals)
    {
        $this->globals  = $globals;
        $this->instance = $instance;
        $this->locale   = $locale;
        $this->template = $template;
    }

    /**
     * Adds Varnish headers to the response basing on the current headers and
     * template variables.
     *
     * @param FilterResponseEvent $event The current event.
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (empty($this->template->getValue('x-cacheable'))
            && empty($response->headers->get('x-cacheable'))
        ) {
            return;
        }

        if (!$this->template->hasValue('x-tags')
            && empty($response->headers->get('x-tags'))
        ) {
            return;
        }

        $tags   = $this->getTags($response);
        $expire = $this->getExpire($response);

        if ($this->hasInstance()) {
            $response->headers->set('x-instance', $this->getInstance());
        }

        $response->headers->set('x-device', $this->globals->getDevice());
        $response->headers->set('x-tags', implode(',', $tags));
        $response->headers->set('x-cache-for', '86400s'); // 1 day

        if (!empty($expire)) {
            $response->headers->set('x-cache-for', $expire);
        }

        // Add surrogate control header to use edge side includes
        $response->headers->set('surrogate-control', 'ESI/1.0');
    }

    /**
     * Returns the expire time to response headers or null.
     *
     * @param Response $response The response object.
     *
     * @return mixed The expire time if valid x-cache-for found or null if
     *               x-cache-for not found.
     */
    protected function getExpire($response)
    {
        $expire = null;

        if (!empty($response->headers->get('x-cache-for'))) {
            $expire = $response->headers->get('x-cache-for');
        }

        if ($this->template->hasValue('x-cache-for')) {
            $expire = $this->template->getValue('x-cache-for');
        }

        if (preg_match('/[0-9]+[smhd]/', $expire)) {
            return $expire;
        }

        $expire = strtotime($expire);

        return !empty($expire) ? $expire - time() . 's' : null;
    }

    /**
     * Returns the instance value to use in x-instance and x-tags.
     *
     * @return string The instance value to use in x-instance and x-tags.
     */
    protected function getInstance()
    {
        return !empty($this->instance) ? $this->instance->internal_name : '';
    }

    /**
     * Returns the list of tags to add to the response headers basing on the
     * response and the template service.
     *
     * @param Response $response The response object.
     *
     * @return array The list of tags.
     */
    protected function getTags($response)
    {
        $tags = explode(',', $this->template->getValue('x-tags')
            . ',' . $response->headers->get('x-tags'));

        $tags = array_filter($tags, function ($tag) {
            return !empty($tag);
        });

        if (empty($tags)) {
            return [];
        }

        $defaults = [
            'locale-' . $this->locale->getRequestLocale(),
            'device-' . $this->globals->getDevice()
        ];

        if ($this->hasInstance()) {
            array_unshift($defaults, 'instance-' . $this->getInstance());
        }

        return array_unique(array_merge($defaults, $tags));
    }

    /**
     * Checks if the service has a valid instance.
     *
     * @return boolean True if the listener has an instance. False otherwise.
     */
    protected function hasInstance()
    {
        return !empty($this->getInstance());
    }
}
