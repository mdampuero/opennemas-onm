<?php


namespace Tests\Api\EventSubscriber;

use Api\EventSubscriber\PollSubscriber;
use Common\Model\Entity\Content;
use Common\Model\Entity\Instance;

/**
 * Defines test cases for PollSubscriber class.
 */

class PollSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'hasParameter' ])
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->helper = $this->getMockBuilder('Api\Helper\Cache\ContentCacheHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteItem' ])
            ->getMock();

        $this->instance = new Instance(['internal_name' => 'grault']);

        $this->template = $this->getMockBuilder('Opennemas\Cache\Core\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'delete' ])
            ->getMock();

        $this->tq = $this->getMockBuilder('Opennemas\Task\Component\Queue\Queue')
            ->setMethods([ 'push' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->subscriber = new PollSubscriber($this->container, $this->helper, $this->template);
    }

    /**
     * Callback function to return custom service based on the name.
     */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'core.instance':
                return $this->instance;

            case 'task.service.queue':
                return $this->tq;
        }

        return null;
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray($this->subscriber->getSubscribedEvents());
    }

    /**
     * Tests logAction when there is no action.
     */
    public function testLogActionWhenNoAction()
    {
        $this->event->expects($this->once())->method('hasArgument')
            ->with('action')
            ->willReturn(false);

        $this->assertEmpty($this->subscriber->logAction($this->event));
    }

    /**
     * Tests logAction when empty items.
     */
    public function testLogActionWhenEmptyItems()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('action')
            ->willReturn(true);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('action')
            ->willReturn('updateItem');

        $this->event->expects($this->at(2))->method('getArgument')
            ->with('item')
            ->willReturn([]);

        $this->subscriber->logAction($this->event);
    }

    /**
     * Tests removeSmartyCacheForContent when no argument
     */
    public function testRemoveSmartyCacheForContentWhenNoArgument()
    {
        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(false);

        $this->subscriber->removeSmartyCacheForContent($this->event);
    }

    /**
     * Tests removeSmartyCacheForContent when argument
     */
    public function testRemoveSmartyCacheForContentWhenArgument()
    {
        $item = new Content(
            [
                'pk_content' => 1,
                'content_type_name' => 'poll',
                'category_id' => 1
            ]
        );

        $this->event->expects($this->once())->method('hasArgument')
            ->with('item')->willReturn(true);

        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($item);

        $this->template->expects($this->at(0))->method('delete')
            ->with('content', $item->pk_content)->willReturn($this->template);

        $this->template->expects($this->at(1))->method('delete')
            ->with('archive', date('Ymd'))->willReturn($this->template);

        $this->template->expects($this->at(2))->method('delete')
            ->with('rss', $item->content_type_name)->willReturn($this->template);

        $this->template->expects($this->at(3))->method('delete')
            ->with('frontpage', $item->content_type_name)->willReturn($this->template);

        $this->template->expects($this->at(4))->method('delete')
            ->with('category', 'list', $item->category_id)->willReturn($this->template);

        $this->template->expects($this->at(5))->method('delete')
            ->with($item->content_type_name, 'frontpage')->willReturn($this->template);

        $this->template->expects($this->at(6))->method('delete')
            ->with($item->content_type_name, 'list')->willReturn($this->template);

        $this->template->expects($this->at(7))->method('delete')
            ->with('sitemap', 'contents')->willReturn($this->template);

        $this->subscriber->removeSmartyCacheForContent($this->event);
    }

    /**
     * Tests removeVarnishCacheCurrentInstance when no varnish param
     */
    public function testRemoveVarnishCacheCurrentInstanceWhenNoVarnish()
    {
        $this->container->expects($this->once())->method('hasParameter')
            ->with('varnish')->willReturn(false);

        $this->subscriber->removeVarnishCacheCurrentInstance($this->event);
    }

    /**
     * Tests removeVarnishCacheCurrentInstance when varnish param
     */
    public function testRemoveVarnishCacheCurrentInstanceWhenVarnish()
    {
        $poll = new Content([ 'pk_content' => 1 ]);

        $this->container->expects($this->once())->method('hasParameter')
            ->with('varnish')->willReturn(true);

        $this->event->expects($this->once())->method('getArgument')
            ->with('item')->willReturn($poll);

        $this->subscriber->removeVarnishCacheCurrentInstance($this->event);
    }
}
