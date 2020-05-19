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

use Api\EventSubscriber\SettingSubscriber;

class SettingSubscriberTest extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->instance = $this->getMockBuilder('Common\Model\Entity\Instance')
            ->disableOriginalConstructor()
            ->getMock();

        $this->tpl = $this->getMockBuilder('Common\Core\Component\Template\Cache\CacheManager')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteAll' ])
            ->getMock();

        $this->vh = $this->getMockBuilder('Common\Core\Component\Helper\VarnishHelper')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteInstance' ])
            ->getMock();

        $this->dcs = $this->getMockBuilder('Framework\Component\Assetic\DynamicCssService')
            ->disableOriginalConstructor()
            ->setMethods([ 'deleteTimestamp' ])
            ->getMock();

        $this->subscriber = new SettingSubscriber(
            $this->instance,
            $this->tpl,
            $this->vh,
            $this->dcs
        );
    }

    /**
     * Tests getSubscribedEvents action
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray($this->subscriber->getSubscribedEvents());
    }

    /**
     * Tests onSettingUpdate action
     */
    public function testOnSettingUpdate()
    {
        $this->tpl->expects($this->once())
            ->method('deleteAll');

        $this->dcs->expects($this->once())
            ->method('deleteTimestamp')
            ->with('%global%');

        $this->vh->expects($this->once())
            ->method('deleteInstance')
            ->with($this->instance);

        $this->subscriber->onSettingUpdate();
    }
}
