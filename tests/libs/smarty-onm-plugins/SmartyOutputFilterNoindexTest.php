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

use Common\Model\Entity\Content;

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
            ->setMethods([ 'getContainer', 'getValue', 'hasValue' ])
            ->getMock();

        $this->subscription = $this->getMockBuilder('SubscriptionHelper')
            ->setMethods([ 'isIndexable' ])
            ->getMock();

        $this->smarty->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $this->container->expects($this->any())->method('get')
            ->willReturn($this->subscription);
    }

    /**
     * Tests smarty_outputfilter_noindex when no subscription token assigned to
     * template.
     */
    public function testNoindexWhenNoToken()
    {
        $this->smarty->expects($this->once())->method('hasValue')
            ->with('o_token')->willReturn(false);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_noindex($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_noindex when subscription token assigned to
     * template but content has to be indexed.
     */
    public function testNoindexWhenIndexed()
    {
        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('o_content')->willReturn(null);
        $this->smarty->expects($this->at(1))->method('hasValue')
            ->with('o_token')->willReturn(true);
        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('o_token')->willReturn('0000000000000');

        $this->subscription->expects($this->once())->method('isIndexable')
            ->with('0000000000000')->willReturn(true);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals($output, smarty_outputfilter_noindex($output, $this->smarty));
    }

    /**
     * Tests smarty_outputfilter_noindex when subscription token assigned to
     * template but content has to be not indexed and the meta is not present
     * in the HTML.
     */
    public function testNoindexWhenNoIndexedAndMetaNotPresent()
    {
        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('o_content')->willReturn(null);
        $this->smarty->expects($this->at(1))->method('hasValue')
            ->with('o_token')->willReturn(true);
        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('o_token')->willReturn('0000000000000');

        $this->subscription->expects($this->once())->method('isIndexable')
            ->with('0000000000000')->willReturn(false);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals(
            '<html><head><meta name="robots" content="noindex" /></head><body>Hello World!</body></html>',
            smarty_outputfilter_noindex($output, $this->smarty)
        );
    }

    /**
     * Tests smarty_outputfilter_noindex when subscription token assigned to
     * template but content has to be not indexed and the meta robots is already
     * present with "index,follow" value
     */
    public function testNoindexWhenNoIndexedAndMetaPresent()
    {
        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('o_content')->willReturn(null);
        $this->smarty->expects($this->at(1))->method('hasValue')
            ->with('o_token')->willReturn(true);
        $this->smarty->expects($this->at(3))->method('getValue')
            ->with('o_token')->willReturn('0000000000000');

        $this->subscription->expects($this->once())->method('isIndexable')
            ->with('0000000000000')->willReturn(false);

        $output = '<html>'
            . '<head>'
                . '<meta name="robots" content="index,follow" />'
            . '</head>'
            . '<body>Hello World!</body></html>';

        $this->assertEquals(
            '<html><head><meta name="robots" content="noindex" /></head><body>Hello World!</body></html>',
            smarty_outputfilter_noindex($output, $this->smarty)
        );
    }

    /**
     * Tests smarty_outputfilter_noindex when content has custom noindex and meta
     */
    public function testNoindexWhenCustomNoIndexAndMetaPresent()
    {
        $content = new Content(
            [
                'pk_content'   => 1,
                'noindex' => true
            ]
        );

        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('o_content')->willReturn($content);

        $output = '<html>'
            . '<head>'
                . '<meta name="robots" content="index,follow" />'
            . '</head>'
            . '<body>Hello World!</body></html>';

        $this->assertEquals(
            '<html><head><meta name="robots" content="noindex" /></head><body>Hello World!</body></html>',
            smarty_outputfilter_noindex($output, $this->smarty)
        );
    }

    /**
     * Tests smarty_outputfilter_noindex when content has custom noindex and no meta
     */
    public function testNoindexWhenCustomNoIndexAndMetaNotPresent()
    {
        $content = new Content(
            [
                'pk_content'   => 1,
                'noindex' => true
            ]
        );
        $this->smarty->expects($this->at(0))->method('getValue')
            ->with('o_content')->willReturn($content);

        $output = '<html><head></head><body>Hello World!</body></html>';

        $this->assertEquals(
            '<html><head><meta name="robots" content="noindex" /></head><body>Hello World!</body></html>',
            smarty_outputfilter_noindex($output, $this->smarty)
        );
    }
}
