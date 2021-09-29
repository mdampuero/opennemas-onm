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

use Api\Service\V1\SmartyService;
use Common\Core\Component\Template\Template;

/**
 * Defines test cases for SmartyService class.
 */
class SmartyServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Common\Core\Component\Template\Cache\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([
                'delete', 'deleteCompiles', 'read', 'setPath', 'write'
            ])->getMock();

        $this->container = $this->getMockBuilder('Container')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\EventDispatcher\EventDispatcher')
            ->disableOriginalConstructor()
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->template = new Template($this->container, []);
        $this->template->setConfigDir('/foobar/fred');

        $this->tq = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->setMethods([ 'push' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));


        $this->service = new SmartyService($this->container);
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
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'core.template.cache':
                return $this->cache;

            case 'core.template.frontend':
                return $this->template;

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
        $this->cache->expects($this->once())->method('delete')
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
        $this->cache->expects($this->once())->method('delete')
            ->with('glorp');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('smarty.deleteItem', [ 'action' => 'Api\Service\V1\SmartyService::deleteItem', 'id' => 'glorp' ]);

        $this->service->deleteItem('glorp');
    }

    /**
     * Tests deleteItem when the provided id is 'compile'.
     */
    public function testDeleteItemWhenCompile()
    {
        $this->cache->expects($this->once())->method('deleteCompiles');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('smarty.deleteItem', [ 'action' => 'Api\Service\V1\SmartyService::deleteItem', 'id' => 'compile' ]);

        $this->service->deleteItem('compile');
    }

    /**
     * Tests deleteList when an error is thrown.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenError()
    {
        $this->tq->expects($this->once())->method('push')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteList([]);
    }

    /**
     * Tests deleteList when no error thrown.
     */
    public function testDeleteListWhenNoError()
    {
        $this->tq->expects($this->once())->method('push');

        $this->service->deleteList([]);
    }

    /**
     * Returns the Smarty configuration.
     *
     * @return array The Smarty configuration.
     */
    public function testGetConfig()
    {
        $this->cache->expects($this->once())->method('setPath')
            ->with('/foobar/fred/')->willReturn($this->cache);
        $this->cache->expects($this->once())->method('read')
            ->willReturn([ 'grault' => [ 'caching' => 0 ] ]);

        $this->assertEquals([
            [ 'id' => 'grault', 'caching' => 0 ]
        ], $this->service->getConfig());
    }

    /**
     * Tests getItem.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItem()
    {
        $this->service->getItem('norf');
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
     * Tests isPattern.
     */
    public function testIsPattern()
    {
        $this->assertFalse($this->service->isPattern('baz'));
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
     * Update the Smarty configuration.
     *
     * @return array The Smarty configuration.
     */
    public function testUpdateConfig()
    {
        $this->cache->expects($this->once())->method('setPath')
            ->with('/foobar/fred/')->willReturn($this->cache);
        $this->cache->expects($this->once())->method('write')
            ->willReturn([ 'grault' => [ 'caching' => 0 ] ]);

        $this->service->updateConfig([
            [ 'id' => 'grault', 'caching' => 0, 'cache_lifetime' => 6077 ]
        ]);
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

        $this->assertEquals('smarty.waldo', $method->invokeArgs($this->service, [ 'waldo' ]));
    }
}
