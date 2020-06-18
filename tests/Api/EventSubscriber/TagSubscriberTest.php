<?php

namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\TagSubscriber;
use Common\Model\Entity\Instance;
use Common\Model\Entity\Tag;
use Opennemas\Task\Component\Task\ServiceTask;

/**
 * Defines test cases for TagSubscriber class.
 */
class TagSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Api\Helper\Cache\TagCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteItem', 'deleteList' ])
            ->getMock();

        $this->subscriber = new TagSubscriber($this->helper);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(TagSubscriber::getSubscribedEvents());
    }

    /**
     * Tests onTagCreate.
     */
    public function testOnTagCreate()
    {
        $this->helper->expects($this->once())->method('deleteList');

        $this->subscriber->onTagCreate();
    }

    /**
     * Tests onTagUpdate when only a tag was updated.
     */
    public function testOnTagUpdateForTag()
    {
        $tag = new Tag([ 'id' => 3750 ]);

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(true);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')->willReturn($tag);

        $this->helper->expects($this->once())->method('deleteItem')->with($tag);
        $this->helper->expects($this->once())->method('deleteList');

        $this->subscriber->onTagUpdate($this->event);
    }

    /**
     * Tests onTagUpdate when more than one tags were updated.
     */
    public function testOnTagUpdateForList()
    {
        $tags = [
            new Tag([ 'id' => 3750 ]),
            new Tag([ 'id' => 1086 ])
        ];

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(false);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('items')->willReturn($tags);

        $this->helper->expects($this->at(0))->method('deleteItem')->with($tags[0]);
        $this->helper->expects($this->at(1))->method('deleteItem')->with($tags[1]);
        $this->helper->expects($this->at(2))->method('deleteList');

        $this->subscriber->onTagUpdate($this->event);
    }

    /**
     * Tests onTagDelete.
     */
    public function testOnTagDelete()
    {
        $subscriber = $this->getMockBuilder('Api\EventSubscriber\TagSubscriber')
            ->setConstructorArgs([ $this->helper ])
            ->setMethods([ 'onTagUpdate' ])
            ->getMock();

        $subscriber->expects($this->once())->method('onTagUpdate');

        $subscriber->onTagDelete($this->event);
    }
}
