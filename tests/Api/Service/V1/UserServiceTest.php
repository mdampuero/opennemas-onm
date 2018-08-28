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

use Api\Service\V1\UserService;
use Common\ORM\Core\Entity;
use Common\ORM\Core\Exception\EntityNotFoundException;

/**
 * Defines test cases for UserService class.
 */
class UserServiceTest extends \PHPUnit_Framework_TestCase
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
            ->setMethods([
                'getConverter' ,'getMetadata', 'getRepository', 'persist',
                'remove'
            ])->getMock();

        $this->encoder = $this->getMockBuilder('Encoder' . uniqid())
            ->setMethods([ 'encodePassword' ])
            ->getMock();

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
        $this->em->expects($this->any())->method('getConverter')
            ->willReturn($this->converter);
        $this->em->expects($this->any())->method('getMetadata')
            ->willReturn($this->metadata);
        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);
        $this->fixer->expects($this->any())->method('fix')
            ->willReturn($this->fixer);
        $this->fixer->expects($this->any())->method('addCondition')
            ->willReturn($this->fixer);
        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new UserService($this->container, 'Common\ORM\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.security.encoder.password':
                return $this->encoder;

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
     * Tests createItem when password is not provided and no error.
     */
    public function testCreateItemWhenPasswordNotProvided()
    {
        $data = [
            'email'    => 'flob@garply.com',
            'name'     => 'flob',
            'password' => null,
            'type'     => 1
        ];

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('email = "flob@garply.com"')
            ->will($this->throwException(new EntityNotFoundException('User')));
        $this->converter->expects($this->any())->method('objectify')
            ->with(array_diff_key($data, [ 'password' => null ]))
            ->willReturn(array_diff_key($data, [ 'password' => null ]));
        $this->em->expects($this->once())->method('persist');

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
        $this->assertEquals(null, $item->password);
    }

    /**
     * Tests createItem when password is provided and no error.
     */
    public function testCreateItemWhenPasswordProvided()
    {
        $data = [
            'email'    => 'flob@garply.com',
            'name'     => 'flob',
            'password' => 'quux',
            'type'     => 1
        ];

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('email = "flob@garply.com"')
            ->will($this->throwException(new EntityNotFoundException('User')));
        $this->encoder->expects($this->once())->method('encodePassword')
            ->with('quux')->willReturn('quux');
        $this->converter->expects($this->any())->method('objectify')
            ->with($data)->willReturn($data);
        $this->em->expects($this->once())->method('persist');

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
        $this->assertEquals('quux', $item->password);
    }

    /**
     * Tests createItem when no email provided.
     */
    public function testCreateItemWhenEmailInUseForSubscriber()
    {
        $data = [ 'name' => 'flob', 'email' => 'flob@garply.com' ];

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('email = "flob@garply.com"')
            ->willReturn(new Entity([
                'id'    => 1,
                'email' => 'flob@garply.com',
                'name'  => 'mumble',
                'type'  => 1
            ]));

        $this->em->expects($this->once())->method('persist');

        $this->service->createItem($data);
    }

    /**
     * Tests createItem when no email provided.
     *
     * @expectedException Api\Exception\CreateExistingItemException
     */
    public function testCreateItemWhenEmailInUseForSubscriberAndUser()
    {
        $data = [ 'name' => 'flob', 'email' => 'flob@garply.com' ];

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('email = "flob@garply.com"')
            ->willReturn(new Entity([
                'id'    => 1,
                'email' => 'flob@garply.com',
                'name'  => 'mumble',
                'type'  => 2
            ]));

        $this->service->createItem($data);
    }

    /**
     * Tests createItem when no email provided.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenNoEmail()
    {
        $data = [ 'name' => 'flob' ];

        $this->service->createItem($data);
    }

    /**
     * Tests deleteItem when no error.
     */
    public function testDeleteItem()
    {
        $item = new Entity();

        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn($item);
        $this->em->expects($this->once())->method('remove')
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
     * Tests deleteItem when no error.
     */
    public function testDeleteItemForSubscriberAndUser()
    {
        $item = new Entity([ 'type' => 2 ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->deleteItem(23);
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
        $item = new Entity();

        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn($item);
        $this->em->expects($this->once())->method('remove')
            ->with($item)->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->service->deleteItem(23);
    }

    /**
     * Tests deleteList when no error.
     */
    public function testDeleteList()
    {
        $itemA = new Entity([ 'name' => 'wubble']);
        $itemB = new Entity([ 'name' => 'xyzzy' ]);

        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type != 0 and id in [1,2]');

        $this->repository->expects($this->once())->method('findBy')
            ->with('type != 0 and id in [1,2]')
            ->willReturn([ $itemA, $itemB ]);
        $this->em->expects($this->exactly(2))->method('remove');

        $this->assertEquals(2, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when no error and an user is the current user.
     */
    public function testDeleteListWhenOneCurrentUser()
    {
        $item = new Entity([ 'name' => 'wubble', 'type' => 0 ]);

        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type != 0 and id in [1,2,3]');

        $this->repository->expects($this->once())->method('findBy')
            ->with('type != 0 and id in [1,2,3]')
            ->willReturn([ $this->user, $item ]);

        $this->em->expects($this->once())->method('remove')->with($item);

        $this->assertEquals(1, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when no error and an user is also a subscriber.
     */
    public function testDeleteListWhenOneSubscriberUser()
    {
        $itemA = new Entity([ 'name' => 'wubble', 'type' => 2 ]);
        $itemB = new Entity([ 'name' => 'xyzzy', 'type' => 0 ]);

        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type != 0 and id in [1,2]');

        $this->repository->expects($this->once())->method('findBy')
            ->with('type != 0 and id in [1,2]')
            ->willReturn([ $itemA, $itemB ]);

        $this->em->expects($this->once())->method('persist')->with($itemA);
        $this->em->expects($this->once())->method('remove')->with($itemB);

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
        $itemA = new Entity([ 'name' => 'wubble']);
        $itemB = new Entity([ 'name' => 'xyzzy' ]);

        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type != 0 and id in [1,2]');

        $this->repository->expects($this->once())->method('findBy')
            ->with('type != 0 and id in [1,2]')
            ->willReturn([ $itemA, $itemB ]);
        $this->em->expects($this->at(2))->method('remove');
        $this->em->expects($this->at(3))->method('remove')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->once())->method('error');

        $this->assertEquals(1, $this->service->deleteList([ 1, 2 ]));
    }

    /**
     * Tests deleteList when an error happens while searching.
     *
     * @expectedException Api\Exception\DeleteListException
     */
    public function testDeleteListWhenErrorWhileSearching()
    {
        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type != 0 and id in [1,2]');

        $this->repository->expects($this->once())->method('findBy')
            ->with('type != 0 and id in [1,2]')
            ->will($this->throwException(new \Exception()));

        $this->logger->expects($this->exactly(2))->method('error');

        $this->service->deleteList([ 1, 2 ]);
    }



    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity([ 'type' => 2 ]);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1')->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the item has no user property to true.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoUser()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1')
            ->will($this->throwException(new \Exception()));

        $this->service->getItem(1);
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

        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type != 1');

        $this->repository->expects($this->once())->method('countBy')
            ->with('type != 1')->willReturn(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with('type != 1')->willReturn($items);

        $response = $this->service->getList('order by title asc');

        $this->assertArrayHasKey('items', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertEquals($items, $response['items']);
        $this->assertEquals(2, $response['total']);
    }

    /**
     * Tests responsify with an item.
     */
    public function testResponsifyWithItem()
    {
        $entity = $this->getMockBuilder('Entity' . uniqid())
            ->setMethods([ 'eraseCredentials' ])
            ->getMock();

        $entity->expects($this->once())->method('eraseCredentials');

        $service = new UserService($this->container, get_class($entity));

        $service->responsify($entity);
    }

    /**
     * Tests responsify with a list of items.
     */
    public function testResponsifyWithList()
    {
        $entity = $this->getMockBuilder('Entity' . uniqid())
            ->setMethods([ 'eraseCredentials' ])
            ->getMock();

        $entity->expects($this->exactly(2))->method('eraseCredentials');

        $service = new UserService($this->container, get_class($entity));

        $service->responsify([ $entity, $entity ]);
    }

    /**
     * Tests responsify with a value that can not be responsified.
     */
    public function testResponsifyWithInvalidValues()
    {
        $this->assertEquals(null, $this->service->responsify(null));
        $this->assertEquals(1, $this->service->responsify(1));
        $this->assertEquals('glork', $this->service->responsify('glork'));
    }

    /**
     * Tests updateItem when password is not provided and no error.
     */
    public function testUpdateItemWhenPasswordNotProvided()
    {
        $data = [
            'email'    => 'garply@glork.glorp',
            'name'     => 'mumble',
            'password' => '',
            'type'     => 1
        ];

        $item = new Entity([
            'name'     => 'foobar',
            'email'    => 'garply@glork.glorp',
            'password' => 'wibblequxbar',
            'type'     => 1
        ]);

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([]);
        $this->converter->expects($this->any())->method('objectify')
            ->with(array_diff_key($data, [ 'password' => null ]))
            ->willReturn(array_diff_key($data, [ 'password' => null ]));

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1')->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
        $this->assertEquals('wibblequxbar', $item->password);
    }

    /**
     * Tests updateItem when password is provided and no error.
     */
    public function testUpdateItemWhenPasswordProvided()
    {
        $data = [
            'email'    => 'garply@glork.glorp',
            'name'     => 'mumble',
            'password' => 'quux',
            'type'     => 1
        ];

        $item = new Entity([
            'name'     => 'foobar',
            'email'    => 'garply@glork.glorp',
            'password' => 'wibblequxbar',
            'type'     => 1
        ]);

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([]);
        $this->encoder->expects($this->once())->method('encodePassword')
            ->with('quux')->willReturn('flobwubblexyzzy');
        $this->converter->expects($this->once())->method('objectify')
            ->with(array_merge($data, [
                'password' => 'flobwubblexyzzy'
            ]))->willReturn(array_merge($data, [
                'password' => 'flobwubblexyzzy'
            ]));

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1')->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
        $this->assertEquals('flobwubblexyzzy', $item->password);
    }

    /**
     * Tests updateItem when no email provided.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenEmailInUseForAnotherUser()
    {
        $data = [ 'name' => 'flob', 'email' => 'flob@garply.com' ];

        $this->fixer->expects($this->once())->method('fix')
            ->with('id != "1" and email = "flob@garply.com"');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('id != "1" and email = "flob@garply.com" and type != 1');
        $this->repository->expects($this->once())->method('findBy')
            ->with('id != "1" and email = "flob@garply.com" and type != 1')
            ->willReturn([ new Entity([]) ]);

        $this->service->updateItem(1, $data);
    }

    /**
     * Tests updateItem when a master user changes the type.
     */
    public function testUpdateItemWhenMasterChangesType()
    {
        $data = [
            'name'  => 'mumble',
            'email' => 'garply@glork.glorp',
            'type'  => 2
        ];
        $item = new Entity([
            'name'  => 'foobar',
            'email' => 'garply@glork.glorp',
            'type'  => 1
        ]);

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([]);

        $this->converter->expects($this->once())->method('objectify')
            ->with($data)->willReturn($data);

        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 1')->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
        $this->assertEquals(2, $item->type);
    }

    /**
     * Tests createItem when no email provided.
     *
     * @expectedException Api\Exception\UpdateItemException
     */
    public function testUpdateItemWhenNoEmail()
    {
        $data = [ 'name' => 'flob' ];

        $this->service->updateItem(1, $data);
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
            ->willReturn($this->fixer);
        $this->fixer->expects($this->once())->method('getOql');

        $method->invokeArgs($this->service, [ [ 1, 3, 5 ] ]);
    }
}
