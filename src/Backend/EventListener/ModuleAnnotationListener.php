<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backend\EventListener;

use Doctrine\Common\Annotations\Reader;
use Onm\Security\Exception\ModuleNotActivatedException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Initializes the controller before it handles the request.
 */
class ModuleAnnotationListener
{
    private $reader;

    public function __construct($router, Reader $reader)
    {
        // Get annotations reader
        $this->reader = $reader;
        // Get router
        $this->router = $router;
    }

    /**
     * This event will fire during any controller call
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            // Validate module access to user
            if (isset($annotation->module)) {
                if (!\Onm\Module\ModuleManager::isActivated($annotation->module)) {
                    $redirectUrl = $this->router->generate('admin_login');

                    throw new ModuleNotActivatedException();
                }
            }
        }
    }
}
