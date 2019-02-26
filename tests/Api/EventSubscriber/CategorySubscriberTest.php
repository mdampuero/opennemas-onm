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

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->th = $this->getMockBuilder('Common\Core\Component\Helper\TemplateCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteCategories', 'deleteDynamicCss' ])
            ->getMock();

        $this->vh = $this->getMockBuilder('Common\Core\Component\Helper\VarnishHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteInstance' ])
            ->getMock();

        $this->subscriber = new CategorySubscriber($this->instance, $this->th, $this->vh);
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
     * Tests onCategoryUpdate when only a category was updated.
     */
    public function testOnCategoryUpdateForCategory()
    {
        $category = new Category([ 'id' => 3750 ]);

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')->willReturn($category);
        $this->event->expects($this->at(2))->method('hasArgument')
            ->with('items')->willReturn(false);

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
        $this->event->expects($this->at(1))->method('hasArgument')
            ->with('items')->willReturn(true);
        $this->event->expects($this->at(2))->method('getArgument')
            ->with('items')->willReturn($categories);

        $this->th->expects($this->once())->method('deleteDynamicCss');
        $this->th->expects($this->once())->method('deleteCategories')
            ->with($categories);

        $this->vh->expects($this->once())->method('deleteInstance')
            ->with($this->instance);

        $this->subscriber->onCategoryUpdate($this->event);
    }
}
