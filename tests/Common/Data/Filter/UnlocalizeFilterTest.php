<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Data\Filter;

use Common\Data\Filter\UnlocalizeFilter;

/**
 * Defines test cases for UnlocalizeFilter class.
 */
class UnlocalizeFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getAvailableLocales', 'getLocale' ])
            ->disableOriginalConstructor()
            ->getMock();
        $this->locale->method('getLocale')->will($this->returnValue('gl'));

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->with('core.locale')->willReturn($this->locale);

        $this->filter = new UnlocalizeFilter($this->container, [
            'locales' => [ 'es', 'gl' ]
        ]);
    }

    /**
     * Tests filterValue with locales defined.
     */
    public function testFilterValue()
    {
        $method = new \ReflectionMethod($this->filter, 'filterValue');
        $method->setAccessible(true);

        $this->assertEquals([ 'gl' => 'fred' ], $method->invokeArgs($this->filter, [ 'fred' ]));
        $this->assertEquals([ 'gl' => null ], $method->invokeArgs($this->filter, [ null ]));
        $this->assertEquals([ 'gl' => 124 ], $method->invokeArgs($this->filter, [ 124 ]));
        $this->assertEquals(
            [ 'es' => 'mumble', 'gl' => 'glork' ],
            $method->invokeArgs($this->filter, [ [ 'es' => 'mumble', 'gl' => 'glork' ] ])
        );
        $this->assertEquals(
            [ 'gl' => 'mumble' ],
            $method->invokeArgs($this->filter, [ [ 'gl' => 'mumble' ] ])
        );
        $this->assertEquals(
            [ 'gl' => [ 'en' => 'mumble' ] ],
            $method->invokeArgs($this->filter, [ [ 'en' => 'mumble' ] ])
        );
    }

    /**
     * Tests filterValue when no locales defined.
     */
    public function testFilterValueWhenLocalesInService()
    {
        $this->locale->expects($this->any())->method('getAvailableLocales')
            ->willReturn([ 'es' => 'Spanish', 'gl' => 'Galician' ]);

        $method = new \ReflectionMethod($this->filter, 'filterValue');
        $method->setAccessible(true);

        $property = new \ReflectionProperty($this->filter, 'params');
        $property->setAccessible(true);
        $params = $property->getValue($this->filter);
        unset($params['locales']);
        $property->setValue($this->filter, $params);

        $this->assertEquals([ 'gl' => 'fred' ], $method->invokeArgs($this->filter, [ 'fred' ]));
        $this->assertEquals([ 'gl' => null ], $method->invokeArgs($this->filter, [ null ]));
        $this->assertEquals([ 'gl' => 124 ], $method->invokeArgs($this->filter, [ 124 ]));
        $this->assertEquals(
            [ 'es' => 'mumble', 'gl' => 'glork' ],
            $method->invokeArgs($this->filter, [ [ 'es' => 'mumble', 'gl' => 'glork' ] ])
        );
        $this->assertEquals(
            [ 'gl' => 'mumble' ],
            $method->invokeArgs($this->filter, [ [ 'gl' => 'mumble' ] ])
        );
    }

    /**
     * Tests filterValue when no locales defined.
     */
    public function testFilterValueWhenNoLocales()
    {
        $this->locale->expects($this->any())->method('getAvailableLocales')
            ->willReturn(null);

        $method = new \ReflectionMethod($this->filter, 'filterValue');
        $method->setAccessible(true);

        $property = new \ReflectionProperty($this->filter, 'params');
        $property->setAccessible(true);
        $params = $property->getValue($this->filter);
        unset($params['locales']);
        $property->setValue($this->filter, $params);

        $this->assertEquals(['gl' => 'fred'], $method->invokeArgs($this->filter, [ 'fred' ]));
        $this->assertEquals(['gl' => null], $method->invokeArgs($this->filter, [ null ]));
        $this->assertEquals(['gl' => 124], $method->invokeArgs($this->filter, [ 124 ]));
        $this->assertEquals(['gl' => [ 'baz' ]], $method->invokeArgs($this->filter, [ [ 'baz' ] ]));
        $this->assertEquals(
            [ 'es' => 'mumble', 'gl' => 'glork' ],
            $method->invokeArgs($this->filter, [ [ 'es' => 'mumble', 'gl' => 'glork' ] ])
        );
        $this->assertEquals(
            [ 'gl' => 'mumble' ],
            $method->invokeArgs($this->filter, [ [ 'gl' => 'mumble' ] ])
        );
    }
}
