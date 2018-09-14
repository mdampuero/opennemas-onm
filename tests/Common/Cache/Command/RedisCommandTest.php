<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Common\Cache\Command;

use Common\Cache\Command\RedisCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Tests the cache:redis command.
 */
class CacheCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the test environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Cache\Core\Cache')
            ->disableOriginalConstructor()
            ->setMethods([
                'exists', 'get', 'remove', 'removeByPattern', 'setNamespace',
                'contains', 'delete', 'deleteMulti', 'deleteByPattern', 'fetch',
                'fetchMulti', 'save', 'saveMulti'
            ])->getMock();

        $this->cm = $this->getMockBuilder('CacheManager')
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->oldCache = $this->getMockBuilder('Cache')
            ->setMethods([ 'contains', 'fetch', 'delete', 'setNamespace' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->cm->expects($this->any())->method('getConnection')
            ->willReturn($this->cache);

        $this->input = $this->getMockBuilder('Input')
            ->setMethods([ 'getArgument', 'getOption' ])
            ->getMock();

        $this->command = new RedisCommand();

        $this->command->setContainer($this->container);

        $this->command->input = $this->input;
    }

    /**
     * Returns a mock basing on the service name.
     *
     * @param string $name The service name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'cache':
                return $this->oldCache;

            case 'cache.manager':
                return $this->cm;
        }

        return null;
    }

    /**
     * Tests execute for invalid action.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testGetActionForInvalidAction()
    {
        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'action'      => 'glork',
            'command'     => $this->command->getName(),
            '--key'       => 'quux',
            '--namespace' => 'quux'
        ]);
    }

    /**
     * Tests execute with get action for new cache connection.
     */
    public function testGetActionForNewCache()
    {
        $this->cache->expects($this->once())->method('get')
            ->with('quux')->willReturn('waldo');

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'action'      => 'get',
            'command'     => $this->command->getName(),
            '--key'       => 'quux',
            '--namespace' => 'quux'
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('key', $output);
        $this->assertContains('value', $output);
        $this->assertContains('quux', $output);
        $this->assertContains('waldo', $output);
    }

    /**
     * Tests execute with get action for new cache connection.
     */
    public function testGetActionForOld()
    {
        $this->oldCache->expects($this->once())->method('fetch')
            ->with('quux')->willReturn('waldo');

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $commandTester->execute([
            'action'      => 'get',
            'command'     => $this->command->getName(),
            '--key'       => 'quux',
            '--namespace' => 'quux',
            '--old'       => null
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('key', $output);
        $this->assertContains('value', $output);
        $this->assertContains('quux', $output);
        $this->assertContains('waldo', $output);
    }

    /**
     * Tests checkExists when key is missing.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckExistsWhenKeyMissing()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('key')->willReturn(false);

        $method = new \ReflectionMethod($this->command, 'checkExists');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkExists when key provided.
     */
    public function testCheckExistsWhenKeyProvided()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('key')->willReturn('wibble');

        $method = new \ReflectionMethod($this->command, 'checkExists');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkGet when key is missing.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckGetWhenKeyMissing()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('key')->willReturn(false);

        $method = new \ReflectionMethod($this->command, 'checkGet');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkGet when key provided.
     */
    public function testCheckGetWhenKeyProvided()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('key')->willReturn('wibble');

        $method = new \ReflectionMethod($this->command, 'checkGet');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkNamespace when key is missing.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckNamespaceWhenNamespaceMissing()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('namespace')->willReturn(false);

        $method = new \ReflectionMethod($this->command, 'checkNamespace');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkNamespace when key provided.
     */
    public function testCheckNamespaceWhenNamespaceProvided()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('namespace')->willReturn('wibble');

        $method = new \ReflectionMethod($this->command, 'checkNamespace');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkRemove when key is missing.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testCheckRemoveWhenKeyMissing()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn(false);
        $this->input->expects($this->at(1))->method('getOption')
            ->with('pattern')->willReturn(false);

        $method = new \ReflectionMethod($this->command, 'checkRemove');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkRemove when key provided.
     */
    public function testCheckRemoveWhenKeyProvided()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn('wibble');

        $method = new \ReflectionMethod($this->command, 'checkRemove');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests checkRemove when pattern provided.
     */
    public function testCheckRemoveWhenPatternProvided()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn(false);
        $this->input->expects($this->at(1))->method('getOption')
            ->with('pattern')->willReturn('wibble');

        $method = new \ReflectionMethod($this->command, 'checkRemove');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests getCache with different parameters.
     */
    public function testGetCacheWhenNoOldParameter()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('old')->willReturn(false);

        $method = new \ReflectionMethod($this->command, 'getCache');
        $method->setAccessible(true);

        $this->assertEquals($this->cache, $method->invokeArgs($this->command, [ 'xyzzy' ]));
    }

    /**
     * Tests getCache with different parameters.
     */
    public function testGetCacheWhenOldParameter()
    {
        $this->input->expects($this->once())->method('getOption')
            ->with('old')->willReturn(true);

        $method = new \ReflectionMethod($this->command, 'getCache');
        $method->setAccessible(true);

        $this->assertEquals($this->oldCache, $method->invokeArgs($this->command, [ 'manager' ]));
    }

    /**
     * Tests executeExists when the given key is a string.
     */
    public function testExecuteExistsWhenString()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn('thud');

        $this->input->expects($this->at(1))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->once())->method('exists')
            ->with('thud')->willReturn(false);

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeExists');
        $method->setAccessible(true);

        $this->assertEquals(
            [ [ 'key' => 'thud', 'value' => 0 ] ],
            $method->invokeArgs($this->command, [])
        );
    }

    /**
     * Tests executeExists when the given key is a string.
     */
    public function testExecuteExistsWhenArray()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn([ 'thud', 'glorp' ]);

        $this->input->expects($this->at(1))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->at(1))->method('exists')
            ->with('thud')->willReturn(false);
        $this->cache->expects($this->at(2))->method('exists')
            ->with('glorp')->willReturn(true);

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeExists');
        $method->setAccessible(true);

        $this->assertEquals([
            [ 'key' => 'thud', 'value' => 0 ],
            [ 'key' => 'glorp', 'value' => 1 ]
        ], $method->invokeArgs($this->command, []));
    }

    /**
     * Tests executeGet when the given key is a string.
     */
    public function testExecuteGetWhenString()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn('thud');

        $this->input->expects($this->at(1))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->once())->method('get')
            ->with('thud')->willReturn('grault');

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeGet');
        $method->setAccessible(true);

        $this->assertEquals(
            [ [ 'key' => 'thud', 'value' => 'grault' ] ],
            $method->invokeArgs($this->command, [])
        );
    }

    /**
     * Tests executeGet when the given key is a string.
     */
    public function testExecuteGetWhenArray()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('key')->willReturn([ 'thud', 'glorp' ]);

        $this->input->expects($this->at(1))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->at(1))->method('get')
            ->with('thud')->willReturn('grault');
        $this->cache->expects($this->at(2))->method('get')
            ->with('glorp')->willReturn('qux');

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeGet');
        $method->setAccessible(true);

        $this->assertEquals([
            [ 'key' => 'thud', 'value' => 'grault' ],
            [ 'key' => 'glorp', 'value' => 'qux' ]
        ], $method->invokeArgs($this->command, []));
    }

    /**
     * Tests executeRemove when the given key is a string.
     */
    public function testExecuteRemoveByKeysWhenString()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('pattern')->willReturn(null);

        $this->input->expects($this->at(1))->method('getOption')
            ->with('key')->willReturn('thud');

        $this->input->expects($this->at(2))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->once())->method('remove')
            ->with('thud')->willReturn('grault');

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeRemove');
        $method->setAccessible(true);

        $this->assertEquals(
            [ [ 'key' => 'thud', 'value' => 'grault' ] ],
            $method->invokeArgs($this->command, [])
        );
    }

    /**
     * Tests executeRemove when the given key is a string.
     */
    public function testExecuteRemoveByKeysWhenArray()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('pattern')->willReturn(null);

        $this->input->expects($this->at(1))->method('getOption')
            ->with('key')->willReturn([ 'thud', 'glorp' ]);

        $this->input->expects($this->at(2))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->at(1))->method('remove')
            ->with('thud')->willReturn(1);
        $this->cache->expects($this->at(2))->method('remove')
            ->with('glorp')->willReturn(0);

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeRemove');
        $method->setAccessible(true);

        $this->assertEquals([
            [ 'key' => 'thud', 'value' => 1 ],
            [ 'key' => 'glorp', 'value' => 0 ]
        ], $method->invokeArgs($this->command, []));
    }

    /**
     * Tests executeRemove when the given key is a pattern.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testExecuteRemoveByKeysForInvalidCache()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('pattern')->willReturn('xyzzy*');

        $this->command->cache = $this->oldCache;

        $method = new \ReflectionMethod($this->command, 'executeRemove');
        $method->setAccessible(true);

        $method->invokeArgs($this->command, []);
    }

    /**
     * Tests executeRemove when the given key is a string.
     */
    public function testExecuteRemoveByPatternForValidCache()
    {
        $this->input->expects($this->at(0))->method('getOption')
            ->with('pattern')->willReturn('xyzzy*');

        $this->input->expects($this->at(1))->method('getOption')
            ->with('pattern')->willReturn('xyzzy*');

        $this->input->expects($this->at(2))->method('getOption')
            ->with('namespace')->willReturn('corge');

        $this->cache->expects($this->once())->method('setNamespace')
            ->with('corge');

        $this->cache->expects($this->once())->method('removeByPattern')
            ->with('xyzzy*')->willReturn(1);

        $this->command->cache = $this->cache;

        $method = new \ReflectionMethod($this->command, 'executeRemove');
        $method->setAccessible(true);

        $this->assertEquals(1, $method->invokeArgs($this->command, []));
    }
}
