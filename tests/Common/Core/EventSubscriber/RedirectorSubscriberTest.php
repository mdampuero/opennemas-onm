<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\EventSubscriber;

use Common\Core\EventSubscriber\RedirectorSubscriber;
use Common\ORM\Entity\Instance;

/**
 * Defines test cases for RedirectorSubscriber class.
 */
class RedirectorSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->instance = new Instance([ 'internal_name' => 'mumble' ]);

        $this->cache = $this->getMockBuilder('Common\Cache\Core\Cache')
            ->setMethods([
                'contains', 'delete', 'deleteByPattern', 'deleteMulti',
                'fetch', 'fetchMulti', 'remove', 'removeByPattern', 'save',
                'saveMulti'
            ])
            ->getMock();

        $this->event = $this->getMockBuilder('Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->varnish = $this->getMockBuilder('Onm\Varnish\MessageExchanger')
            ->setMethods([ 'addBanMessage' ])
            ->getMock();

        $this->subscriber = new RedirectorSubscriber($this->cache, $this->instance, $this->varnish);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscriberEvents()
    {
        $events = RedirectorSubscriber::getSubscribedEvents();

        foreach ($events as $name => $handler) {
            $this->assertRegexp('/url\..*/', $name);
            $this->assertEquals('removeUrlsFromCache', $handler[0][0]);
            $this->assertEquals('removeUrlsFromVarnish', $handler[1][0]);
        }
    }

    /**
     * Tests revemoUrlsFromCache.
     */
    public function testRemoveUrlsFromCache()
    {
        $this->cache->expects($this->once())->method('removeByPattern')
            ->with('*mumble_redirector*');

        $this->subscriber->removeUrlsFromCache();
    }

    /**
     * Tests revemoUrlsFromVarnish when an id is passed as argument in the
     * event.
     */
    public function testRemoveUrlsFromVarnishForAnUrl()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('id')->willReturn(true);
        $this->event->expects($this->at(1))->method('getArgument')
            ->with('id')->willReturn(456);
        $this->event->expects($this->at(2))->method('hasArgument')
            ->with('ids')->willReturn(false);

        $this->varnish->expects($this->once())->method('addBanMessage')
            ->with('obj.http.x-tags ~ url-456');

        $this->subscriber->removeUrlsFromVarnish($this->event);
    }

    /**
     * Tests revemoUrlsFromVarnish when a list of ids is passed as argument in
     * the event.
     */
    public function testRemoveUrlsFromVarnishForMultipleUrls()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('id')->willReturn(false);
        $this->event->expects($this->at(1))->method('hasArgument')
            ->with('ids')->willReturn(true);
        $this->event->expects($this->at(2))->method('getArgument')
            ->with('ids')->willReturn([ 456, 234 ]);

        $this->varnish->expects($this->once())->method('addBanMessage')
            ->with('obj.http.x-tags ~ url-456|url-234');

        $this->subscriber->removeUrlsFromVarnish($this->event);
    }
}
