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

use Common\Core\Annotation\Security as SecurityAnnotation;
use Common\Core\Security\Security as SecurityService;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * The SecurityAnnotationListener class defines an event listener to check
 * roles, permissions and extensions when a controller action is called.
 */
class SecurityAnnotationListener
{
    /**
     * The annotation reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * The Security service.
     *
     * @var Security
     */
    protected $security;

    /**
     * Initializes the SecurityAnnotationListener.
     *
     * @param Reader          $reader The annotation reader.
     * @param SecurityService $security The security service.
     */
    public function __construct(Reader $reader, SecurityService $security)
    {
        $this->reader   = $reader;
        $this->security = $security;
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
            if ($annotation instanceof SecurityAnnotation) {
                $expression = $this->parseAnnotation($annotation);

                if (!eval($expression)) {
                    throw new AccessDeniedException();
                }
            }
        }
    }

    /**
     * Returns a PHP expresion basing on the annotation value.
     *
     * @param SecurityAnnotation $annotation The annotation.
     *
     * @return string The PHP expresion.
     */
    protected function parseAnnotation(SecurityAnnotation $annotation)
    {
        $value = $annotation->value;
        $value = preg_replace(
            [ '/and/', '/or/', '/\r|\n|\*/', '/\s+/' ],
            [ '&&', '||', '', ' ' ],
            $value
        );

        $value = preg_replace(
            '/(hasCategory|hasExtension|hasPermission|hasRole)/',
            '$this->security->$1',
            $value
        );

        return 'return ' . $value . ';';
    }
}
