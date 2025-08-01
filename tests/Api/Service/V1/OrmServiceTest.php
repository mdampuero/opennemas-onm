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

use Api\Service\V1\OrmService;
use Common\Model\Entity\Content;
use Opennemas\Orm\Core\Entity;

/**
 * Defines test cases for OrmService class.
 */
class OrmServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->converter = $this->getMockBuilder('Converter' . uniqid())
            ->setMethods([ 'objectify', 'responsify' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->fm = $this->getMockBuilder('FilterManager')
            ->setMethods([ 'filter', 'get', 'set' ])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getId', 'getIdKeys', 'getL10nKeys' ])
            ->getMock();

        $this->locale = $this->getMockBuilder('Locale')
            ->setMethods([ 'getContext' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'find', 'findBy', 'findBySql' ])
            ->getMock();

        $this->validator = $this->getMockBuilder('Validator' . uniqid())
            ->setMethods([ 'validate' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new OrmService(
            $this->container,
            'Opennemas\Orm\Core\Entity',
            $this->validator
        );
    }

    /**
     * Returns a mock basing on the requested service name.
     *
     * @return mixed A mock.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'core.locale':
                return $this->locale;

            case 'data.manager.filter':
                return $this->fm;

            case 'orm.manager':
                return $this->em;
        }
    }

    /**
     * Tests createItem when no error.
     */
    public function testCreateItem()
    {
        $data = [ 'name' => 'flob' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with($data)->willReturn($data);
        $this->em->expects($this->once())->method('persist');
        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.createItem');
        $this->metadata->expects($this->once())->method('getId')
            ->willReturn([ 'id' => 1 ]);

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
    }

    /**
     * Tests createItem when an error happens while converting data.
     *
     * @expectedException \Api\Exception\CreateItemException
     */
    public function testCreateWhenErrorWhileConverting()
    {
        $this->converter->expects($this->any())->method('objectify')
            ->will($this->throwException(new \Exception()));

        $this->service->createItem([]);
    }

    /**
     * Tests createItem when an error happens while persisting object.
     *
     * @expectedException \Api\Exception\CreateItemException
     */
    public function testCreateWhenErrorWhilePersisting()
    {
        $this->converter->expects($this->any())->method('objectify');
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception()));

        $this->service->createItem([]);
    }

    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $item = new Entity();

        $this->repository->expects($this->once())->method('find')
            ->willReturn($item);

        $this->em->expects($this->once())->method('remove')
            ->with($item);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getItem', [ 'id' => 23, 'item' => $item ]);
        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('entity.deleteItem', [
                'action'  => 'Api\Service\V1\OrmService::deleteItem',
                'id'      => 23,
                'item'    => $item,
                'last_changed' => ''
            ]);

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when no item found.
     *
     * @expectedException \Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenNoEntity()
    {
        $this->repository->expects($this->any())->method('find')
            ->will($this->throwException(new \Exception()));

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when an error happens while removing object.
     *
     * @expectedException \Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenErrorWhileRemoving()
    {
        $item = new Entity();

        $this->repository->expects($this->once())->method('find')
            ->willReturn($item);
        $this->em->expects($this->once())->method('remove')
            ->with($item)->will($this->throwException(new \Exception()));

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getItem', [ 'id' => 23, 'item' => $item ]);

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteList when no error.
     */
    public function testDeleteList()
    {
        $itemA = new Entity([ 'pk_content' => 1, 'name' => 'wubble']);
        $itemB = new Entity([ 'pk_content' => 2, 'name' => 'xyzzy' ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);

        $this->em->expects($this->exactly(2))->method('remove');

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('entity.deleteList', [
                'action'  => 'Api\Service\V1\OrmService::deleteList',
                'ids'     => [ 1, 2 ],
                'item'    => [ $itemA, $itemB ],
                'last_changed' => []
            ]);

        $this->assertEquals(2, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when invalid list of ids provided.
     *
     * @expectedException \Api\Exception\DeleteListException
     */
    public function testDeleteListWhenInvalidIds()
    {
        $this->service->deleteList('xyzzy');
    }

    /**
     * Tests deleteList when one error happens while removing.
     *
     * @expectedException \Api\Exception\DeleteListException
     */
    public function testDeleteListWhenOneErrorWhileRemoving()
    {
        $itemA = new Entity([ 'name' => 'wubble']);
        $itemB = new Entity([ 'name' => 'xyzzy' ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);

        $this->em->expects($this->once())->method('remove')
            ->will($this->throwException(new \Exception()));

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->assertEquals(2, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when an error happens while searching.
     *
     * @expectedException \Api\Exception\DeleteListException
     */
    public function testDeleteListWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->will($this->throwException(new \Exception()));

        $this->service->deleteList([ 1, 2 ]);
    }

    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity();

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.getItem', [ 'id'   => 1, 'item' => $item ]);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the provided id is empty.
     *
     * @expectedException \Api\Exception\GetItemException
     */
    public function testGetItemWhemEmptyId()
    {
        $this->service->getItem(0);
    }

    /**
     * Tests getItem when an error happens while converting data.
     *
     * @expectedException \Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('find')
            ->with(1)->will($this->throwException(new \Exception()));

        $this->service->getItem(1);
    }

    /**
     * Tests getItemBy when no error.
     */
    public function testGetItemBy()
    {
        $item = new Entity([ 'name' => 'wubble' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('order by title asc')->willReturn([ $item ]);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getList', [
                'items' => [ $item ],
                'oql'   => 'order by title asc'
            ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('entity.getItemBy', [
                'item' => $item,
                'oql'  => 'order by title asc'
            ]);

        $response = $this->service->getItemBy('order by title asc');

        $this->assertEquals($item, $response);
    }

    /**
     * Tests getItemBy when error while searching.
     *
     * @expectedException \Api\Exception\GetItemException
     */
    public function testGetItemByWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->will($this->throwException(new \Exception()));

        $this->service->getItemBy('order by title asc');
    }

    /**
     * Tests getItemBy when the criteria returns more than one result.
     *
     * @expectedException \Api\Exception\GetItemException
     */
    public function testGetItemByWhenMoreThanOneResult()
    {
        $items = [
            new Entity([ 'name' => 'wubble' ]),
            new Entity([ 'name' => 'mumble' ])
        ];

        $this->repository->expects($this->once())->method('findBy')
            ->with('order by title asc')->willReturn($items);

        $this->service->getItemBy('order by title asc');
    }

    /**
     * Tests getList when no error.
     */
    public function testGetList()
    {
        $items = [
            new Entity([ 'name' => 'wubble' ]),
            new Entity([ 'name' => 'mumble' ])
        ];

        $this->repository->expects($this->once())->method('countBy')
            ->with('order by title asc')->willReturn(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with('order by title asc')->willReturn($items);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.getList', [
                'items' => $items,
                'oql'   => 'order by title asc'
            ]);

        $response = $this->service->getList('order by title asc');

        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertEquals($items, $response['items']);
        $this->assertEquals(2, $response['total']);
    }

    /**
     * Tests getList when no count required.
     */
    public function testGetListWhenNoCount()
    {
        $items = [
            new Entity([ 'name' => 'wubble' ]),
            new Entity([ 'name' => 'mumble' ])
        ];

        $this->repository->expects($this->once())->method('findBy')
            ->with('order by title asc')->willReturn($items);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.getList', [
                'items' => $items,
                'oql'   => 'order by title asc'
            ]);

        $response = $this->service->setCount(false)
            ->getList('order by title asc');

        $this->assertArrayHasKey('items', $response);
        $this->assertArrayNotHasKey('total', $response);
    }

    /**
     * Tests getList when there is an error while counting contents.
     *
     * @expectedException \Api\Exception\GetListException
     */
    public function testGetListWhenErrorWhileCounting()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([]);
        $this->repository->expects($this->once())->method('countBy')
            ->will($this->throwException(new \Exception()));

        $this->service->getList('order by title asc');
    }

    /**
     * Tests getList when there is an error while searching contents.
     *
     * @expectedException \Api\Exception\GetListException
     */
    public function testGetListWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->will($this->throwException(new \Exception()));

        $this->service->getList('order by title asc');
    }

    /**
     * Tests getListBySql when no error.
     */
    public function testGetListBySql()
    {
        $sql    = 'select count(*) from glorp';
        $items  = [ new Content()  ];
        $result = [ 'items' => $items, 'total' => count($items) ];

        $this->repository->expects($this->once())->method('findBySql')
            ->with($sql)
            ->willReturn($items);

        $this->assertEquals($result, $this->service->getListBySql($sql));
    }

    /**
     * Tests getListBySql when error.
     *
     * @expectedException \Api\Exception\GetListException
     */
    public function testGetListWhenException()
    {
        $sql = 'select count(*) from glorp';

        $this->repository->expects($this->once())->method('findBySql')
            ->will($this->throwException(new \Exception()));

        $this->service->getListBySql($sql);
    }

    /**
     * Tests getListByIds when no error.
     */
    public function testGetListByIds()
    {
        $items = [
            new Entity([ 'name' => 'wubble' ]),
            new Entity([ 'name' => 'mumble' ])
        ];

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])->willReturn($items);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => $items
            ]);

        $response = $this->service->getListByIds([ 1, 2 ]);

        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertEquals($items, $response['items']);
        $this->assertEquals(2, $response['total']);
    }

    /**
     * Tests getListByIds when the list of ids is empty.
     */
    public function testGetListByIdsWhenEmptyIds()
    {
        $this->assertEquals(
            [ 'items' => [], 'total' => 0 ],
            $this->service->getListByIds([])
        );
    }

    /**
     * Tests getListByIds when invalid list of ids provided.
     *
     * @expectedException \Api\Exception\GetListException
     */
    public function testGetListByIdsWhenInvalidIds()
    {
        $this->service->getListByIds(null);
    }

    /**
     * Tests getL10nKeys.
     */
    public function testGetL10nKeys()
    {
        $this->metadata->expects($this->once())->method('getL10nKeys')
            ->willReturn([ 'quux', 'frog' ]);

        $this->assertEquals([ 'quux', 'frog' ], $this->service->getL10nKeys());
    }

    /**
     * Tests patchItem when no error.
     */
    public function testPatchItem()
    {
        $item = new Entity([ 'name' => 'foobar' ]);
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getItem', [ 'id' => 1, 'item' => $item ]);
        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('entity.patchItem', [
                'action' => 'Api\Service\V1\OrmService::patchItem',
                'id'     => 1,
                'item'   => $item,
                'last_changed' => ''
            ]);

        $this->service->patchItem(1, $data);

        $this->assertEquals('mumble', $item->name);
    }

    /**
     * Tests patchItem when there is an error while searching.
     *
     * @expectedException \Api\Exception\PatchItemException
     */
    public function testPatchItemWhenErrorWhileSearching()
    {
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->will($this->throwException(new \Exception()));

        $this->service->patchItem(1, $data);
    }

    /**
     * Tests patchItem when there is an error while persisting.
     *
     * @expectedException \Api\Exception\PatchItemException
     */
    public function testPatchItemWhenErrorWhilePersisting()
    {
        $item = new Entity([ 'name' => 'foobar' ]);
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception));

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.getItem', [ 'id' => 1, 'item' => $item ]);

        $this->service->patchItem(1, $data);
    }

    /**
     * Tests patchList when no error.
     */
    public function testPatchList()
    {
        $itemA = new Entity([ 'name' => 'wubble', 'enabled' => false ]);
        $itemB = new Entity([ 'name' => 'xyzzy', 'enabled' => false  ]);
        $data  = [ 'enabled' => true ];

        $this->metadata->expects($this->at(0))->method('getL10nKeys')
            ->willReturn([]);
        $this->metadata->expects($this->at(2))->method('getId')
            ->with($itemA)->willReturn([ 'id' => 1 ]);
        $this->metadata->expects($this->at(3))->method('getId')
            ->with($itemB)->willReturn([ 'id' => 2 ]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);
        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->em->expects($this->exactly(2))->method('persist');

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('entity.patchList', [
                'action' => 'Api\Service\V1\OrmService::patchList',
                'ids'    => [ 1, 2 ],
                'item'   => [ $itemA, $itemB ],
                'last_changed' => []
            ]);

        $this->assertEquals(2, $this->service->patchList([ 1, 2 ], $data));
        $this->assertTrue($itemA->enabled);
        $this->assertTrue($itemB->enabled);
    }

    /**
     * Tests patchList when invalid list of ids provided.
     *
     * @expectedException \Api\Exception\PatchListException
     */
    public function testPatchListWhenInvalidIds()
    {
        $this->service->patchList('xyzzy', []);
    }

    /**
     * Tests patchList when one error happens while updating.
     *
     * @expectedException \Api\Exception\PatchListException
     */
    public function testPatchListWhenOneErrorWhileUpdating()
    {
        $itemA = new Entity([ 'name' => 'wubble' ]);
        $itemB = new Entity([ 'name' => 'xyzzy' ]);
        $data  = [ 'enabled' => true ];

        $this->metadata->expects($this->at(0))->method('getL10nKeys')
            ->willReturn([]);

        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->willReturn([ $itemA, $itemB ]);

        $this->em->expects($this->at(4))->method('persist')
            ->will($this->throwException(new \Exception()));

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getListByIds', [
                'ids'   => [ 1, 2 ],
                'items' => [ $itemA, $itemB ]
            ]);

        $this->assertEquals(1, $this->service->patchList([ 1, 2 ], $data));
    }

    /**
     * Tests patchList when an error happens while searching.
     *
     * @expectedException \Api\Exception\PatchListException
     */
    public function testPatchListWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('find')
            ->with([ 1, 2 ])
            ->will($this->throwException(new \Exception()));

        $this->service->patchList([ 1, 2 ], [ 'enabled' => true ]);
    }

    /**
     * Tests responsify.
     */
    public function testResponsify()
    {
        $this->converter->expects($this->once())->method('responsify');

        $this->service->responsify('foo');
    }

    /**
     * Tests setCount.
     */
    public function testSetCount()
    {
        $property = new \ReflectionProperty($this->service, 'count');
        $property->setAccessible(true);

        $this->assertEquals($this->service, $this->service->setCount(true));
        $this->assertTrue($property->getValue($this->service));

        $this->service->setCount(false);
        $this->assertFalse($property->getValue($this->service));
    }

    /**
     * Tests setOrigin.
     */
    public function testSetOrigin()
    {
        $this->assertEquals('instance', $this->service->getOrigin());
        $this->assertEquals($this->service, $this->service->setOrigin('wobble'));
        $this->assertEquals('wobble', $this->service->getOrigin());
    }

    /**
     * Tests updateItem when no error.
     */
    public function testUpdateItem()
    {
        $item        = new Entity([ 'name' => 'foobar' ]);
        $itemOldData = clone $item;
        $data        = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->dispatcher->expects($this->at(0))->method('dispatch')
            ->with('entity.getItem', [
                'id'   => 1,
                'item' => $item
            ]);

        $this->dispatcher->expects($this->at(1))->method('dispatch')
            ->with('entity.updateItem', [
                'action'        => 'Api\Service\V1\OrmService::updateItem',
                'id'            => 1,
                'item'          => $item,
                'item_old_data' => $itemOldData,
                'last_changed'  => ''
            ]);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
    }

    /**
     * Tests updateItem when there is an error while searching.
     *
     * @expectedException \Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWhileSearching()
    {
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->will($this->throwException(new \Exception()));

        $this->service->updateItem(1, $data);
    }

    /**
     * Tests updateItem when there is an error while persisting.
     *
     * @expectedException \Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWhilePersisting()
    {
        $item = new Entity([ 'name' => 'foobar' ]);
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception));

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('entity.getItem', [
                'id'   => 1,
                'item' => $item
            ]);

        $this->service->updateItem(1, $data);
    }

    /**
     * Tests getOqlForIds
     */
    public function testGetOqlForIds()
    {
        $method = new \ReflectionMethod($this->service, 'getOqlForIds');
        $method->setAccessible(true);

        $this->assertEquals(
            'id in [1,3,5]',
            $method->invokeArgs($this->service, [ [ 1, 3, 5 ] ])
        );
    }

    /**
     * Tests getOqlForList.
     */
    public function testGetOqlForList()
    {
        $oql = 'glork = "gorp"';

        $method = new \ReflectionMethod($this->service, 'getOqlForList');
        $method->setAccessible(true);

        $this->assertEquals($oql, $method->invokeArgs($this->service, [ $oql ]));
    }

    /**
     * Tests validate.
     */
    public function testValidate()
    {
        $method = new \ReflectionMethod($this->service, 'validate');
        $method->setAccessible(true);

        $this->validator->expects($this->once())->method('validate');

        $method->invokeArgs($this->service, [ new Entity() ]);
    }

    /**
     * Tests validate when no validator
     */
    public function testValidateWhenNoValidator()
    {
        $service = new OrmService($this->container, 'Opennemas\Orm\Core\Entity');

        $method = new \ReflectionMethod($service, 'validate');
        $method->setAccessible(true);

        $method->invokeArgs($service, [ new Entity() ]);

        $this->addToAssertionCount(1);
    }

    /**
     * Tests localizeItem when the entity has no l10n_string values.
     */
    public function testLocalizeItemWhenNoL10nStringValues()
    {
        $item = new Entity([ 'name' => 'foobar' ]);

        $this->metadata->expects($this->once())->method('getL10nKeys')
            ->willReturn([]);

        $method = new \ReflectionMethod($this->service, 'localizeItem');
        $method->setAccessible(true);

        $this->assertEquals($item, $method->invokeArgs($this->service, [ $item ]));
    }

    /**
     * Tests localizeItem when keys provided and context is frontend.
     */
    public function testLocalizeItem()
    {
        $item = new Entity([ 'name' => [ 'en' => 'foobar', 'es' => 'fubar' ] ]);

        $this->metadata->expects($this->any())->method('getL10nKeys')
            ->willReturn([ 'title', 'name' ]);

        $this->fm->expects($this->once())->method('set')
            ->with([ 'en' => 'foobar', 'es' => 'fubar' ])
            ->willReturn($this->fm);
        $this->fm->expects($this->once())->method('filter')
            ->with('localize')->willReturn($this->fm);
        $this->fm->expects($this->once())->method('get')
            ->willReturn('fubar');

        $method = new \ReflectionMethod($this->service, 'localizeItem');
        $method->setAccessible(true);

        $method->invokeArgs($this->service, [ $item ]);

        $this->assertEquals('fubar', $item->name);
    }
}
