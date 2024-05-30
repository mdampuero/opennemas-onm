<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Common\Core\EventListener\HttpClickjackingHeadersListener;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for HttpClickjackingHeadersListener class.
 */
class HttpClickjackingHeadersListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->response = new Response('', 200);

        $this->event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods([ 'getResponse', 'getRequest' ])
            ->getMock();

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'set', 'get', 'has' ])
            ->getMock();

        $this->event->expects($this->any())->method('getResponse')
            ->willReturn($this->response);

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri', 'getSession' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlHelper = $this->getMockBuilder('Common\Core\Component\Helper\UrlHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'isFrontendUri' ])
            ->getMock();

        $this->response->headers = $this->headers;

        $this->event->method('getRequest')->willReturn($this->request);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->listener = new HttpClickjackingHeadersListener($this->container);
    }

    /**
     * Returns a mock basing on parameter when calling get method in service
     * container.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.url':
                return $this->urlHelper;
            case 'core.instance':
                return $this->instance;
            default:
                return null;
        }
    }
    /**
     * Tests onKernelResponse when  content-type is not available yet, so it won't add the headers.
     */
    public function testOnKernelResponseWhenNotFrontendUri()
    {
        // Checks if it is an ESI fragment, looking the pattern "/widget/render/" in the uri
        $this->request->method('getRequestUri')->willReturn('/non/esi/page/...');

        // Checks if is a frontend page uri
        $this->urlHelper->method('isFrontendUri')->with('/non/esi/page/...')->willReturn(false);

        // Won't add the headers, since it is not an frontend page uri
        $this->headers->expects($this->never())->method('set');

        $this->listener->onKernelResponse($this->event);
    }

    /**
     * Tests onKernelResponse when response content-type is application/json, so it won't add the headers.
     */
    public function testOnKernelResponseWhenApiResponse()
    {
        // Checks the response content-type
        $this->headers->expects($this->any())->method('get')->with('Content-Type')->willReturn('application/json');

        // Checks if it is an ESI fragment, looking the pattern "/widget/render/" in the uri
        $this->request->method('getRequestUri')->willReturn('/non/esi/page/...');

        // Checks if is a frontend page uri
        $this->urlHelper->method('isFrontendUri')->with('/non/esi/page/...')->willReturn(true);

        // Won't add the headers, since the response content-type is application/json
        $this->headers->expects($this->never())->method('set');

        $this->listener->onKernelResponse($this->event);
    }

    /**
     * Tests onKernelResponse for standard web page requests (neither API response nor ESI fragment), adding headers.
     */
    public function testOnKernelResponseForStandardRequests()
    {
        // Checks the response content-type.
        $this->headers->expects($this->any())->method('get')->with('Content-Type')->willReturn('text/html');

        // Checks if it is an ESI fragment, looking for the pattern "/widget/render/" in the uri.
        $this->request->method('getRequestUri')->willReturn('/non/esi/page/...');

        // Checks if is a frontend page uri
        $this->urlHelper->method('isFrontendUri')->with('/non/esi/page/...')->willReturn(true);

        // Will add the headers, since the content is neither an API response nor ESI fragment.
        $this->headers->expects($this->atLeastOnce())->method('set')
            ->withConsecutive(
                [$this->equalTo('X-Frame-Options'), $this->equalTo('SAMEORIGIN')],
                [$this->equalTo('Content-Security-Policy'), $this->equalTo("frame-ancestors 'self'")]
            );

        $this->listener->onKernelResponse($this->event);
    }
}
