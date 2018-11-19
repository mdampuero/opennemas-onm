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

        $this->subscriber = new RedirectorSubscriber($this->cache, $this->instance);
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
}
