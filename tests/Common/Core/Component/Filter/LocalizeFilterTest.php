<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Filter;

use Common\Core\Component\Filter\LocalizeFilter;
use Opennemas\Orm\Core\Entity;

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
            ->setMethods([ 'getRequestLocale', 'getContext' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->disableOriginalConstructor()
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'hasParameter', 'get' ])
            ->disableOriginalConstructor()
            ->getMock();

        $this->locale->expects($this->any())->method('getRequestLocale')
            ->willReturn('gl');

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->filter = new LocalizeFilter($this->container, [
            'keys'    => [ 'xyzzy' ],
            'locale'  => 'es',
            'default' => 'en'
        ]);
    }

    /**
     * Returns a mocked service based on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.locale':
                return $this->locale;

            case 'core.instance':
                return $this->instance;

            default:
                return null;
        }
    }

    /**
     * Tests filter.
     */
    public function testFilter()
    {
        $this->instance->expects($this->any())->method('hasMultilanguage')
            ->willReturn(false);

        $this->locale->expects($this->any())->method('getContext')
            ->willReturn('frontend');

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

        $related = [
            [
                'source_id'         => 866,
                'target_id'         => 876,
                'type'              => 'featured_frontpage',
                'content_type_name' => 'photo',
                'xyzzy'             => [
                    'es' => 'glorp',
                    'en' => 'baz'
                ],
            ],
            [
                'source_id'         => 516,
                'target_id'         => 1009,
                'type'              => 'featured_inner',
                'content_type_name' => 'photo',
                'xyzzy'             => [
                    'es' => 'corge',
                    'en' => 'mumble'
                ],
            ]
        ];

        $this->assertEmpty($this->filter->filter([]));
        $this->assertEmpty($this->filter->filter(null));
        $this->assertEquals('wobble', $this->filter->filter('wobble'));

        $translated = $this->filter->filter($related);
        $this->assertEquals('glorp', $translated[0]['xyzzy']);
        $this->assertEquals('corge', $translated[1]['xyzzy']);

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
     * Tests filter when backend context.
     */
    public function testFilterBackendContext()
    {
        $this->instance->expects($this->any())->method('hasMultilanguage')
            ->willReturn(true);

        $this->locale->expects($this->any())->method('getContext')
            ->willReturn('backend');

        $this->assertEquals(
            [ 'es_ES' => 'glorp', 'en_US' => 'baz'],
            $this->filter->filter([ 'es_ES' => 'glorp', 'en_US' => 'baz'])
        );
    }

    /**
     * Tests filterArray.
     */
    public function testFilterArray()
    {
        $related = [
            [
                'source_id'         => 866,
                'target_id'         => 876,
                'type'              => 'featured_frontpage',
                'content_type_name' => 'photo',
                'xyzzy'             => [
                    'es' => 'glorp',
                    'en' => 'baz'
                ],
            ],
            [
                'source_id'         => 516,
                'target_id'         => 1009,
                'type'              => 'featured_inner',
                'content_type_name' => 'photo',
                'xyzzy'             => [
                    'es' => 'corge',
                    'en' => 'mumble'
                ],
            ]
        ];

        $result             = $related;
        $result[0]['xyzzy'] = 'glorp';
        $result[1]['xyzzy'] = 'corge';

        $method = new \ReflectionMethod($this->filter, 'filterArray');
        $method->setAccessible(true);

        $this->assertEquals($result, $method->invokeArgs($this->filter, [ $related ]));
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
