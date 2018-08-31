<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class ErrorController extends Controller
{
    /**
     * Displays an error
     *
     * @param Request $request the request object
     *
     * @return void
     */
    public function defaultAction(Request $request)
    {
        $error = $request->attributes->get('exception');
        $name  = basename(str_replace('\\', '/', $error->getClass()));

        $this->view = $this->get('onm_templating')->getBackendTemplate();

        switch ($name) {
            case 'InstanceNotFoundException':
                return $this->getInstanceNotFoundResponse();

            case 'InstanceNotActivatedException':
                return $this->getInstanceNotActivatedResponse();

            case 'ResourceNotFoundException':
            case 'NotFoundHttpException':
                return $this->getNotFoundResponse();

            case 'AccessDeniedException':
                return $this->getAccessDeniedResponse();

            default:
                return $this->getErrorResponse();
        }
    }

    /**
     * Generates a response when the user is not authorized to access the
     * requested resource.
     *
     * @return Response The response object.
     */
    protected function getAccessDeniedResponse()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $content = _('You are not allowed to perform this action.');

        $this->get('application.log')->info('security.authorization.failure');

        if (!$request->isXmlHttpRequest()) {
            $content = $this->renderView('error/404.tpl', [
                'error'   => $request->attributes->get('exception'),
                'message' => $content,
            ]);
        }

        return new Response($content, 401);
    }

    /**
     * Generates a response when an unknown error happens.
     *
     * @return Response The response object.
     */
    protected function getErrorResponse()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $content = _('Oups! Seems that we had an unknown problem while trying to run your request.');
        $error   = $request->attributes->get('exception');

        $this->get('error.log')->error(
            $error->getMessage() . ' ' . json_encode($error->getTrace())
        );

        if (!$request->isXmlHttpRequest()) {
            $content = $this->renderView('error/404.tpl', [
                'environment' => $this->get('kernel')->getEnvironment(),
                'error'       => $error,
                'message'     => $content
            ]);
        }

        return new Response($content, 500);
    }

    /**
     * Generates a response when the requested resource belongs to a disabled
     * instance.
     *
     * @return Response The response object.
     */
    protected function getInstanceNotActivatedResponse()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $content = 'Instance not activated';

        $this->get('application.log')->info($content);

        if (!$request->isXmlHttpRequest()) {
            $content = $this->renderView('error/instance_not_activated.tpl', [
                'environment' => $this->get('kernel')->getEnvironment(),
                'error'       => $request->attributes->get('exception'),
                'host'        => $request->getHost()
            ]);
        }

        $instance = $this->get('core.instance')->internal_name;

        $response = new Response($content, 404);
        $response->headers->set('x-cache-for', '5s');
        $response->headers->set('x-instance', $instance);
        $response->headers->set('x-tags', 'instance-' . $instance . ',not-activated-error');

        return $response;
    }

    /**
     * Generates a response when the requested resource belongs to an unexsiting
     * instance.
     *
     * @return Response The response object.
     */
    protected function getInstanceNotFoundResponse()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $content = 'Instance not found';

        $this->get('application.log')->info($content);

        if (!$request->isXmlHttpRequest()) {
            $content = $this->renderView('error/instance_not_found.tpl', [
                'environment' => $this->get('kernel')->getEnvironment(),
                'error'       => $request->attributes->get('exception'),
                'host'        => $request->getHost()
            ]);
        }

        $response = new Response($content, 404);
        $response->headers->set('x-cache-for', '5s');

        return $response;
    }

    /**
     * Generates a response when the requested resource was not found.
     *
     * @return Response The response object.
     */
    protected function getNotFoundResponse()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $content = sprintf(
            'Oups! We can\'t find anything at "%s".',
            $request->getRequestUri()
        );

        $this->get('application.log')->info('Page not found');

        if (!$request->isXmlHttpRequest()) {
            $content = $this->renderView('error/404.tpl', [
                'environment' => $this->get('kernel')->getEnvironment(),
                'error'       => $request->attributes->get('exception'),
                'message'     => $content
            ]);
        }

        return new Response($content, 404);
    }
}
