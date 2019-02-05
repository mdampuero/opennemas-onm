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
use Common\Core\EventListener\HttpCacheHeadersListener;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for HttpCacheHeadersListener class.
 */
class HttpCacheHeadersListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'fubar' ]);
        $this->response = new Response('', 200);

        $this->event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->setMethods([ 'getResponse' ])
            ->getMock();

        $this->headers = $this->getMockBuilder('HeaderBag')
            ->setMethods([ 'get', 'set' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequestLocale' ])
            ->getMock();

        $this->template = $this->getMockBuilder('Common\Core\Component\Template\Template')
            ->disableOriginalConstructor()
            ->setMethods([ 'getValue', 'hasValue' ])
            ->getMock();

        $this->event->expects($this->any())->method('getResponse')
            ->willReturn($this->response);

        $this->locale->expects($this->any())->method('getRequestLocale')
            ->willReturn('en_US');

        $this->response->headers = $this->headers;

        $this->listener = new HttpCacheHeadersListener($this->instance, $this->locale, $this->template);
    }

    /**
     * Tests onKernelResponse when x-cacheable value is false.
     */
    public function testOnKernelResponseWhenNoCacheable()
    {
        $this->template->expects($this->at(0))->method('hasValue')
            ->with('x-cacheable')->willReturn(true);
        $this->template->expects($this->at(1))->method('getValue')
            ->with('x-cacheable')->willReturn(false);

        $this->listener->onKernelResponse($this->event);
    }

    /**
     * Tests onKernelResponse when no x-tags found in response nor template.
     */
    public function testOnKernelResponseWhenNoTags()
    {
        $this->headers->expects($this->once())->method('get')
            ->with('x-tags')->willReturn(null);

        $this->template->expects($this->at(0))->method('hasValue')
            ->with('x-cacheable')->willReturn(false);
        $this->template->expects($this->at(1))->method('hasValue')
            ->with('x-tags')->willReturn(false);

        $this->listener->onKernelResponse($this->event);
    }

    /**
     * Tests onKernelResponse when x-tags found.
     */
    public function testOnKernelResponseWhenTags()
    {
        $listener = $this->getMockBuilder('Common\Core\EventListener\HttpCacheHeadersListener')
            ->setConstructorArgs([ $this->instance, $this->locale, $this->template ])
            ->setMethods([ 'getExpire', 'getTags' ])
            ->getMock();

        $this->template->expects($this->at(0))->method('hasValue')
            ->with('x-cacheable')->willReturn(false);
        $this->template->expects($this->at(1))->method('hasValue')
            ->with('x-tags')->willReturn(true);

        $listener->expects($this->once())->method('getTags')
            ->with($this->response)->willReturn([ 'qux', 'baz' ]);
        $listener->expects($this->once())->method('getExpire')
            ->with($this->response)->willReturn('flob');

        $this->headers->expects($this->at(0))->method('set')
            ->with('x-instance', 'fubar');
        $this->headers->expects($this->at(1))->method('set')
            ->with('x-tags', 'qux,baz');
        $this->headers->expects($this->at(2))->method('set')
            ->with('x-cache-for', 'flob');

        $listener->onKernelResponse($this->event);
    }

    /**
     * Tests getExpire when no x-cache-for value found in response nor template.
     */
    public function testGetExpireWhenNoValue()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpire');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-cache-for')->willReturn(null);
        $this->template->expects($this->any())->method('hasValue')
            ->with('x-cache-for')->willReturn(false);

        $this->assertEmpty($method->invokeArgs($this->listener, [ $this->response ]));
    }

    /**
     * Tests getExpire when Varnish value already found.
     */
    public function testGetExpireWhenValidExpireFound()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpire');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-cache-for')->willReturn(null);
        $this->template->expects($this->any())->method('hasValue')
            ->with('x-cache-for')->willReturn(true);
        $this->template->expects($this->once())->method('getValue')
            ->with('x-cache-for')->willReturn('3600s');

        $this->assertEquals(
            '3600s',
            $method->invokeArgs($this->listener, [ $this->response ])
        );
    }

    /**
     * Tests getExpire when value found in response and template.
     */
    public function testGetExpireWhenValueInTemplate()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpire');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-cache-for')->willReturn('+1 day');
        $this->template->expects($this->any())->method('hasValue')
            ->with('x-cache-for')->willReturn(true);
        $this->template->expects($this->once())->method('getValue')
            ->with('x-cache-for')->willReturn('+1 hour');

        $this->assertEquals(
            '3600s',
            $method->invokeArgs($this->listener, [ $this->response ])
        );
    }

    /**
     * Tests getExpire when value found only in response.
     */
    public function testGetExpireWhenValueInResponse()
    {
        $method = new \ReflectionMethod($this->listener, 'getExpire');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-cache-for')->willReturn('+1 day');
        $this->template->expects($this->any())->method('hasValue')
            ->with('x-cache-for')->willReturn(false);

        $this->assertEquals(
            '86400s',
            $method->invokeArgs($this->listener, [ $this->response ])
        );
    }

    /**
     * Tests getTags when no tags found.
     */
    public function testGetTagsWhenNoValues()
    {
        $method = new \ReflectionMethod($this->listener, 'getTags');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-tags')->willReturn(null);

        $this->template->expects($this->any())->method('hasValue')
            ->with('x-tags')->willReturn(false);

        $this->assertEmpty($method->invokeArgs($this->listener, [ $this->response ]));
    }

    /**
     * Tests getTags when tags found only in response.
     */
    public function testGetTagsWhenValuesInResponse()
    {
        $method = new \ReflectionMethod($this->listener, 'getTags');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-tags')->willReturn('glork,qux');

        $this->template->expects($this->any())->method('hasValue')
            ->with('x-tags')->willReturn(false);

        $this->assertEquals(
            [ 'instance-fubar', 'locale-en_US' , 'glork' , 'qux' ],
            $method->invokeArgs($this->listener, [ $this->response ])
        );
    }

    /**
     * Tests getTags when tags found only in response.
     */
    public function testGetTagsWhenValuesInTemplate()
    {
        $method = new \ReflectionMethod($this->listener, 'getTags');
        $method->setAccessible(true);

        $this->headers->expects($this->any())->method('get')
            ->with('x-tags')->willReturn(null);

        $this->template->expects($this->any())->method('hasValue')
            ->with('x-tags')->willReturn(true);
        $this->template->expects($this->any())->method('getValue')
            ->with('x-tags')->willReturn('gorp,wibble');

        $this->assertEquals(
            [ 'instance-fubar', 'locale-en_US' , 'gorp' , 'wibble' ],
            $method->invokeArgs($this->listener, [ $this->response ])
        );
    }
}
