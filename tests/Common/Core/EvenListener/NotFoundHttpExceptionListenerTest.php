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

use Common\Core\EventListener\NotFoundHttpExceptionListener;
use Common\ORM\Entity\Url;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Defines test cases for NotFoundHttpExceptionListener class.
 */
class NotFoundHttpExceptionListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->event = $this
            ->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent')
            ->disableOriginalConstructor()
            ->setMethods([ 'getException', 'getRequest', 'setResponse' ])
            ->getMock();

        $this->redirector = $this->getMockBuilder('Common\Core\Component\Routing\Redirector')
            ->disableOriginalConstructor()
            ->setMethods([ 'getResponse', 'getUrl'])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getRequestUri' ])
            ->getMock();

        $this->event->expects($this->any())->method('getRequest')
            ->willReturn($this->request);

        $this->listener = new NotFoundHttpExceptionListener($this->redirector);
    }

    /**
     * Tests onKernelException when the listener the redirector has no URL to
     * handle the request.
     */
    public function testOnKernelExceptionWhenNoUrlFound()
    {
        $this->event->expects($this->once())->method('getException')
            ->willReturn(new NotFoundHttpException());
        $this->event->expects($this->exactly(0))->method('setResponse');

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/bar/corge');

        $this->redirector->expects($this->once())->method('getUrl')
            ->with('/bar/corge')->willReturn(null);

        $this->listener->onKernelException($this->event);
    }

    /**
     * Tests onKernelException when the listener does not know how to handle the
     * thrown exception.
     */
    public function testOnKernelExceptionWhenUnknownException()
    {
        $this->event->expects($this->once())->method('getException')
            ->willReturn(new \Exception());
        $this->event->expects($this->exactly(0))->method('setResponse');

        $this->listener->onKernelException($this->event);
    }

    /**
     * Tests onKernelException when the listener the redirector has an URL to
     * handle the request.
     */
    public function testOnKernelExceptionWhenUrlFound()
    {
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
            ->setMethods([ 'methods' ])
            ->getMock();

        $url = new Url();

        $this->event->expects($this->once())->method('getException')
            ->willReturn(new NotFoundHttpException());
        $this->event->expects($this->once())->method('setResponse')
            ->with($response);

        $this->request->expects($this->once())->method('getRequestUri')
            ->willReturn('/bar/corge');

        $this->redirector->expects($this->once())->method('getUrl')
            ->with('/bar/corge')->willReturn($url);

        $this->redirector->expects($this->once())->method('getResponse')
            ->with($url)->willReturn($response);

        $this->listener->onKernelException($this->event);
    }
}
