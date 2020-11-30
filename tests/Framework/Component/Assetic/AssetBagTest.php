<?php

namespace Tests\Framework\Component\Assetic;

use Framework\Component\Assetic\AssetBag;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Theme;

class AssetBagTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $instance = new Instance([
            'settings' => [ 'TEMPLATE_USER' => 'es.openhost.theme.foo' ]
        ]);

        $this->config = [
            'folders' => [
                'bundles' => 'bundles',
                'common'  => 'assets',
                'themes'  => 'core/themes',
            ]
        ];

        $this->bag = new AssetBag($this->config, $instance);
    }

    public function testAddLiteralStyle()
    {
        $this->bag->addLiteralStyle('foo');

        $this->assertContains('foo', $this->bag->getLiteralStyles());
    }

    public function testAddLiteralScript()
    {
        $this->bag->addLiteralScript('foo');

        $this->assertContains('foo', $this->bag->getLiteralScripts());
    }

    public function testAddScript()
    {
        $script  = 'foo/bar.js';
        $filters = [ 'baz' ];

        $this->bag->addScript($script, $filters);

        $this->assertContains($script, $this->bag->getScripts()['default']);
        $this->assertEquals($filters, $this->bag->getFilters()[$script]);
    }

    public function testAddStyle()
    {
        $style   = 'foo/bar.css';
        $filters = [ 'baz' ];

        $this->bag->addStyle($style, $filters);

        $this->assertContains($style, $this->bag->getStyles()['default']);
        $this->assertEquals($filters, $this->bag->getFilters()[$style]);
    }

    public function testParseBundleName()
    {
        $method = new \ReflectionMethod($this->bag, 'parseBundleName');
        $method->setAccessible(true);

        $this->assertEquals(
            SITE_PATH . 'bundles/foo',
            $method->invokeArgs($this->bag, [ 'FooBundle' ])
        );
    }

    public function testParsePath()
    {
        $method = new \ReflectionMethod($this->bag, 'parsePath');
        $method->setAccessible(true);

        $this->assertEquals([
            SITE_PATH . 'assets/js/admin.js'
        ], $method->invokeArgs($this->bag, [ '@Common/js/admin.js' ]));

        $this->assertEquals([
            SITE_PATH . 'core/themes/foo/bar/baz.js'
        ], $method->invokeArgs($this->bag, [ '@Theme/bar/baz.js' ]));

        $this->assertNotEmpty($method->invokeArgs($this->bag, [ '@AdminTheme/js/controllers/*' ]));
        $this->assertNotEmpty($method->invokeArgs($this->bag, [ '@FosJsRoutingBundle/js/router.js' ]));
        $this->assertEmpty($method->invokeArgs($this->bag, [ '@AddminTheme/js/foo/*' ]));
    }

    public function testParseThemeName()
    {
        $method = new \ReflectionMethod($this->bag, 'parseThemeName');
        $method->setAccessible(true);

        $this->assertEquals(
            SITE_PATH . 'core/themes/foo',
            $method->invokeArgs($this->bag, [ 'FooTheme' ])
        );
    }

    public function testReset()
    {
        $this->bag->addLiteralScript('foo');
        $this->bag->addLiteralStyle('foo');
        $this->bag->addScript('foo');
        $this->bag->addStyle('foo');

        $this->bag->reset();

        $this->assertEmpty($this->bag->getFilters());
        $this->assertEmpty($this->bag->getLiteralScripts());
        $this->assertEmpty($this->bag->getLiteralStyles());
        $this->assertEmpty($this->bag->getScripts());
        $this->assertEmpty($this->bag->getStyles());
    }
}
