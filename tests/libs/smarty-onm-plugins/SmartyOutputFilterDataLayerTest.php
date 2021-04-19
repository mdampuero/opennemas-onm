<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Libs\Smarty;

/**
 * Defines test cases for SmartyOutputFilterDataLayerTest class.
 */
class SmartyOutputFilterDataLayerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.data_layer.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', '__get' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->dl = $this->getMockBuilder('Common\Core\Component\DataLayer\DataLayer')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataLayerCode' ])
            ->getMock();

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('getContainer')
            ->willReturn($this->container);

        $this->smartySource = $this->getMockBuilder('Smarty_Template_Source')
            ->disableOriginalConstructor()
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('__get')
            ->with($this->equalTo('source'))
            ->will($this->returnValue($this->smartySource));

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->output = '<html><head></head><body></body></html>';
    }

    /**
     * Return a mock basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'request_stack':
                return $this->requestStack;

            case 'core.service.data_layer':
                return $this->dl;

            case 'request_stack':
                return $this->stack;

            case 'orm.manager':
                return $this->em;
        }

        return null;
    }

    /**
     * Test smarty_outputfilter_data_layer when no request
     */
    public function testDataLayerWhenNoRequest()
    {
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        $this->assertEquals(
            $this->output,
            smarty_outputfilter_data_layer($this->output, $this->smarty)
        );
    }

    /**
     * Test smarty_outputfilter_data_layer when no dataLayer
     */
    public function testDataLayerWhenNoDataLayer()
    {
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getUri')
            ->willReturn('https://www.example.org');

        $this->ds->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn(null);

        $this->assertEquals(
            $this->output,
            smarty_outputfilter_data_layer($this->output, $this->smarty)
        );
    }

    /**
     * Test smarty_outputfilter_data_layer
     */
    public function testDataLayer()
    {
        $this->requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request->expects($this->once())->method('getUri')
            ->willReturn('https://www.example.org');

        $this->ds->expects($this->once())->method('get')
            ->with('data_layer')
            ->willReturn(['foo', 'bar']);

        $this->dl->expects($this->once())->method('getDataLayerCode')
            ->willReturn('<script>Data Layer Code</script>');

        $output = '<html><head><script>Data Layer Code</script></head><body></body></html>';
        $this->assertEquals(
            $output,
            smarty_outputfilter_data_layer($this->output, $this->smarty)
        );
    }
}
