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

use Api\Service\V1\CategoryService;
use Common\Model\Entity\Category;

/**
 * Defines test cases for CategoryService class.
 */
class CategoryServiceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->dispatcher = $this->getMockBuilder('EventDispatcher')
            ->setMethods([ 'dispatch' ])
            ->getMock();

        $this->instance = $this->getMockBuilder('Instance')
            ->setMethods([ 'hasMultilanguage' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager' . uniqid())
            ->setMethods([ 'getRepository' ])->getMock();

        $this->repository = $this->getMockBuilder('Repository' . uniqid())
            ->setMethods([
                'countContents', 'moveContents', 'removeContents'
            ])->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->em->expects($this->any())->method('getRepository')
            ->willReturn($this->repository);

        $this->service = $this->getMockBuilder('Api\Service\V1\CategoryService')
            ->setMethods([ 'getItem', 'getItemBy', 'getListByIds' ])
            ->setConstructorArgs([ $this->container, 'Common\Model\Entity\Category' ])
            ->getMock();
    }

    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.dispatcher':
                return $this->dispatcher;

            case 'orm.manager':
                return $this->em;

            case 'core.instance':
                return $this->instance;
        }

        return null;
    }

    /**
     * Tests emptyItem when the item was not found.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testEmptyItemWhenItemNotFound()
    {
        $this->service->expects($this->once())->method('getItem')
            ->with(1)->will($this->throwException(new \Exception()));

        $this->service->emptyItem(1);
    }

    /**
     * Tests emptyItem when the item was not found.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testEmptyItemWhenItemIsEmpty()
    {
        $this->service->expects($this->once())->method('getItem')
            ->willReturn(new Category([ 'id' => 18752 ]));

        $this->repository->expects($this->once())->method('countContents')
            ->with(18752)->willReturn([]);

        $this->service->emptyItem(1);
    }

    /**
     * Tests emptyItem when the item was not found.
     */
    public function testEmptyItemWhenItemIsNotEmpty()
    {
        $category = new Category([ 'id' => 18752 ]);
        $contents = [ 'id' => 8883, 'type' => 'glorp' ];

        $this->service->expects($this->once())->method('getItem')
            ->willReturn($category);

        $this->repository->expects($this->at(0))->method('countContents')
            ->with(18752)->willReturn([ 18752 => 10 ]);
        $this->repository->expects($this->at(1))->method('removeContents')
            ->with(1);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('category.emptyItem', [
                'id'   => 1,
                'item' => $category,
            ]);

        $this->service->emptyItem(1);
    }

    /**
     * Tests emptyList when the list of ids is invalid.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testEmptyListWhenInvalidIds()
    {
        $this->service->emptyList('thud');
    }

    /**
     * Tests emptyList when an error happens while searching or emptying the
     * list.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testEmptyListWhenError()
    {
        $this->service->expects($this->once())->method('getListByIds')
            ->will($this->throwException(new \Exception()));

        $this->service->emptyList([ 1, 2 ]);
    }

    /**
     * Tests emptyList when the action is executed successfully.
     */
    public function testEmptyListWhenSuccess()
    {
        $items = [ new Category([ 'id' => 1 ]) ];

        $this->service->expects($this->once())->method('getListByIds')
            ->with([ 1, 2 ])->willReturn([
                'items' => $items,
                'total' => 1
            ]);

        $this->repository->expects($this->once())->method('removeContents')
            ->with([ 1 ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('category.emptyList', [
                'ids'   => [ 1, 2 ],
                'items' => $items,
            ]);

        $this->service->emptyList([ 1, 2 ]);
    }

    /**
     * Tests getItemBySlug.
     */
    public function testGetItemBySlug()
    {
        $this->instance->expects($this->once())->method('hasMultilanguage')
            ->willReturn(true);

        $this->service->expects($this->once())->method('getItemBy')
            ->with('name regexp "(.+\"|^)flob(\".+|$)"')
            ->willReturn(new Category([ 'name' => 'flob' ]));

        $item = $this->service->getItemBySlug('flob');

        $this->assertEquals('flob', $item->name);
    }

    /**
     * Tests getStats for an item.
     */
    public function testGetStatsForItem()
    {
        $this->repository->expects($this->once())->method('countContents')
            ->with([ 9230 ])->willReturn([ 9230 => 325 ]);

        $this->assertEquals(
            [ 9230 => 325 ],
            $this->service->getStats(new Category([ 'id' => 9230 ]))
        );
    }

    /**
     * Tests getStats for a list of items
     */
    public function testGetStatsForList()
    {
        $this->repository->expects($this->once())->method('countContents')
            ->with([ 9230 ])->willReturn([ 9230 => 325 ]);

        $this->assertEquals(
            [ 9230 => 325 ],
            $this->service->getStats([ new Category([ 'id' => 9230 ]) ])
        );
    }

    /**
     * Tests getStats when an error while counting.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testGetStatsWhenError()
    {
        $this->repository->expects($this->once())->method('countContents')
            ->with([ 9230 ])->will($this->throwException(new \Exception()));

        $this->service->getStats([ new Category([ 'id' => 9230 ]) ]);
    }

    /**
     * Tests getStats when no items provided.
     */
    public function testGetStatsWhenNoItems()
    {
        $this->assertEmpty($this->service->getStats(null));
    }

    /**
     * Tests emptyItem when the item was not found.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testMoveItemWhenItemNotFound()
    {
        $this->service->expects($this->once())->method('getItem')
            ->with(4937)->will($this->throwException(new \Exception()));

        $this->service->moveItem(4937, 26252);
    }

    /**
     * Tests emptyItem when the item was not found.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testMoveItemWhenItemIsEmpty()
    {
        $this->service->expects($this->once())->method('getItem')
            ->willReturn(new Category([ 'id' => 24036 ]));

        $this->repository->expects($this->once())->method('countContents')
            ->with(24036)->willReturn([]);

        $this->service->moveItem(24036, 6506);
    }

    /**
     * Tests emptyItem when the item was not found.
     */
    public function testMoveItemWhenItemIsNotEmpty()
    {
        $source   = new Category([ 'id' => 394 ]);
        $target   = new Category([ 'id' => 12119 ]);
        $contents = [ 'id' => 8883, 'type' => 'glorp' ];

        $this->service->expects($this->at(0))->method('getItem')
            ->with(394)->willReturn($source);
        $this->service->expects($this->at(1))->method('getItem')
            ->with(12119)->willReturn($target);

        $this->repository->expects($this->at(0))->method('countContents')
            ->with(394)->willReturn([ 394 => 10 ]);
        $this->repository->expects($this->at(1))->method('moveContents')
            ->with(394, 12119)->willReturn($contents);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('category.moveItem', [
                'id'       => 394,
                'item'     => $source,
                'target'   => $target,
                'contents' => $contents
            ]);

        $this->service->moveItem(394, 12119);
    }

    /**
     * Tests moveList when the list of ids is invalid.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testMoveListWhenInvalidIds()
    {
        $this->service->moveList('thud', 18028);
    }

    /**
     * Tests emptyList when the list of ids is invalid.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testMoveListWhenError()
    {
        $this->service->expects($this->once())->method('getListByIds')
            ->will($this->throwException(new \Exception()));

        $this->service->moveList([ 1, 2 ], 22933);
    }

    /**
     * Tests emptyMove when the action is executed successfully.
     */
    public function testMoveListWhenSuccess()
    {
        $items  = [ new Category([ 'id' => 1 ]) ];
        $target = new Category([ 'id' => 22933 ]);

        $this->service->expects($this->at(0))->method('getListByIds')
            ->with([ 1, 2 ])->willReturn([
                'items' => $items,
                'total' => 1
            ]);

        $this->service->expects($this->at(1))->method('getItem')
            ->with(22933)->willReturn($target);

        $this->repository->expects($this->once())->method('moveContents')
            ->with([ 1 ])->willReturn([ [ 'id' => 17427, 'type' => 'flob' ] ]);

        $this->dispatcher->expects($this->once())->method('dispatch')
            ->with('category.moveList', [
                'ids'      => [ 1, 2 ],
                'items'    => $items,
                'target'   => $target,
                'contents' => [ [ 'id' => 17427, 'type' => 'flob' ] ]
            ]);

        $this->service->moveList([ 1, 2 ], 22933);
    }

    /**
     * Tests isItemEmpty when an error is thrown while searching.
     *
     * @expectedException \Api\Exception\ApiException
     */
    public function testIsItemEmptyWhenError()
    {
        $method = new \ReflectionMethod($this->service, 'isItemEmpty');
        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('countContents')
            ->will($this->throwException(new \Exception()));

        $method->invokeArgs($this->service, [ new Category([ 'id' => 10267 ]) ]);
    }

    /**
     * Tests isItemEmpty when the item is empty.
     */
    public function testIsItemEmptyWhenEmpty()
    {
        $method = new \ReflectionMethod($this->service, 'isItemEmpty');
        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('countContents')
            ->willReturn([]);

        $this->assertTrue(
            $method->invokeArgs($this->service, [ new Category([ 'id' => 10267 ]) ])
        );
    }

    /**
     * Tests isItemEmpty when the item is not empty.
     */
    public function testIsItemEmptyWhenNotEmpty()
    {
        $method = new \ReflectionMethod($this->service, 'isItemEmpty');
        $method->setAccessible(true);

        $this->repository->expects($this->once())->method('countContents')
            ->willReturn([ 10267 => 9223 ]);

        $this->assertFalse(
            $method->invokeArgs($this->service, [ new Category([ 'id' => 10267 ]) ])
        );
    }
}
