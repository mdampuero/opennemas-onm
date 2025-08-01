<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\Service\V1;

use Api\Service\V1\RedisService;

/**
 * Defines test cases for RedisService class.
 */
class RedisServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Opennemas\Cache\Redis\Redis')
            ->disableOriginalConstructor()
            ->setMethods([ 'get', 'remove', 'removeByPattern' ])
            ->getMock();

        $this->cm = $this->getMockBuilder('Opennemas\Cache\Core\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->tq = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->setMethods([ 'push' ])
            ->getMock();

        $this->cm->expects($this->any())->method('getConnection')
            ->with('instance')->willReturn($this->cache);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->service = new RedisService($this->container);
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
            case 'cache.manager':
                return $this->cm;

            case 'core.dispatcher':
                return $this->dispatcher;

            case 'task.service.queue':
                return $this->tq;

            default:
                return null;
        }
    }

    /**
     * Tests createItem.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItem()
    {
        $this->service->createItem([ 16373 ]);
    }

    /**
     * Tests deleteItem when an error is thrown.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenError()
    {
        $this->cache->expects($this->once())->method('get')
            ->with('glorp')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteItem('glorp');
    }

    /**
     * Tests deleteByPattern when no error is thrown and the input is not a
     * pattern.
     */
    public function testDeleteItemWhenNoError()
    {
        $this->cache->expects($this->once())->method('get')
            ->with('glorp')
            ->willReturn('garply');

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('redis.getItem', [ 'id' => 'glorp', 'item' => 'garply' ]);
        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('redis.deleteItem', [
                'action' => 'Api\Service\V1\RedisService::deleteItem',
                'id'     => 'glorp',
                'item'   => 'garply'
            ]);

        $this->service->deleteItem('glorp');
    }

    /**
     * Tests deleteByPattern when no error is thrown and the input is a pattern.
     */
    public function testDeleteItemWhenPattern()
    {
        $this->tq->expects($this->once())->method('push');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('redis.deleteItemByPattern', [ 'id' => 'glorp*flob' ]);

        $this->service->deleteItem('glorp*flob');
    }

    /**
     * Tests deleteByPattern when an error is thrown.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemByPatternWhenError()
    {
        $this->tq->expects($this->once())->method('push')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteItemByPattern('glorp*flob');
    }

    /**
     * Tests deleteByPattern when no error is thrown.
     */
    public function testDeleteItemByPatternWhenNoError()
    {
        $this->tq->expects($this->once())->method('push');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('redis.deleteItemByPattern', [ 'id' => 'glorp*flob' ]);

        $this->service->deleteItemByPattern('glorp*flob');
    }

    /**
     * Tests deleteList.
     */
    public function testDeleteList()
    {
        $service = $this->getMockBuilder('Api\Service\V1\RedisService')
            ->setConstructorArgs([ $this->container ])
            ->setMethods([ 'deleteItemByPattern' ])
            ->getMock();

        $service->expects($this->once())->method('deleteItemByPattern')
            ->with('*');

        $service->deleteList([]);
    }

    /**
     * Tests getConfig.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testGetConfig()
    {
        $this->service->getConfig();
    }

    /**
     * Tests getItem when a error is thrown.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenError()
    {
        $this->cache->expects($this->once())->method('get')
            ->will($this->throwException(new \Exception()));

        $this->service->getItem('norf');
    }

    /**
     * Tests getItem when no error is thrown.
     */
    public function testGetItemWhenNoError()
    {
        $this->cache->expects($this->once())->method('get')
            ->with('norf')->willReturn('frog');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('redis.getItem', [ 'id' => 'norf', 'item' => 'frog' ]);

        $this->assertEquals('frog', $this->service->getItem('norf'));
    }

    /**
     * Tests getItem when the provided id is a pattern.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenPattern()
    {
        $this->service->getItem('norf*');
    }

    /**
     * Tests getList.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetList()
    {
        $this->service->getList();
    }

    /**
     * Tests getListByIds.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListByIds()
    {
        $this->service->getListByIds([ 1028 ]);
    }

    /**
     * Tests isPattern for specific and pattern values.
     */
    public function testIsPattern()
    {
        $this->assertFalse($this->service->isPattern('grault'));
        $this->assertTrue($this->service->isPattern('baz*glork'));
    }

    /**
     * Tests patchItem.
     *
     * @expectedException Api\Exception\PatchItemException
     */
    public function testPatchItem()
    {
        $this->service->patchItem('fred', []);
    }

    /**
     * Tests patchList.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchList()
    {
        $this->service->patchList('fred', []);
    }

    /**
     * Tests responsify.
     */
    public function testResponsify()
    {
        $this->assertEquals('fubar', $this->service->responsify('fubar'));
    }

    /**
     * Tests updateConfig.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testUpdateConfig()
    {
        $this->service->updateConfig([]);
    }

    /**
     * Tests updateItem.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItem()
    {
        $this->service->updateItem('fred', []);
    }

    /**
     * Tests getEventName.
     */
    public function testGetEventName()
    {
        $method = new \ReflectionMethod($this->service, 'getEventName');
        $method->setAccessible(true);

        $this->assertEquals('redis.waldo', $method->invokeArgs($this->service, [ 'waldo' ]));
    }
}
