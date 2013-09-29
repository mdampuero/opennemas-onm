<?php
namespace Onm\Framework;

use Onm\Framework\KernelEvents;
use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Framework extends HttpKernel\HttpKernel
{
    public function __construct($routes)
    {
        $this->context   = new Routing\RequestContext();
        $this->matcher   = new Routing\Matcher\UrlMatcher($routes, $this->context);
        $this->resolver  = new \Onm\Framework\ControllerResolver();
        $this->generator = new \Symfony\Component\Routing\Generator\UrlGenerator($routes, $this->context);

        $this->dispatcher = new EventDispatcher();

        // kernel.request
        $this->dispatcher->addSubscriber(new KernelEvents\CleanRequest());
        $this->dispatcher->addSubscriber(new KernelEvents\AdminAuthenticationListener());
        $this->dispatcher->addSubscriber(new KernelEvents\InstanceLoaderListener());
        $this->dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($this->matcher));

        // resolve controller
        $this->dispatcher->addSubscriber(new KernelEvents\ControllerListener());


        // kernel.response
        $this->dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

        parent::__construct($this->dispatcher, $this->resolver);
    }
}
