<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListener;

use Doctrine\Common\Annotations\Reader;
use Framework\Annotation\Template;
use Onm\Security\Exception\ModuleNotActivatedException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * The TemplateAnnotationListener class initializes controller views basing on
 * annotations.
 */
class TemplateAnnotationListener
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The annotation reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Initializes the TemplateAnnotationListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->reader    = $container->get('annotation_reader');
    }

    /**
     * Initializes the controller view basing on the annotation.
     *
     * @param FilterResponseEvent $event The event object.
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $object     = new \ReflectionObject($controller[0]);
        $method     = $object->getMethod($controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof Template) {
                $controller[0]->view =
                    $this->container->get($annotation->getName());

                return;
            }
        }
    }
}
