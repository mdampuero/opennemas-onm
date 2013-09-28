<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Framework\KernelEvents;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ControllerListener implements EventSubscriberInterface
{
    private $charset;

    public function __construct()
    {
    }

    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelResponse(FilterControllerEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $controller = $event->getController();

        global $sc;

        $template = new \TemplateAdmin(TEMPLATE_ADMIN);
        $template->container = $sc;

        $sc->set('view', $template);

        $controller[0]->setContainer($sc);
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::CONTROLLER => 'onKernelResponse',
        );
    }
}
