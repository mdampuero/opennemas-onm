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
use Common\ORM\Core\Entity;

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

        $this->service = new UserGroupService($this->container, 'Common\ORM\Core\Entity');
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
        $data = [ 'name' => 'flob' ];

        $this->converter->expects($this->any())->method('objectify')
            ->with(array_merge([ 'type' => 0 ], $data))
            ->willReturn($data);
        $this->em->expects($this->once())->method('persist');

        $item = $this->service->createItem($data);

        $this->assertEquals('flob', $item->name);
    }

    /**
     * Tests getItem when no error.
     */
    public function testGetItem()
    {
        $item = new Entity([ 'type' => 0 ]);

        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the item has no user group property to true.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoUserGroup()
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
            ->willReturn('type = 0');

        $this->repository->expects($this->once())->method('countBy')
            ->with('type = 0')->willReturn(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with('type = 0')->willReturn($results);

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
        $item = new Entity([ 'name' => 'foobar', 'type' => 0 ]);
        $data = [ 'name' => 'mumble', 'type' => 1 ];

        $this->converter->expects($this->once())->method('objectify')
            ->with(array_diff($data, [ 'type' => 1 ]))
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
        $itemA = new Entity([ 'name' => 'wubble', 'enabled' => false ]);
        $itemB = new Entity([ 'name' => 'xyzzy', 'enabled' => false  ]);
        $data  = [ 'enabled' => true, 'type' => 0 ];

        $this->repository->expects($this->once())->method('findBy')
            ->willReturn([ $itemA, $itemB ]);
        $this->converter->expects($this->once())->method('objectify')
            ->with([ 'enabled' => true ])
            ->willReturn([ 'enabled' => true ]);
        $this->em->expects($this->exactly(2))->method('persist');

        $this->assertEquals(2, $this->service->patchList([ 1, 2 ], $data));
        $this->assertTrue($itemA->enabled);
        $this->assertTrue($itemB->enabled);
    }

    /**
     * Tests updateItem when no error.
     */
    public function testUpdateItem()
    {
        $item = new Entity([ 'name' => 'foobar', 'type' => 0 ]);
        $data = [ 'name' => 'mumble'];

        $this->converter->expects($this->once())->method('objectify')
            ->with(array_merge($data, [ 'type' => 0 ]))
            ->willReturn(array_merge($data, [ 'type' => 0 ]));
        $this->repository->expects($this->once())->method('find')
            ->with(1)->willReturn($item);
        $this->em->expects($this->once())->method('persist')
            ->with($item);

        $this->service->updateItem(1, $data);

        $this->assertEquals('mumble', $item->name);
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
