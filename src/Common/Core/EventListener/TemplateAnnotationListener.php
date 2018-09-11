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

use Common\Core\Annotation\Template;
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
     * Initializes the TemplateAnnotationListener.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
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
        $reader     = $this->container->get('annotation_reader');

        foreach ($reader->getMethodAnnotations($method) as $annotation) {
            if (!($annotation instanceof Template)) {
                continue;
            }

            $controller[0]->view = $this->container->get($annotation->getName());

            if (!empty($annotation->getFile())) {
                $controller[0]->view->setFile($annotation->getFile());
            }

            return;
        }
    }
}
