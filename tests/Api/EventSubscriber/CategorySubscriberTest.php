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
use Common\ORM\Entity\Category;
use Common\ORM\Entity\Instance;

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
        $this->instance = new Instance([ 'internal_name' => 'flob' ]);

        $this->cache = $this->getMockBuilder('Common\Cache\Core\Cache')
            ->setMethods([
                'contains', 'delete', 'deleteByPattern', 'deleteMulti', 'fetch',
                'fetchMulti', 'remove', 'save', 'saveMulti'
            ])
            ->getMock();

        $this->cm = $this->getMockBuilder('Common\Cache\Core\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'getConnection' ])
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->oldCache = $this->getMockBuilder('Onm\Cache\AbstractCache')
            ->setMethods([
                'delete', 'doContains', 'doDelete', 'doFetch', 'doSave', 'getIds'
            ])
            ->getMock();

        $this->th = $this->getMockBuilder('Common\Core\Component\Helper\TemplateCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteCategories', 'deleteDynamicCss' ])
            ->getMock();

        $this->vh = $this->getMockBuilder('Common\Core\Component\Helper\VarnishHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteInstance' ])
            ->getMock();

        $this->cm->expects($this->any())->method('getConnection')
            ->with('instance')->willReturn($this->cache);

        $this->subscriber = new CategorySubscriber(
            $this->instance,
            $this->th,
            $this->vh,
            $this->oldCache,
            $this->cm
        );
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
        $this->th->expects($this->once())->method('deleteDynamicCss');

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

        $this->oldCache->expects($this->once())->method('delete')
            ->with([ 'content-29457', 'fubar-29457', 'content-28034', 'flob-28034' ]);
        $this->cache->expects($this->once())->method('remove')
            ->with([ 'content-29457', 'fubar-29457', 'content-28034', 'flob-28034' ]);

        $this->th->expects($this->once())->method('deleteCategories')
            ->with([ $source, $target ]);

        $this->vh->expects($this->once())->method('deleteInstance')
            ->with($this->instance);

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

        $this->oldCache->expects($this->once())->method('delete')
            ->with([ 'content-29457', 'fubar-29457', 'content-28034', 'flob-28034' ]);
        $this->cache->expects($this->once())->method('remove')
            ->with([ 'content-29457', 'fubar-29457', 'content-28034', 'flob-28034' ]);

        $this->th->expects($this->once())->method('deleteCategories')
            ->with(array_merge($source, [ $target ]));

        $this->vh->expects($this->once())->method('deleteInstance')
            ->with($this->instance);

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

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')->willReturn($category);

        $this->th->expects($this->once())->method('deleteDynamicCss');
        $this->th->expects($this->once())->method('deleteCategories')
            ->with([ $category ]);

        $this->vh->expects($this->once())->method('deleteInstance')
            ->with($this->instance);

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

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(false);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('items')->willReturn($categories);

        $this->th->expects($this->once())->method('deleteDynamicCss');
        $this->th->expects($this->once())->method('deleteCategories')
            ->with($categories);

        $this->vh->expects($this->once())->method('deleteInstance')
            ->with($this->instance);

        $this->subscriber->onCategoryUpdate($this->event);
    }

    /**
     * Tests onCategoryDelete.
     */
    public function testOnCategoryDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\CategorySubscriber')
            ->setConstructorArgs([
                $this->instance, $this->th, $this->vh, $this->oldCache, $this->cm
            ])
            ->setMethods([ 'onCategoryUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onCategoryUpdate');

        $subscriber->onCategoryDelete($this->event);
    }
}
