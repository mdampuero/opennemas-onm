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

use Api\Service\V1\NewsAgencyServerService;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for NewsAgencyServerService class.
 */
class NewsAgencyServerServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->dataset = $this->getMockBuilder('Opennemas\Orm\Core\DataSet')
            ->setMethods([ 'delete', 'get', 'init', 'set' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('Common\Core\Component\Event\EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('Opennemas\Orm\Core\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->queue = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->setMethods([ 'push' ])
            ->getMock();

        $this->instance = new Instance([ 'internal_name' => 'flob' ]);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->container->expects($this->any())->method('getParameter')
            ->with('core.paths.cache')->willReturn('/thud/fred');

        $this->dataset->expects($this->any())->method('init')
            ->willReturn($this->dataset);

        $this->dataset->expects($this->any())->method('get')
            ->with('news_agency_config')->willReturn([]);

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')->willReturn($this->dataset);

        $this->service = new NewsAgencyServerService($this->container);
    }

    /**
     * Returns a mock for a service based on the provided name.
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

            case 'core.instance':
                return $this->instance;

            case 'orm.manager':
                return $this->em;

            case 'task.service.queue':
                return $this->queue;

            default:
                return null;
        }
    }

    /**
     * Tests createItem.
     */
    public function testCreateItem()
    {
        $data = [ 'xyzzy' => 'foobar' ];

        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config', [ $data ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('news_agency.server.createItem', [
                'action' => 'Api\Service\V1\NewsAgencyServerService::createItem',
                'id'     => 1,
                'item'   => $data
            ]);

        $this->service->createItem($data);
    }

    /**
     * Tests createItem when there is an error while saving.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenError()
    {
        $this->dataset->expects($this->once())->method('set')
            ->will($this->throwException(new \Exception()));

        $this->service->createItem([ 'xyzzy' => 'foobar' ]);
    }

    /**
     * Tests deleteItem.
     */
    public function testDeleteItem()
    {
        $data = [ 'id' => 1, 'xyzzy' => 'foobar' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config', []);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('news_agency.server.getItem', [ 'id' => 1, 'item' => $data ]);
        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('news_agency.server.deleteItem', [
                'action' => 'Api\Service\V1\NewsAgencyServerService::deleteItem',
                'id'     => 1,
                'item'   => $data
            ]);

        $this->service->deleteItem(1);
    }

    /**
     * Tests deleteItem when there is an error while saving.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenError()
    {
        $this->service->deleteItem(22360);
    }

    /**
     * Tests deleteList.
     */
    public function testDeleteList()
    {
        $data = [
            [ 'id' => 1, 'xyzzy' => 'foobar' ],
            [ 'id' => 2, 'xyzzy' => 'baz' ]
        ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, $data);

        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config', []);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('news_agency.server.deleteList', [
                'action' => 'Api\Service\V1\NewsAgencyServerService::deleteList',
                'ids'    => [ 1, 2 ],
                'item'   => $data
            ]);

        $this->service->deleteList([ 1, 2 ]);
    }

    /**
     * Tests deleteList when there is an error while deleting.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenError()
    {
        $data = [ 'id' => 1, 'xyzzy' => 'foobar' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteList([ 1 ]);
    }

    /**
     * Tests deleteList when the list of ids is invalid.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenInvalidIds()
    {
        $this->service->deleteList(22360);
    }

    /**
     * Tests emptyItem.
     */
    public function testEmptyItem()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->queue->expects($this->once())->method('push');
        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('news_agency.server.emptyItem', [
                'id' => 1
            ]);

        $this->service->emptyItem(1);
    }

    /**
     * Tests emptyItem when there is an error while saving.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testEmptyItemWhenError()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->queue->expects($this->once())->method('push')
            ->will($this->throwException(new \Exception()));

        $this->service->emptyItem(1);
    }

    /**
     * Tests getItem.
     */
    public function testGetItem()
    {
        $data = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('news_agency.server.getItem', [
                'id'   => 1,
                'item' => array_merge([ 'id' => 1 ], $data)
            ]);

        $this->assertEquals(
            array_merge([ 'id' => 1 ], $data),
            $this->service->getItem(1)
        );
    }

    /**
     * Tests getItem when the item is not found.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenError()
    {
        $this->service->getItem(22360);
    }

    /**
     * Tests getList.
     */
    public function testGetList()
    {
        $data = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('news_agency.server.getList', [
                'items' => [ array_merge([ 'id' => 1 ], $data) ],
                'oql'   => ''
            ]);

        $this->assertEquals(
            [ 'items' => [ array_merge([ 'id' => 1 ], $data) ], 'total' => 1 ],
            $this->service->getList()
        );
    }

    /**
     * Tests getList when error.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListWhenError()
    {
        $data = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->will($this->throwException(new \Exception()));

        $this->service->getList();
    }

    /**
     * Tests getListByIds.
     */
    public function testGetListByIds()
    {
        $data = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('news_agency.server.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ array_merge([ 'id' => 1 ], $data) ]
            ]);

        $this->assertEquals(
            [ 'items' => [ array_merge([ 'id' => 1 ], $data) ], 'total' => 1 ],
            $this->service->getListByIds([ 1, 2 ])
        );
    }

    /**
     * Tests getListByIds when list of provided ids is empty.
     */
    public function testGetListByIdsWhenEmptyIds()
    {
        $this->assertEquals(
            [ 'items' => [], 'total' => 0 ],
            $this->service->getListByIds([])
        );
    }

    /**
     * Tests getListByIds when no valid ids provided.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListByIdsWhenInvalidIds()
    {
        $this->service->getListByIds(null);
    }

    /**
     * Tests getListByIds when error.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListByIdsWhenError()
    {
        $data = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $data ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->will($this->throwException(new \Exception()));

        $this->service->getListByIds([ 1, 2 ]);
    }

    /**
     * Tests patchItem.
     */
    public function testPatchItem()
    {
        $config = [ 'foo' => 'quux' ];
        $data   = [ 'waldo' => 'foobar' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('news_agency.server.patchItem', [
                'action' => 'Api\Service\V1\NewsAgencyServerService::patchItem',
                'id'     => 1,
                'item'   => array_merge([ 'foo' => 'quux' ], $data)
            ]);

        $this->service->patchItem(1, $data);
    }

    /**
     * Tests patchItem when there is an error while saving.
     *
     * @expectedException Api\Exception\PatchItemException
     */
    public function testPatchItemWhenError()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->dataset->expects($this->once())->method('set')
            ->will($this->throwException(new \Exception()));

        $this->service->patchItem(1, [ 'xyzzy' => 'foobar' ]);
    }

    /**
     * Tests patchItem when the provided id is invalid.
     *
     * @expectedException Api\Exception\PatchItemException
     */
    public function testPatchItemWhenInvalidId()
    {
        $this->service->patchItem(null, [ 'gorp' => 'plugh' ]);
    }

    /**
     * Tests patchList.
     */
    public function testPatchList()
    {
        $config = [ 'foo' => 'quux' ];
        $data   = [ 'waldo' => 'foobar' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config');

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('news_agency.server.patchList', [
                'action' => 'Api\Service\V1\NewsAgencyServerService::patchList',
                'ids'    => [ 1 ],
                'item'   => [ array_merge([ 'foo' => 'quux' ], $data) ]
            ]);

        $this->service->patchList([ 1 ], $data);
    }

    /**
     * Tests patchList when the list of provided ids is invalid.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchListWhenInvalidIds()
    {
        $this->service->patchList([], [ 'gorp' => 'plugh' ]);
    }

    /**
     * Tests patchList when an id in the list of ids is missing.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchListWhenMissingId()
    {
        $config = [ 'foo' => 'quux' ];
        $data   = [ 'waldo' => 'foobar' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->service->patchList([ 1, 2 ], $data);
    }

    /**
     * Tests patchList when there is an error while saving.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchListWhenError()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->dataset->expects($this->once())->method('set')
            ->will($this->throwException(new \Exception()));

        $this->service->patchList([ 1 ], [ 'xyzzy' => 'foobar' ]);
    }

    /**
     * Tests responsify.
     */
    public function testResponsify()
    {
        $item = [ 'xyzzy' => 'foobar' ];

        $this->assertEquals($item, $this->service->responsify($item));
    }

    /**
     * Tests synchronizeItem.
     */
    public function testSynchronizeItem()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->queue->expects($this->once())->method('push');
        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('news_agency.server.synchronizeItem', [
                'id' => 1
            ]);

        $this->service->synchronizeItem(1);
    }

    /**
     * Tests updateItem when there is an error while saving.
     *
     * @expectedException Api\Exception\ApiException
     */
    public function testSynchronizeItemWhenError()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->queue->expects($this->once())->method('push')
            ->will($this->throwException(new \Exception()));

        $this->service->synchronizeItem(1);
    }

    /**
     * Tests updateItem.
     */
    public function testUpdateItem()
    {
        $config = [ 'foo' => 'quux' ];
        $data   = [ 'xyzzy' => 'foobar' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);


        $this->dataset->expects($this->once())->method('set')
            ->with('news_agency_config', [ $data ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('news_agency.server.updateItem', [
                'action' => 'Api\Service\V1\NewsAgencyServerService::updateItem',
                'id'     => 1,
                'item'   => $data
            ]);

        $this->service->updateItem(1, $data);
    }

    /**
     * Tests updateItem when there is an error while saving.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenError()
    {
        $config = [ 'foo' => 'quux' ];

        $property = new \ReflectionProperty($this->service, 'config');
        $property->setAccessible(true);
        $property->setValue($this->service, [ $config ]);

        $this->dataset->expects($this->once())->method('set')
            ->will($this->throwException(new \Exception()));

        $this->service->updateItem(1, [ 'xyzzy' => 'foobar' ]);
    }

    /**
     * Tests updateItem when the provided id is invalid.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenInvalidId()
    {
        $this->service->updateItem(null, [ 'gorp' => 'plugh' ]);
    }
}
