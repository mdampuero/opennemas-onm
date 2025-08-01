<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\UrlHelper;

/**
 * Defines test cases for UrlHelper class.
 */
class UrlHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->kernel = $this->getMockBuilder('Kernel')
            ->setMethods([ 'getContainer' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'isSubdirectory', 'getSubdirectory' ])
            ->getMock();

        $this->kernel->expects($this->any())->method('getContainer')
            ->willReturn($this->container);

        $GLOBALS['kernel'] = $this->kernel;

        $this->helper = new UrlHelper($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;
        }

        return null;
    }

    /**
     * Tests isFrontendUri for frontend and backend URIs.
     */
    public function testIsFrontendUri()
    {
        $this->instance->expects($this->at(0))->method('isSubdirectory')
            ->willReturn(true);
        $this->instance->expects($this->at(1))->method('isSubdirectory')
            ->willReturn(false);
        $this->instance->expects($this->any())->method('getSubdirectory')
            ->willReturn('/aaaa');

        $this->assertFalse($this->helper->isFrontendUri('/admin'));
        $this->assertTrue($this->helper->isFrontendUri('/aaaa/asdas'));
        $this->assertTrue($this->helper->isFrontendUri('/aaasd'));
    }

    /**
     * Tests parseUrl with multiple URLs.
     */
    public function testParse()
    {
        $this->assertEquals([
            'path' => '/xyzzy/thud'
        ], $this->helper->parse('/xyzzy/thud'));

        $this->assertEquals([
            'fragment' => 'corge',
            'path'     => '/xyzzy/thud'
        ], $this->helper->parse('/xyzzy/thud#corge'));

        $this->assertEquals([
            'host'   => 'foobar',
            'path'   => '/xyzzy/thud',
            'scheme' => 'http'
        ], $this->helper->parse('http://foobar/xyzzy/thud'));

        $this->assertEquals([
            'fragment' => 'fred',
            'host'     => 'foobar',
            'pass'     => 'thud',
            'path'     => '/xyzzy/thud',
            'query'    => 'flob=bar',
            'scheme'   => 'http',
            'user'     => 'quux'
        ], $this->helper->parse('http://quux:thud@foobar/xyzzy/thud?flob=bar#fred'));
    }

    /**
     * Tests unparse with multiple parsed URLs.
     */
    public function testUnparse()
    {
        $this->assertEquals(
            '/xyzzy/thud',
            $this->helper->unparse([
                'path' => '/xyzzy/thud'
            ])
        );

        $this->assertEquals(
            '/xyzzy/thud#corge',
            $this->helper->unparse([
                'fragment' => 'corge',
                'path'     => '/xyzzy/thud'
            ])
        );

        $this->assertEquals(
            'http://foobar/xyzzy/thud',
            $this->helper->unparse([
                'host'   => 'foobar',
                'path'   => '/xyzzy/thud',
                'scheme' => 'http'
            ])
        );

        $this->assertEquals(
            'http://quux:thud@foobar/xyzzy/thud?flob=bar#fred',
            $this->helper->unparse([
                'fragment' => 'fred',
                'host'     => 'foobar',
                'pass'     => 'thud',
                'path'     => '/xyzzy/thud',
                'query'    => 'flob=bar',
                'scheme'   => 'http',
                'user'     => 'quux'
            ])
        );
    }
}
