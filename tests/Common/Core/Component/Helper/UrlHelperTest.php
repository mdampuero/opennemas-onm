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
        $this->helper = new UrlHelper();
    }

    /**
     * Tests isFrontendUri for frontend and backend URIs.
     */
    public function testIsFrontendUri()
    {
        $this->assertTrue($this->helper->isFrontendUri('/fubar'));
        $this->assertFalse($this->helper->isFrontendUri('/admin'));
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
