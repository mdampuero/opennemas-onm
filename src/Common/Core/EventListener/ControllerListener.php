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

use Common\Core\Component\Locale\Locale;
use Common\Core\Component\Core\GlobalVariables;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * The ControllerListener class adds information from request to the
 * @core.template.globals service when a controller action is called.
 */
class ControllerListener
{
    /**
     * The global variables service.
     *
     * @var GlobalVariables
     */
    protected $globals;

    /**
     * The locale service
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Initializes the ControllerListener.
     *
     * @param GlobalVariables $globals The global variables service.
     */
    public function __construct(GlobalVariables $globals, Locale $locale)
    {
        $this->globals = $globals;
        $this->locale  = $locale;
    }

    /**
     * This event will fire during any controller call
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $namespace  = get_class($controller[0]);

        $this->globals->setAction($this->getAction($controller[1]));
        $this->globals->setEndpoint($this->getEndpoint($namespace));
        $this->globals->setExtension($this->getExtension($namespace));
        $this->locale->setContext($this->globals->getEndpoint());
    }

    /**
     * Returns the action name from the method name.
     *
     * @param string $action The action name.
     *
     * @return string The action name.
     */
    protected function getAction($action)
    {
        return strtolower(str_replace('Action', '', $action));
    }

    /**
     * Returns the endpoint from the controller namespace.
     *
     * @param string $namespace The controller namespace.
     *
     * @return string The endpoint.
     */
    protected function getEndpoint($namespace)
    {
        return strtolower(substr($namespace, 0, strpos($namespace, '\\')));
    }

    /**
     * Returns the extension name from the controller namespace.
     *
     * @param string $namespace The controller namespace.
     *
     * @return string The extension name.
     */
    protected function getExtension($namespace)
    {
        $extension = substr($namespace, strrpos($namespace, '\\') + 1);
        $extension = str_replace('Controller', '', $extension);

        return strtolower($extension);
    }
}
