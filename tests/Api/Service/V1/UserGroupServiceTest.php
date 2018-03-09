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

use Api\Service\V1\UserGroupService;
use Common\ORM\Entity\UserGroup;

/**
 * Defines test cases for UserGroupService class.
 */
class UserGroupServiceTest extends \PHPUnit_Framework_TestCase
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

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([ 'getConverter' ,'getRepository', 'persist', 'remove' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger' . uniqid())
            ->setMethods([ 'error' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'find', 'findBy'])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = new UserGroupService($this->container);
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'error.log':
                return $this->logger;

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

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
    }

    /**
     * Tests createItem when an error happens while converting data.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateWhenErrorWhileConverting()
    {
        $this->converter->expects($this->any())->method('objectify')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->createItem([]);
    }

    /**
     * Tests createItem when an error happens while persisting object.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateWhenErrorWhilePersisting()
    {
        $this->converter->expects($this->any())->method('objectify');
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->createItem([]);
    }

    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $userGroup = new UserGroup();

        $this->repository->expects($this->once())->method('find')
            ->willReturn($userGroup);
        $this->em->expects($this->once())->method('remove')
            ->with($userGroup);

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when no user group found.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenNoUserGroup()
    {
        $this->repository->expects($this->any())->method('find')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when an error happens while removing object.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenErrorWhileRemoving()
    {
        $userGroup = new UserGroup();

        $this->repository->expects($this->once())->method('find')
            ->willReturn($userGroup);
        $this->em->expects($this->once())->method('remove')
            ->with($userGroup)->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteList when no error.
     */
    public function testDeleteList()
    {
        $userGroupA = new UserGroup([ 'name' => 'wubble']);
        $userGroupB = new UserGroup([ 'name' => 'xyzzy' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1,2]')
            ->willReturn([ $userGroupA, $userGroupB ]);
        $this->em->expects($this->exactly(2))->method('remove');

        $this->assertEquals(2, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when invalid list of ids provided.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenInvalidIds()
    {
        $this->service->deleteList('xyzzy');
    }

    /**
     * Tests deleteList when one error happens while removing.
     */
    public function testDeleteListWhenOneErrorWhileRemoving()
    {
        $userGroupA = new UserGroup([ 'name' => 'wubble']);
        $userGroupB = new UserGroup([ 'name' => 'xyzzy' ]);

        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1,2]')
            ->willReturn([ $userGroupA, $userGroupB ]);
        $this->em->expects($this->at(1))->method('remove');
        $this->em->expects($this->at(2))->method('remove')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->assertEquals(1, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when an error happens while searching.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1,2]')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->deleteList([ 1, 2 ]);
    }

    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $userGroup = new UserGroup();

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($userGroup);

        $this->assertEquals($userGroup, $this->service->getItem(1));
    }

    /**
     * Tests getItem when an error happens while converting data.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('find')
            ->with(1)->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->getItem(1);
    }

    /**
     * Tests getList when no error.
     */
    public function testGetList()
    {
        $results = [
            new UserGroup([ 'name' => 'wubble' ]),
            new UserGroup([ 'name' => 'mumble' ])
        ];

        $this->repository->expects($this->once())->method('countBy')
            ->with('order by title asc')->willReturn(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with('order by title asc')->willReturn($results);

        $response = $this->service->getList('order by title asc');

        $this->assertArrayHasKey('results', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertEquals($results, $response['results']);
        $this->assertEquals(2, $response['total']);
    }

    /**
     * Tests getList when there is an error while counting contents.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListWhenErrorWhileCounting()
    {
        $this->repository->expects($this->once())->method('countBy')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->getList('order by title asc');
    }

    /**
     * Tests getList when there is an error while searching contents.
     *
     * @expectedException Api\Exception\GetListException
     */
    public function testGetListWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('countBy')
            ->willReturn(2);
        $this->repository->expects($this->once())->method('findBy')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->getList('order by title asc');
    }

    /**
     * Tests patchItem when no error.
     */
    public function testPatchItem()
    {
        $userGroup = new UserGroup([ 'name' => 'foobar' ]);
        $data      = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($userGroup);
        $this->em->expects($this->once())->method('persist')
            ->with($userGroup);

        $this->service->patchItem(1, $data);

        $this->assertEquals('mumble', $userGroup->name);
    }

    /**
     * Tests patchItem when there is an error while searching.
     *
     * @expectedException Api\Exception\PatchItemException
     */
    public function testPatchItemWhenErrorWhileSearching()
    {
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->patchItem(1, $data);
    }

    /**
     * Tests patchItem when there is an error while persisting.
     *
     * @expectedException Api\Exception\PatchItemException
     */
    public function testPatchItemWhenErrorWhilePersisting()
    {
        $userGroup = new UserGroup([ 'name' => 'foobar' ]);
        $data      = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->willReturn($userGroup);
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception));
        $this->logger->expects($this->once())->method('error');

        $this->service->patchItem(1, $data);
    }

    /**
     * Tests patchList when no error.
     */
    public function testPatchList()
    {
        $userGroupA = new UserGroup([ 'name' => 'wubble', 'enabled' => false ]);
        $userGroupB = new UserGroup([ 'name' => 'xyzzy', 'enabled' => false  ]);

        $data = [ 'enabled' => true ];

        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1,2]')
            ->willReturn([ $userGroupA, $userGroupB ]);
        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->em->expects($this->exactly(2))->method('persist');

        $this->assertEquals(2, $this->service->patchList([ 1, 2 ], $data));
        $this->assertTrue($userGroupA->enabled);
        $this->assertTrue($userGroupB->enabled);
    }

    /**
     * Tests patchList when invalid list of ids provided.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchListWhenInvalidIds()
    {
        $this->service->patchList('xyzzy', []);
    }

    /**
     * Tests patchList when one error happens while removing.
     */
    public function testPatchListWhenOneErrorWhileRemoving()
    {
        $userGroupA = new UserGroup([ 'name' => 'wubble']);
        $userGroupB = new UserGroup([ 'name' => 'xyzzy' ]);

        $data = [ 'enabled' => true ];

        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1,2]')
            ->willReturn([ $userGroupA, $userGroupB ]);
        $this->em->expects($this->at(1))->method('persist');
        $this->em->expects($this->at(2))->method('persist')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->assertEquals(1, $this->service->patchList([ 1, 2 ], $data));
    }

    /**
     * Tests patchList when an error happens while searching.
     *
     * @expectedException Api\Exception\PatchListException
     */
    public function testPatchWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('findBy')
            ->with('pk_user_group in [1,2]')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->patchList([ 1, 2 ], [ 'enabled' => true ]);
    }

    /**
     * Tests updateItem when no error.
     */
    public function testUpdateItem()
    {
        $userGroup = new UserGroup([ 'name' => 'foobar' ]);
        $data      = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($userGroup);
        $this->em->expects($this->once())->method('persist')
            ->with($userGroup);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $userGroup->name);
    }

    /**
     * Tests updateItem when there is an error while searching.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWhileSearching()
    {
        $data = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->updateItem(1, $data);
    }

    /**
     * Tests updateItem when there is an error while persisting.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenErrorWhilePersisting()
    {
        $userGroup = new UserGroup([ 'name' => 'foobar' ]);
        $data      = [ 'name' => 'mumble' ];

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);
        $this->repository->expects($this->once())->method('find')
            ->willReturn($userGroup);
        $this->em->expects($this->once())->method('persist')
            ->will($this->throwException(new \Exception));
        $this->logger->expects($this->once())->method('error');

        $this->service->updateItem(1, $data);
    }
}
