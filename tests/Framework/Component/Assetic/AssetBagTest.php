<?php

namespace Framework\Tests\Component\Assetic;

use Framework\Component\Assetic\AssetBag;
use Onm\Instance\Instance;

class AssetBagTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $instance = new Instance();
        $instance->settings = [ 'TEMPLATE_USER' => 'es.openhost.theme.foo' ];

        $this->config = [
            'folders' => [
                'bundles' => 'bundles',
                'common'  => 'assets',
                'themes'  => 'themes',
            ]
        ];

        $this->bag = new AssetBag($this->config, $instance);

        $reflection = new \ReflectionClass(get_class($this->bag));

        $this->methods['parseBundleName'] = $reflection->getMethod('parseBundleName');
        $this->methods['parseThemeName']  = $reflection->getMethod('parseThemeName');
        $this->methods['parsePath']       = $reflection->getMethod('parsePath');

        foreach ($this->methods as $method) {
            $method->setAccessible(true);
        }
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

    public function testAddStyle()
    {
        $style   = 'foo/bar.css';
        $filters = [ 'baz' ];

        $this->bag->addStyle($style, $filters);

        $this->assertTrue(array_key_exists($style, $this->bag->getStyles()));
        $this->assertTrue(in_array($filters, $this->bag->getStyles()));
    }

    public function testAddScript()
    {
        $script  = 'foo/bar.js';
        $filters = [ 'baz' ];

        $this->bag->addScript($script, $filters);

        $this->assertTrue(array_key_exists($script, $this->bag->getScripts()));
        $this->assertTrue(in_array($filters, $this->bag->getScripts()));
    }

    public function testParseBundleName()
    {
        $bundle = $this->methods['parseBundleName']
            ->invokeArgs($this->bag, [ 'FooBundle' ]);

        $this->assertEquals(realpath(__DIR__ . '/../../../../public/bundles/') . '/foo', $bundle);
    }

    public function testParsePath()
    {
        $expected = SITE_PATH . $this->config['folders']['common'] . DS . 'js' . DS . 'admin.js';
        $path     = $this->methods['parsePath']->invokeArgs($this->bag, [ '@Common/js/admin.js' ]);

        $this->assertEquals([ $expected ], $path);

        $expected = SITE_PATH . $this->config['folders']['themes'] . DS . 'foo' . DS . 'bar' . DS . 'baz.js';
        $path     = $this->methods['parsePath']->invokeArgs($this->bag, [ '@Theme/bar/baz.js' ]);

        $this->assertEquals([ $expected ], $path);

        $path = $this->methods['parsePath']->invokeArgs($this->bag, [ '@AdminTheme/js/controllers/*' ]);

        $this->assertNotEmpty($path);

        $path = $this->methods['parsePath']->invokeArgs($this->bag, [ '@FosJsRoutingBundle/js/router.js' ]);

        $this->assertNotEmpty($path);

        $path = $this->methods['parsePath']->invokeArgs($this->bag, [ '@AddminTheme/js/foo/*' ]);
        $this->assertEmpty($path);
    }

    public function testParseThemeName()
    {
        $theme = $this->methods['parseThemeName']
            ->invokeArgs($this->bag, [ 'FooTheme' ]);

        $this->assertEquals(realpath(__DIR__ . '/../../../../public/themes/') . '/foo', $theme);
    }
}
