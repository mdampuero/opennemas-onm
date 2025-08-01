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

use Common\Core\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Defines test cases for ResponseListener class.
 */
class ResponseListenerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->csv = $this->getMockBuilder('Common\Core\Component\Helper\CsvHelper')
            ->setMethods([ 'getReport' ])
            ->getMock();

        $this->kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\HttpKernelInterface')
            ->getMock();

        $this->locale = $this->getMockBuilder('Common\Core\Component\Locale\Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContext', 'setContext' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->event = new GetResponseForControllerResultEvent(
            $this->kernel,
            $this->request,
            0,
            null
        );

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->listener = new ResponseListener($this->container);
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.helper.csv':
                return $this->csv;

            case 'core.locale':
                return $this->locale;

            default:
                return null;
        }
    }

    /**
     * Tests onKernelView when request has a valid format.
     */
    public function testOnKernelViewWhenFormat()
    {
        $this->request->expects($this->once())->method('get')
            ->with('format')->willReturn('.json');

        $this->listener->onKernelView($this->event);

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\JsonResponse',
            $this->event->getResponse()
        );
    }

    /**
     * Tests onKernelView when request has an invalid format.
     */
    public function testOnKernelViewWhenInvalidFormat()
    {
        $this->request->expects($this->once())->method('get')
            ->with('format')->willReturn('baz');

        $this->listener->onKernelView($this->event);

        $this->assertEquals('', $this->request->getContent());
    }

    /**
     * Tests onKernelView when request has no format.
     */
    public function testOnKernelViewWhenNoFormat()
    {
        $this->request->expects($this->once())->method('get')
            ->with('format')->willReturn(null);

        $this->listener->onKernelView($this->event);

        $this->assertEquals('', $this->request->getContent());
    }

    /**
     * Tests getCsvResponse when an error happens while generating the CSV
     * report.
     */
    public function testGetCsvResponseWhenError()
    {
        $this->csv->expects($this->once())->method('getReport')
            ->will($this->throwException(new \Exception()));

        $method = new \ReflectionMethod($this->listener, 'getCsvResponse');
        $method->setAccessible(true);

        $response = $method->invokeArgs($this->listener, [ [] ]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Tests getCsvResponse when no error while generating the CSV report.
     */
    public function testGetCsvResponseWhenNoError()
    {
        $csv     = "thud\nbar\nfoobar";
        $content = [
            'results'    => [ [ 'thud' => 'bar' ], [ 'thud' => 'foobar' ] ],
            'o-filename' => 'foo'
        ];

        $this->csv->expects($this->once())->method('getReport')
            ->willReturn($csv);

        $method = new \ReflectionMethod($this->listener, 'getCsvResponse');
        $method->setAccessible(true);

        $response = $method->invokeArgs($this->listener, [ $content ]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($csv, $response->getContent());
    }

    /**
     * Tests getJsonResponse with different content values.
     */
    public function testGetJsonResponse()
    {
        $method = new \ReflectionMethod($this->listener, 'getJsonResponse');
        $method->setAccessible(true);

        $content  = [ 'frog' => 'bar', 'o-filename' => 'foo' ];
        $response = $method->invokeArgs($this->listener, [ $content ]);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $response);
        $this->assertEquals('{"frog":"bar"}', $response->getContent());
    }
}
