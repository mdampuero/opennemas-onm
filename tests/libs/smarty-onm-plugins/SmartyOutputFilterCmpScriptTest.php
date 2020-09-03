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
 * Defines test cases for SmartyCmpScriptTest class.
 */
class SmartyCmpScriptTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.cmp_script.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', 'getTemplateVars', '__set', '__get' ])
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

        $this->requestStack = $this->getMockBuilder('RequestStack')
            ->setMethods([ 'getCurrentRequest' ])
            ->getMock();

        $this->request = $this->getMockBuilder('Request')
            ->setMethods([ 'getUri' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getLocaleShort' ])
            ->getMock();

        $this->templateAdmin = $this->getMockBuilder('TemplateAdmin')
            ->setMethods([ 'fetch' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->output = '<html><head>Hello World</head><body></body></html>';

        $this->smartySource = $this->getMockBuilder('Smarty_Template_Source')
            ->disableOriginalConstructor()
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('__get')
            ->with($this->equalTo('source'))
            ->will($this->returnValue($this->smartySource));

        $this->smarty->source->resource = 'foo.tpl';
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
            case 'core.locale':
                return $this->locale;

            case 'core.template.admin':
                return $this->templateAdmin;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Test CMP when no request
     */
    public function testCmpWhenNoRequest()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn(null);

        $this->assertEquals($this->output, smarty_outputfilter_cmp_script(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test CMP not activated
     */
    public function testCmpNotActivated()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->any())
            ->method('get')
            ->with('cookies')
            ->willReturn('none');

        $this->assertEquals($this->output, smarty_outputfilter_cmp_script(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test CMP activated
     */
    public function testCmpActivated()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->at(0))
            ->method('get')
            ->with('cookies')
            ->willReturn('cmp');

        $this->ds->expects($this->at(1))
            ->method('get')
            ->with('cmp_type')
            ->willReturn('default');

        $returnvalue = "foo-bar-baz";

        $this->templateAdmin->expects($this->any())->method('fetch')
            ->with('common/helpers/cmp_default.tpl', [ 'id' => null ])
            ->willReturn($returnvalue);

        $output = "<html><head>Hello World\n" . $returnvalue . "</head><body></body></html>";

        $this->assertEquals($output, smarty_outputfilter_cmp_script(
            $this->output,
            $this->smarty
        ));
    }
}
