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
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for Localizer class.
 */
class LocalizeFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filter = new LocalizeFilter($this->container);
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

        $this->assertEmpty($this->filter->filter('wobble'));

        $this->filter->filter($entities, [ 'keys' => [ 'xyzzy' ], 'locale' => 'es', 'default' => 'en' ]);
        $this->assertEquals('frog', $entities[0]->xyzzy);
        $this->assertEmpty($entities[1]->xyzzy);
        $this->assertEquals('quux', $entities[2]->xyzzy);
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

        $method->invokeArgs(
            $this->filter,
            [ $entity, [ 'keys' => [ 'xyzzy' ], 'locale' => 'gl' ] ]
        );

        $this->assertEquals('norf', $entity->flob);
        $this->assertEquals('corge', $entity->xyzzy);
    }

    /**
     * Tests filterValue with locale and default locale parameters.
     */
    public function testFilterValue()
    {
        $method = new \ReflectionMethod($this->filter, 'filterValue');
        $method->setAccessible(true);

        $this->assertEquals('fred', $method->invokeArgs($this->filter, [ 'fred', 'es' ]));
        $this->assertEquals(null, $method->invokeArgs($this->filter, [ null, 'es' ]));
        $this->assertEquals(124, $method->invokeArgs($this->filter, [ 124, 'es' ]));
        $this->assertEquals('mumble', $method->invokeArgs($this->filter, [
            [ 'es' => 'mumble', 'en' => 'glork' ],
            'es'
        ]));
        $this->assertEmpty($method->invokeArgs($this->filter, [
            [ 'es' => 'mumble', 'en' => 'glork' ],
            'gl'
        ]));
        $this->assertEquals('glork', $method->invokeArgs($this->filter, [
            [ 'es' => 'mumble', 'en' => 'glork' ],
            'gl',
            'en'
        ]));
    }
}
