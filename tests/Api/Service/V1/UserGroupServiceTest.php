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
            ->setMethods([ 'objectify' ])
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
    public function testDeleteWhenOneErrorWhileRemoving()
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
     * @expectedException Api\Exception\getItemException
     */
    public function testGetItemWhenErrorWhileSearching()
    {
        $this->repository->expects($this->once())->method('find')
            ->with(1)->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->getItem(1);
    }
}
