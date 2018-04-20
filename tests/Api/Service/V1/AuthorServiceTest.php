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

use Api\Service\V1\AuthorService;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for AuthorService class.
 */
class AuthorServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer' . uniqid())
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([
                'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->fixer = $this->getMockBuilder('Fixer' . uniqid())
            ->setMethods([ 'addCondition', 'fix', 'getOql' ])
            ->getMock();

        $this->metadata = $this->getMockBuilder('Metadata' . uniqid())
            ->setMethods([ 'getIdKeys' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger' . uniqid())
            ->setMethods([ 'error' ])
            ->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([ 'countBy', 'findBy', 'findOneBy'])
            ->getMock();

        $this->user = new Entity([
            'email'    => 'flob@garply.com',
            'id'       => 1,
            'name'     => 'flob',
            'password' => 'quux',
            'type'     => 1
        ]);

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new AuthorService($this->container, 'Common\ORM\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.user':
                return $this->user;

            case 'error.log':
                return $this->logger;

            case 'orm.manager':
                return $this->em;

            case 'orm.oql.fixer':
                return $this->fixer;
        }
    }

    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $item = new Entity([
            'fk_user_group' => [ 3 ],
            'user_groups'   => [ 3 => [ 'status' => 1 ] ]
        ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when the item to delete is the current user.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenEqualsToCurrentUser()
    {
        $this->service->deleteItem(1);
    }

    /**
     * Tests deleteItem when no item found.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenNoEntity()
    {
        $this->repository->expects($this->any())->method('findOneBy')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->exactly(2))->method('error');

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteItem when an error happens while removing object.
     *
     * @expectedException Api\Exception\DeleteItemException
     */
    public function testDeleteItemWhenErrorWhileRemoving()
    {
        $item = new Entity([
            'fk_user_group' => [ 3 ],
            'user_groups'   => [ 3 => [ 'status' => 1 ] ]
        ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item)->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->deleteItem(23);
    }

    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity([ 'type' => 2 ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and user_group_id = 3')->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the item has no author property to true.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoAuthor()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and user_group_id = 3')
            ->will($this->throwException(new \Exception()));
        $this->logger->expects($this->once())->method('error');

        $this->service->getItem(1);
    }

    /*
     * Tests getOqlForList.
     */
    public function testGetOqlForList()
    {
        $method = new \ReflectionMethod($this->service, 'getOqlForList');
        $method->setAccessible(true);

        $this->fixer->expects($this->once())->method('fix')
            ->willReturn($this->fixer);
        $this->fixer->expects($this->once())->method('addCondition')
            ->with('user_group_id = 3')->willReturn($this->fixer);
        $this->fixer->expects($this->once())->method('getOql');

        $method->invokeArgs($this->service, [ [ 1, 3, 5 ] ]);
    }
}
