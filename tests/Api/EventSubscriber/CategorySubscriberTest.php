<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\CategorySubscriber;
use Common\Model\Entity\Category;

/**
 * Defines test cases for CategorySubscriber class.
 */
class CategorySubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->cache = $this->getMockBuilder('Cache' . uniqid())
            ->disableOriginalConstructor()
            ->setMethods([ 'remove' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Api\Helper\Cache\CategoryCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([
                'deleteContents', 'deleteDynamicCss', 'deleteInstance',
                'deleteItem'
            ])->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([$this, 'serviceContainerCallback']));

        $this->subscriber = new CategorySubscriber($this->container, $this->helper);
    }

    /**
     * Returns a mocked service basing on the service name.
     *
     * @param string $name The service name.
     *
     * @return mixed The mocked service.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'cache.connection.instance':
                return $this->cache;
        }

        return null;
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(CategorySubscriber::getSubscribedEvents());
    }

    /**
     * Tests onCategoryCreate.
     */
    public function testOnCategoryCreate()
    {
        $this->helper->expects($this->once())->method('deleteDynamicCss');

        $this->subscriber->onCategoryCreate();
    }

    /**
     * Tests onCategoryMove when contents from only one category were moved.
     */
    public function testOnCategoryMoveForCategory()
    {
        $source = new Category([ 'id' => 3750 ]);
        $target = new Category([ 'id' => 16396 ]);

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('contents')->willReturn(true);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('contents')->willReturn([
                [ 'id' => 29457, 'type' => 'fubar' ],
                [ 'id' => 28034, 'type' => 'flob' ]
            ]);
        $this->event->expects($this->at(2))->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->at(3))->method('getArgument')
            ->with('item')->willReturn($source);
        $this->event->expects($this->at(4))->method('getArgument')
            ->with('target')->willReturn($target);

        $this->helper->expects($this->at(0))->method('deleteItem')
            ->with($source);

        $this->helper->expects($this->at(1))->method('deleteContents')
            ->with([
                'content-29457', 'fubar-29457',  'content-28034', 'flob-28034'
            ])->willReturn($this->helper);
        $this->helper->expects($this->at(2))->method('deleteItem')
            ->with($target)->willReturn($this->helper);
        $this->helper->expects($this->at(3))->method('deleteInstance');

        $this->subscriber->onCategoryMove($this->event);
    }

    /**
     * Tests onCategoryMove when contents from a list of one categories were
     * moved.
     */
    public function testOnCategoryMoveForList()
    {
        $source = [
            new Category([ 'id' => 3750 ]),
            new Category([ 'id' => 28250 ])
        ];

        $target = new Category([ 'id' => 16396 ]);

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('contents')->willReturn(true);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('contents')->willReturn([
                [ 'id' => 29457, 'type' => 'fubar' ],
                [ 'id' => 28034, 'type' => 'flob' ]
            ]);
        $this->event->expects($this->at(2))->method('hasArgument')
            ->with('item')->willReturn(false);
        $this->event->expects($this->at(3))->method('getArgument')
            ->with('items')->willReturn($source);
        $this->event->expects($this->at(4))->method('getArgument')
            ->with('target')->willReturn($target);

        $this->helper->expects($this->at(2))->method('deleteContents')
            ->with([ 'content-29457', 'fubar-29457', 'content-28034', 'flob-28034' ])
            ->willReturn($this->helper);
        $this->helper->expects($this->at(3))->method('deleteItem')
            ->with($target)->willReturn($this->helper);
        $this->helper->expects($this->at(4))->method('deleteInstance');

        $this->subscriber->onCategoryMove($this->event);
    }

    /**
     * Tests onCategoryMove when no contents were moved.
     */
    public function testOnCategoryMoveWhenNoContents()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('contents')->willReturn(false);

        $this->subscriber->onCategoryMove($this->event);
    }

    /**
     * Tests onCategoryUpdate when only a category was updated.
     */
    public function testOnCategoryUpdateForCategory()
    {
        $category = new Category([ 'id' => 3750 ]);

        $this->event->expects($this->any())->method('getArgument')
            ->with('item')->willReturn($category);

        $this->helper->expects($this->at(0))->method('deleteItem')
            ->with($category);
        $this->helper->expects($this->at(1))->method('deleteDynamicCss')
            ->willReturn($this->helper);
        $this->helper->expects($this->at(2))->method('deleteInstance');

        $this->subscriber->onCategoryUpdate($this->event);
    }

    /**
     * Tests onCategoryUpdate when more than one categories were updated.
     */
    public function testOnCategoryUpdateForList()
    {
        $categories = [
            new Category([ 'id' => 3750 ]),
            new Category([ 'id' => 1086 ])
        ];

        $this->event->expects($this->any())->method('getArgument')
            ->with('item')->willReturn($categories);

        $this->helper->expects($this->at(0))->method('deleteItem')
            ->with($categories[0]);
        $this->helper->expects($this->at(1))->method('deleteItem')
            ->with($categories[1]);
        $this->helper->expects($this->at(2))->method('deleteDynamicCss')
            ->willReturn($this->helper);
        $this->helper->expects($this->at(3))->method('deleteInstance');

        $this->subscriber->onCategoryUpdate($this->event);
    }

    /**
     * Tests onCategoryDelete.
     */
    public function testOnCategoryDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\CategorySubscriber')
            ->setConstructorArgs([ $this->container, $this->helper ])
            ->setMethods([ 'onCategoryUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onCategoryUpdate');

        $subscriber->onCategoryDelete($this->event);
    }
}
