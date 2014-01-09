<?php
/**
 * Defines the Onm\Framework\Controller\Controller class
 *
 * This file is part of the Onm package.
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  Onm_Framework_Controller_Controller
 **/
namespace Onm\Framework\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as SymfonyController;

/**
 * Controller is a simple implementation of a Controller.
 *
 * It provides methods to common features needed in controllers.
 *
 * @package  Onm_Framework_Controller_Controller
 */
class Controller extends SymfonyController
{
    /**
     * Fetches unsetted variables from the container
     *
     * @param string $name the property name
     *
     * @return mixed the property value
     **/
    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The renderer view
     */
    public function renderView($view, array $parameters = array())
    {
        // If a cache_id parameter was passed use it to cache view results
        $cacheID = null;
        if (array_key_exists('cache_id', $parameters)) {
            $cacheID = $parameters['cache_id'];
            unset($parameters['cache_id']);
        }

        $this->view->assign($parameters);

        $this->template = $this->view->createTemplate($view, $cacheID);

        return $this->view->fetch($view, $cacheID);
    }

    /**
     * Returns information about a template
     *
     * @return array
     **/
    public function getExpireDate()
    {
        $data = null;

        // If the template is cached, fetch the dates from it
        if ($this->view->caching && $this->template->cached->timestamp) {
            $expires = $this->template->cached->timestamp + $this->template->cache_lifetime;

            $creationDate = new \DateTime();
            $creationDate->setTimeStamp($this->template->cached->timestamp);
            $creationDate->setTimeZone(new \DateTimeZone('Europe/Madrid'));

            $expireDate = new \DateTime();
            $expireDate->setTimeStamp($expires);
            $expireDate->setTimeZone(new \DateTimeZone('Europe/Madrid'));

            $data = array(
                'creation_date' => $creationDate,
                'expire_date' => $expireDate,
                'max_age'     => $expires - time(),
            );
        }

        return $data;

    }

    /**
     * Renders a view.
     *
     * @param string   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $contents = $this->renderView($view, $parameters);

        if (is_null($response)) {
            $response = new Response($contents);
        } else {
            $response->setContent($contents);
        }

        // if (array_key_exists('cache_id', $parameters)) {
        //     $expires = $this->getExpireDate();
        //     if (!is_null($expires)) {
        //         $response->setDate($expires['creation_date']);
        //         $response->setExpires($expires['expire_date']);
        //         $response->setSharedMaxAge($expires['max_age']);
        //     }
        // }

        return $response;
    }

    /**
     * Dispatches an event given its name and attaches a set of parameters to it
     *
     * @param string eventName The event to be dispatched
     * @param array eventParameters The set of parameters to attache to an event
     *
     * @return Symfony\Component\EventDispatcher\Event the event dispatched
     **/
    public function dispatchEvent($eventName, $eventParameters = array())
    {
        $event = new \Symfony\Component\EventDispatcher\GenericEvent();

        if (is_array($eventParameters) && count($eventParameters) > 0) {
            foreach ($eventParameters as $name => $value) {
                $event->setArgument($name, $value);
            }
        }

        return $this->container->get('event_dispatcher')->dispatch($eventName, $event);
    }

    /**
     * Checks if the user can access an specific aclname or redirects him to wellcome page
     *
     * @param string $aclName the name of the acl
     *
     * @return boolean true if the user has rights for the given ACL
     **/
    public function checkAclOrForward($aclName)
    {
        $this->container->get('acl_checker')->isGranted($aclName);
    }
}
