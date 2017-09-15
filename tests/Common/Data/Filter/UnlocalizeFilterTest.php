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
use Common\ORM\Core\Entity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Defines test cases for UnlocalizeFilter class.
 */
class UnlocalizeFilterTest extends KernelTestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'hasParameter' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filter = new UnlocalizeFilter($this->container, [
            'keys'    => [ 'xyzzy' ],
            'locales' => [ 'es', 'gl' ]
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
                'flob'  => 'baz',
                'xyzzy' => 'corge'
            ])
        ];

        $this->assertEmpty($this->filter->filter([]));
        $this->assertEmpty($this->filter->filter(null));
        $this->assertEquals('wobble', $this->filter->filter('wobble'));

        $this->filter->filter($entities);
        $this->assertEquals([ 'en' => 'grault', 'es' => 'frog', 'gl' => 'corge' ], $entities[0]->xyzzy);
        $this->assertEquals([ 'es' => 'corge', 'gl' => 'corge' ], $entities[1]->xyzzy);
    }

    /**
     * Tests filterItem with object and non-object values.
     */
    public function testFilterItem()
    {
        $item   = [ 'fubar', 'glorp' ];
        $entity = new Entity([ 'flob' => 'norf', 'xyzzy' => 'grault' ]);

        $method = new \ReflectionMethod($this->filter, 'filterItem');
        $method->setAccessible(true);

        $this->assertEmpty($method->invokeArgs($this->filter, [ 'wobble' ]));

        $method->invokeArgs($this->filter, [ $item, [ 'keys' => [ 0 ] ] ]);
        $this->assertEquals([ 'fubar', 'glorp' ], $item);

        $method->invokeArgs($this->filter, [ $entity ]);

        $this->assertEquals('norf', $entity->flob);
        $this->assertEquals([ 'es' => 'grault', 'gl' => 'grault' ], $entity->xyzzy);
    }

    /**
     * Tests filterValue with locales defined.
     */
    public function testFilterValue()
    {
        $method = new \ReflectionMethod($this->filter, 'filterValue');
        $method->setAccessible(true);

        $this->assertEquals([ 'es' => 'fred', 'gl' => 'fred' ], $method->invokeArgs($this->filter, [ 'fred' ]));
        $this->assertEquals([ 'es' => null, 'gl' => null ], $method->invokeArgs($this->filter, [ null ]));
        $this->assertEquals([ 'es' => 124, 'gl' => 124 ], $method->invokeArgs($this->filter, [ 124 ]));
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
        $filter = new UnlocalizeFilter($this->container, [ 'keys'    => [ 'xyzzy' ] ]);

        $method = new \ReflectionMethod($filter, 'filterValue');
        $method->setAccessible(true);

        $this->assertEquals('fred', $method->invokeArgs($filter, [ 'fred' ]));
        $this->assertEquals(null, $method->invokeArgs($filter, [ null ]));
        $this->assertEquals(124, $method->invokeArgs($filter, [ 124 ]));
        $this->assertEquals(
            [ 'es' => 'mumble', 'gl' => 'glork' ],
            $method->invokeArgs($filter, [ [ 'es' => 'mumble', 'gl' => 'glork' ] ])
        );
        $this->assertEquals(
            [ 'gl' => 'mumble' ],
            $method->invokeArgs($filter, [ [ 'gl' => 'mumble' ] ])
        );
    }
}
