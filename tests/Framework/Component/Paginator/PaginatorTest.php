<?php

namespace Framework\Tests\Component\Paginator;

use Framework\Component\Paginator\Paginator;

class PaginatorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->router = $this->getMockBuilder('Symfony\Component\Routing\Router')
            ->disableOriginalConstructor()
            ->setMethods([ 'generate' ])
            ->getMock();

        $this->paginator = new Paginator($this->router);

        $reflection = new \ReflectionClass(get_class($this->paginator));
        $this->methods['getFirstLink'] = $reflection->getMethod('getFirstLink');
        $this->methods['getLastLink'] = $reflection->getMethod('getLastLink');
        $this->methods['getLinks'] = $reflection->getMethod('getLinks');
        $this->methods['getNextLink'] = $reflection->getMethod('getNextLink');
        $this->methods['getPreviousLink'] = $reflection->getMethod('getPreviousLink');
        $this->methods['getUrl'] = $reflection->getMethod('getUrl');

        foreach ($this->methods as $method) {
            $method->setAccessible(true);
        }
    }

    public function testConstruct()
    {
        $this->assertEquals($this->router, $this->paginator->router);
    }

    public function testToString()
    {
        $this->assertEquals(
            $this->paginator->links,
            $this->paginator->__toString()
        );
    }

    public function testEmpty()
    {
        $this->assertEmpty($this->paginator->get());
    }

    public function testGet()
    {
        $this->paginator->get([ 'epp' => 10, 'maxLinks' => 5, 'total' => 100 ]);

        $this->assertEquals(5, substr_count($this->paginator->links, '<li'));

        $this->paginator->get([ 'epp' => 7, 'maxLinks' => 3, 'total' => 100 ]);
        $this->assertEquals(3, substr_count($this->paginator->links, '<li'));

        // Test if passing epp=0 the return is empty.
        $this->assertEquals('', $this->paginator->get([ 'epp' => 0, 'maxLinks' => 5, 'total' => 100 ]));
    }

    public function testGetFirstLink()
    {
        $this->router->expects($this->once())->method('generate');

        $this->assertEmpty($this->paginator->get());
        $this->assertEmpty(
            $this->methods['getFirstLink']->invokeArgs($this->paginator, [])
        );

        $this->paginator->get([ 'boundary' => true, 'maxLinks' => 0, 'route' => 'foo']);
        $this->assertContains(
            _('First'),
            $this->methods['getFirstLink']->invokeArgs($this->paginator, [])
        );
    }

    public function testGetLastLink()
    {
        $this->router->expects($this->once())->method('generate');

        $this->assertEmpty($this->paginator->get());
        $this->assertEmpty(
            $this->methods['getLastLink']->invokeArgs($this->paginator, [])
        );

        $this->paginator->get([ 'boundary' => true, 'pages' => 4, 'route' => 'foo' ]);
        $this->assertContains(
            _('Last'),
            $this->methods['getLastLink']->invokeArgs($this->paginator, [])
        );
    }

    public function testGetLinks()
    {
        $this->router->expects($this->exactly(4))->method('generate');

        $this->assertEmpty($this->paginator->get([ 'maxLinks' => 0 ]));
        $this->assertEmpty(
            $this->methods['getLinks']->invokeArgs($this->paginator, [])
        );

        $this->paginator->get([ 'maxLinks' => 4, 'pages' => 4, 'route' => 'foo' ]);
        $links = $this->methods['getLinks']->invokeArgs($this->paginator, []);
        $this->assertEquals(4, substr_count($links, '<li'));
    }

    public function testGetNextLink()
    {
        $this->router->expects($this->once())->method('generate');

        $this->assertEmpty($this->paginator->get());
        $this->assertEmpty(
            $this->methods['getNextLink']->invokeArgs($this->paginator, [])
        );

        $this->paginator->get([ 'directional' => true, 'pages' => 4, 'route' => 'foo']);
        $this->assertContains(
            _('Next'),
            $this->methods['getNextLink']->invokeArgs($this->paginator, [])
        );
    }

    public function testGetPreviousLink()
    {
        $this->router->expects($this->once())->method('generate');

        $this->assertEmpty($this->paginator->get());
        $this->assertEmpty(
            $this->methods['getPreviousLink']->invokeArgs($this->paginator, [])
        );

        $this->paginator->get([ 'directional' => true, 'pages' => 4 , 'route' => 'foo' ]);
        $this->assertContains(
            _('Previous'),
            $this->methods['getPreviousLink']->invokeArgs($this->paginator, [])
        );
    }

    public function testGetUrl()
    {
        $this->router->expects($this->exactly(2))->method('generate')
            ->with('foo', [ 'page' => 1 ]);

        $this->paginator->get([ 'route' => 'foo' ]);
        $this->methods['getUrl']->invokeArgs($this->paginator, [ 1 ]);

        $this->paginator->get([ 'route' => [ 'name' => 'foo', 'params' => [] ] ]);
        $this->methods['getUrl']->invokeArgs($this->paginator, [ 1 ]);
    }
}
