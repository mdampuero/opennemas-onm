<?php
namespace Onm\Framework;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Framework extends HttpKernel\HttpKernel
{
    public function __construct($routes)
    {
        $this->context = new Routing\RequestContext();
        $this->matcher = new Routing\Matcher\UrlMatcher($routes, $this->context);
        $this->resolver = new \Onm\Framework\ControllerResolver();
        $this->generator = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $this->context);

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($this->matcher));
        $this->dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));
        $this->dispatcher->addSubscriber(new \Onm\Framework\KernelEvents\ControllerListener());
        $this->dispatcher->addSubscriber(new \Onm\Framework\KernelEvents\AdminAuthenticationListener());

        parent::__construct($this->dispatcher, $this->resolver);
    }
}
