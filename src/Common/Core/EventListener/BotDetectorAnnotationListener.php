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

use Common\Core\Annotation\BotDetector as BotDetectorAnnotation;
use Common\Core\Component\Exception\BotDetectedException;
use Common\Core\Component\Http\BotDetector;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * The BotDetectorAnnotationListener class defines an event listener to check
 * roles, permissions and extensions when a controller action is called.
 */
class BotDetectorAnnotationListener
{
    /**
     * The BotDetector service.
     *
     * @var BotDetector
     */
    protected $detector;

    /**
     * The annotation reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * Initializes the BotDetectorAnnotationListener.
     *
     * @param Reader $reader The annotation reader.
     */
    public function __construct(Reader $reader)
    {
        $this->reader   = $reader;
        $this->detector = new BotDetector();
    }

    /**
     * This event will fire during any controller call
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $object     = new \ReflectionObject($controller[0]);
        $method     = $object->getMethod($controller[1]);

        $request = $event->getRequest();

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof BotDetectorAnnotation
                && $this->detector->isBot($request, $annotation->getBot())
            ) {
                throw new BotDetectedException($annotation->getRoute());
            }
        }
    }
}
