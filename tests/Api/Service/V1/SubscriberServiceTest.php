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

use Api\Service\V1\SubscriberService;
use Common\ORM\Core\Entity;

/**
 * Defines test cases for SubscriberService class.
 */
class SubscriberServiceTest extends \PHPUnit_Framework_TestCase
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
            ->setMethods([ 'countBy', 'find', 'findBy'])
            ->getMock();

        $this->security = $this->getMockBuilder('Security' . uniqid())
            ->setMethods([ 'hasPermission' ])
            ->getMock();

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
        $this->security->expects($this->any())->method('hasPermission')
            ->willReturn(false);
        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new SubscriberService($this->container, 'Common\ORM\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.security':
                return $this->security;

            case 'error.log':
                return $this->logger;

            case 'orm.manager':
                return $this->em;

            case 'orm.oql.fixer':
                return $this->fixer;
        }
    }

    /**
     * Tests createItem when no error.
     */
    public function testCreateItem()
    {
        $data = [ 'name' => 'flob', 'email' => 'flob@garply.com' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with(array_merge([
                'activated' => false,
                'type'      => 1,
                'username'  => 'flob@garply.com'
            ], $data))->willReturn($data);
        $this->em->expects($this->once())->method('persist');

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
    }

    /**
     * Tests createItem when no email provided.
     *
     * @expectedException Api\Exception\CreateItemException
     */
    public function testCreateItemWhenEmailInUse()
    {
        $data = [ 'name' => 'flob', 'email' => 'flob@garply.com' ];

        $this->fixer->expects($this->once())->method('fix')
            ->with('email = "flob@garply.com"');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('email = "flob@garply.com" and type = 1');
        $this->repository->expects($this->once())->method('findBy')
            ->with('email = "flob@garply.com" and type = 1')
            ->willReturn(new Entity([]));

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
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity([ 'type' => 1 ]);

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the item has no subscriber property to true.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoSubscriber()
    {
        $item = new Entity();

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);

        $this->service->getItem(1);
    }

    /**
     * Tests getList when no error.
     */
    public function testGetList()
    {
        $results = [
            new Entity([ 'name' => 'wubble' ]),
            new Entity([ 'name' => 'mumble' ])
        ];

        $this->fixer->expects($this->once())->method('fix');
        $this->fixer->expects($this->once())->method('addCondition');
        $this->fixer->expects($this->once())->method('getOql')
            ->willReturn('type = 1');

        $this->repository->expects($this->once())->method('countBy')
            ->with('type = 1')->willReturn(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with('type = 1')->willReturn($results);

        $response = $this->service->getList('order by title asc');

        $this->assertArrayHasKey('results', $response);
        $this->assertArrayHasKey('total', $response);
        $this->assertEquals($results, $response['results']);
        $this->assertEquals(2, $response['total']);
    }

    /**
     * Tests patchItem when no error for non master user.
     */
    public function testPatchItem()
    {
        $item = new Entity([ 'name' => 'foobar', 'type' => 1 ]);
        $data = [ 'name' => 'mumble', 'type' => 0 ];

        $this->converter->expects($this->once())->method('objectify')
            ->with(array_diff($data, [ 'type' => 0 ]))
            ->willReturn([ 'name' => 'mumble' ]);
        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->patchItem(1, $data);

        $this->assertEquals('mumble', $item->name);
    }

    /**
     * Tests patchList when no error.
     */
    public function testPatchList()
    {
        $itemA = new Entity([ 'name' => 'wubble', 'activated' => false ]);
        $itemB = new Entity([ 'name' => 'xyzzy', 'activated' => false  ]);
        $data  = [ 'activated' => true, 'type' => 1 ];

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([ $itemA, $itemB ]);
        $this->converter->expects($this->once())->method('objectify')
            ->with([ 'activated' => true ])
            ->willReturn([ 'activated' => true ]);
        $this->em->expects($this->exactly(2))->method('persist');

        $this->assertEquals(2, $this->service->patchList([ 1, 2 ], $data));
        $this->assertTrue($itemA->activated);
        $this->assertTrue($itemB->activated);
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

        $service = new SubscriberService($this->container, get_class($entity));

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

        $service = new SubscriberService($this->container, get_class($entity));

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
     * Tests updateItem when no error.
     */
    public function testUpdateItem()
    {
        $data = [ 'name' => 'mumble', 'email' => 'garply@glork.glorp' ];
        $item = new Entity([
            'name'  => 'foobar',
            'email' => 'garply@glork.glorp',
            'type'  => 1
        ]);

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([]);

        $this->converter->expects($this->once())->method('objectify')
            ->with(array_merge($data, [
                'type' => 1,
                'username' => 'garply@glork.glorp'
            ]))->willReturn(array_merge($data, [
                'type' => 1,
                'username' => 'garply@glork.glorp'
            ]));

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
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
            ->willReturn('id != "1" and email = "flob@garply.com" and type = 1');
        $this->repository->expects($this->once())->method('findBy')
            ->with('id != "1" and email = "flob@garply.com" and type = 1')
            ->willReturn([ new Entity([]) ]);

        $this->service->updateItem(1, $data);
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
