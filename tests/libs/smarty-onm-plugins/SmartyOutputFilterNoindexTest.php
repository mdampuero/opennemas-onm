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
 * Defines test cases for SmartyUrl class.
 */
class SmartyOutputFilterNoindexTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        include_once './libs/smarty-onm-plugins/outputfilter.noindex.php';

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->smarty = $this->getMockBuilder('Smarty')
            ->setMethods([ 'getContainer', 'getTemplateVars', '__set', '__get' ])
            ->getMock();

        $this->subscription = $this->getMockBuilder('SubscriptionHelper')
            ->setMethods([ 'isIndexable' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->subscription);


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
     * Tests smarty_outputfilter_noindex when no subscription token assigned to
     * template.
     */
    public function testNoindexWhenNoToken()
    {
        $this->smarty->expects($this->once())->method('getTemplateVars')
            ->willReturn([]);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_noindex($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_noindex when subscription token assigned to
     * template but content has to be indexed.
     */
    public function testNoindexWhenIndexed()
    {
        $this->smarty->expects($this->any())->method('getTemplateVars')
            ->willReturn([ 'o_token' => '0000000000000' ]);

        $this->subscription->expects($this->once())->method('isIndexable')
            ->with('0000000000000')->willReturn(true);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_noindex($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_noindex when subscription token assigned to
     * template but content has to be not indexed.
     */
    public function testNoindexWhenNoIndexed()
    {
        $this->smarty->expects($this->any())->method('getTemplateVars')
            ->willReturn([ 'o_token' => '0000000000000' ]);

        $this->subscription->expects($this->once())->method('isIndexable')
            ->with('0000000000000')->willReturn(false);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals(
            '<html><head><meta name="robots" content="noindex" /></head><body>Hello World!</body></html>',
            smarty_outputfilter_noindex($output, $this->smarty)
        );
    }
}
