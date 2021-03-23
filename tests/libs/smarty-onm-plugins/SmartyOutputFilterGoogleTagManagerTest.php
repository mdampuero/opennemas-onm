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
 * Defines test cases for SmartyOutputFilterGoogleTagManagerTest class.
 */
class SmartyOutputFilterGoogleTagManagerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.google_tag_manager.php';

        $this->smarty = $this->getMockBuilder('Smarty')
            ->disableOriginalConstructor()
            ->setMethods([ 'getContainer', '__set', '__get' ])
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

        $this->gtm = $this->getMockBuilder('TagManager')
            ->setMethods([
                'getGoogleTagManagerBodyCodeAMP',
                'getGoogleTagManagerHeadCode',
                'getGoogleTagManagerBodyCode'
            ])->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->ds);

        $this->smartySource = $this->getMockBuilder('Smarty_Template_Source')
            ->disableOriginalConstructor()
            ->getMock();

        $this->smarty->expects($this->any())
            ->method('__get')
            ->with($this->equalTo('source'))
            ->will($this->returnValue($this->smartySource));

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
            case 'core.google.tag_manager':
                return $this->gtm;

            case 'orm.manager':
                return $this->em;

            case 'request_stack':
                return $this->requestStack;
        }

        return null;
    }

    /**
     * Test GTM when no request
     */
    public function testGTMWhenNoRequest()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn(null);

        $this->assertEquals($this->output, smarty_outputfilter_google_tag_manager(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test GTM when no config
     */
    public function testGTMWhenNoConfig()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->any())
            ->method('get')
            ->with('google_tags_id')
            ->willReturn(null);

        $this->assertEquals($this->output, smarty_outputfilter_google_tag_manager(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test GTMAMP when no config
     */
    public function testGTMAMPWhenNoConfig()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('https://example.org/foo-bar-baz.amp.html');

        $this->ds->expects($this->any())
            ->method('get')
            ->with('google_tags_id_amp')
            ->willReturn(null);

        $this->assertEquals($this->output, smarty_outputfilter_google_tag_manager(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test GTM web
     */
    public function testGTMWeb()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->ds->expects($this->any())
            ->method('get')
            ->with('google_tags_id')
            ->willReturn('GTM-00000');

        $this->gtm->expects($this->at(0))->method('getGoogleTagManagerHeadCode')
            ->willReturn('fooHead');
        $this->gtm->expects($this->at(1))->method('getGoogleTagManagerBodyCode')
            ->willReturn('fooBody');

        $output = '<html><head>fooHead</head><body>' . "\n" . 'fooBody</body></html>';

        $this->assertEquals($output, smarty_outputfilter_google_tag_manager(
            $this->output,
            $this->smarty
        ));
    }

    /**
     * Test GTM AMP
     */
    public function testGTMAMP()
    {
        $this->requestStack->expects($this->any())
            ->method('getCurrentRequest')->willReturn($this->request);

        $this->request->expects($this->any())->method('getUri')
            ->willReturn('https://example.org/foo-bar-baz.amp.html');

        $this->ds->expects($this->any())
            ->method('get')
            ->with('google_tags_id_amp')
            ->willReturn('GTM-00000');

        $this->gtm->expects($this->at(0))->method('getGoogleTagManagerBodyCodeAMP')
            ->willReturn('fooBodyAMP');

        $output = '<html><head></head><body>' . "\n" . 'fooBodyAMP</body></html>';

        $this->assertEquals($output, smarty_outputfilter_google_tag_manager(
            $this->output,
            $this->smarty
        ));
    }
}
