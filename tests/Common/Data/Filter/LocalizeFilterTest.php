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

use Common\ORM\Core\Entity;
use Common\Data\Filter\LocalizeFilter;

/**
 * Defines test cases for Localizer class.
 */
class LocalizeFilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->locale = $this->getMockBuilder('Locale')
            ->disableOriginalConstructor()
            ->setMethods([ 'getRequestLocale' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'hasParameter', 'get' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->locale->expects($this->any())->method('getRequestLocale')
            ->willReturn('gl');
        $this->container->expects($this->any())->method('get')
            ->with('core.locale')->willReturn($this->locale);

        $this->filter = new LocalizeFilter($this->container, [
            'keys'    => [ 'xyzzy' ],
            'locale'  => 'es',
            'default' => 'en'
        ]);
    }

    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $entities = [
            new Entity([
                'flob' => 'norf',
                'xyzzy' => [
                    'en' => 'grault',
                    'es' => 'frog',
                    'gl' => 'corge',
                ]
            ]),
            new Entity([
                'flob' => 'baz',
                'xyzzy' => [
                    'gl' => 'corge',
                ]
            ]),
            new Entity([
                'flob' => 'baz',
                'xyzzy' => [
                    'en' => 'quux',
                    'gl' => 'mumble',
                ]
            ])
        ];

        $this->assertEmpty($this->filter->filter([]));
        $this->assertEmpty($this->filter->filter(null));
        $this->assertEquals('wobble', $this->filter->filter('wobble'));

        $this->filter->filter($entities);
        $this->assertEquals('frog', $entities[0]->xyzzy);
        $this->assertEquals('corge', $entities[1]->xyzzy);
        $this->assertEquals('quux', $entities[2]->xyzzy);

        $property = new \ReflectionProperty($this->filter, 'params');
        $property->setAccessible(true);
        $params = $property->getValue($this->filter);
        unset($params['keys']);
        $property->setValue($this->filter, $params);

        $this->assertEquals('quux', $this->filter->filter([ 'es' => 'quux' ]));
    }

    /**
     * Tests filterItem with object and non-object values.
     */
    public function testFilterItem()
    {
        $item   = [ 'fubar', 'glorp' ];
        $entity = new Entity([
            'flob' => 'norf',
            'xyzzy' => [
                'en' => 'grault',
                'es' => 'frog',
                'gl' => 'corge',
            ]
        ]);

        $method = new \ReflectionMethod($this->filter, 'filterItem');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->filter, [ 'wobble' ]));

        $method->invokeArgs($this->filter, [ $item, [ 'keys' => [ 0 ] ] ]);
        $this->assertEquals([ 'fubar', 'glorp' ], $item);

        $method->invokeArgs($this->filter, [ $entity ]);

        $this->assertEquals('norf', $entity->flob);
        $this->assertEquals('frog', $entity->xyzzy);
    }

    /**
     * Tests filterValue with locale and default locale parameters.
     */
    public function testFilterValue()
    {
        $method = new \ReflectionMethod($this->filter, 'filterValue');
        $method->setAccessible(true);

        $this->assertEquals('fred', $method->invokeArgs($this->filter, [ 'fred' ]));
        $this->assertEquals(null, $method->invokeArgs($this->filter, [ null ]));
        $this->assertEquals(124, $method->invokeArgs($this->filter, [ 124 ]));
        $this->assertEquals('mumble', $method->invokeArgs($this->filter, [
            [ 'es' => 'mumble', 'en' => 'glork' ]
        ]));
        $this->assertEquals('mumble', $method->invokeArgs($this->filter, [
            [ 'gl' => 'mumble' ]
        ]));
        $this->assertEmpty($method->invokeArgs($this->filter, [
            [ 'gl' => '' ]
        ]));
        $this->assertEquals('glork', $method->invokeArgs($this->filter, [
            [ 'gl' => 'mumble', 'en' => 'glork' ]
        ]));

        $property = new \ReflectionProperty($this->filter, 'params');
        $property->setAccessible(true);
        $params = $property->getValue($this->filter);
        unset($params['locale']);
        $property->setValue($this->filter, $params);

        $this->assertEquals('mumble', $method->invokeArgs($this->filter, [
            [ 'gl' => 'mumble', 'en' => 'glork' ]
        ]));
    }
}
