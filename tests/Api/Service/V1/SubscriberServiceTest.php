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
            ->setMethods([ 'countBy', 'findBy', 'findOneBy'])
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
        $this->metadata->expects($this->any())->method('getIdKeys')
            ->willReturn([ 'id' ]);

        $this->service = new SubscriberService($this->container, 'Common\ORM\Core\Entity');
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
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
        $item = new Entity();

        $this->repository->expects($this->once())->method('findOneBy')
            ->willReturn($item);
        $this->em->expects($this->once())->method('remove')
            ->with($item);

        $this->service->deleteItem(23);
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
     * Tests deleteList when no error.
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
            ->with('id = 1 and type != 0')->willReturn($item);

        $this->assertEquals($item, $this->service->getItem(1));
    }

    /**
     * Tests getItem when the item has no subscriber property to true.
     *
     * @expectedException Api\Exception\GetItemException
     */
    public function testGetItemWhenErrorWhenNoSubscriber()
    {
        $this->repository->expects($this->once())->method('findOneBy')
            ->with('id = 1 and type != 0')
            ->will($this->throwException(new \Exception()));

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
            ->willReturn($this->fixer);
        $this->fixer->expects($this->once())->method('getOql');

        $method->invokeArgs($this->service, [ [ 1, 3, 5 ] ]);
    }
}
