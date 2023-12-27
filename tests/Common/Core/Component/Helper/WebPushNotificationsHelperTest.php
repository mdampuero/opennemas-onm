<?php

namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\WebPushNotificationsHelper;
use Common\Model\Entity\WebPushNotifications;

/**
 * Defines test cases for ContentMediaHelper class.
 */
class WebPushNotificationsHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->webPushNotifications = new WebPushNotifications([
            'status'         => 0,
            'body'           => 'body',
            'title'          => 'title',
            'send_date'      => '2022-10-23 10:48:59',
            'image'          => null,
            'transaction_id' => 'abc123',
            'impressions'    => 0,
            'clicks'         => 0,
            'closed'         => 0,
        ]);

        $this->oql = 'send_date >="2000-01-01"'
        . ' order by send_date asc limit 1';

        $this->service = $this->getMockBuilder('Api\Service\V1\WebPushNotificationsService')
            ->disableOriginalConstructor()
            ->setMethods([ 'getItemBy' ])
            ->getMock();

        $this->container->expects($this->any())->method('get')
            ->will($this->returnCallback([ $this, 'serviceContainerCallback' ]));

        $this->helper = new WebPushNotificationsHelper($this->container);
    }

    /**
    * Returns a mocked service based on the service name.
    *
    * @param string $name The service name.
    *
    * @return mixed The mocked service.
    */
    public function serviceContainerCallback($name)
    {
        switch ($name) {
            case 'api.service.webpush_notifications':
                return $this->service;

            case 'core.instance':
                return $this->instance;

            default:
                return null;
        }
    }

    /**
    * Tests testGetFirstItemDate.
    */
    public function testGetFirstItemDate()
    {
        $this->service->expects($this->any())->method('getItemBy')
            ->with($this->oql)
            ->willReturn($this->webPushNotifications);

        $this->assertEquals(
            '2022-10-23 10:48:59',
            $this->helper->getFirstItemDate()
        );

        $this->service->expects($this->any())->method('getItemBy')
            ->with($this->oql)
            ->will($this->throwException(new \Exception()));

        $this->assertNull($this->helper->getFirstItemDate());
    }

    /**
    * Tests getCreateNotificationFromData.
    */
    public function testCreateNotificationFromData()
    {
        $data = [
            'status'         => 0,
            'body'           => 'body',
            'title'          => 'title',
            'send_date'      => '2022-10-23 10:48:59',
            'image'          => null,
            'transaction_id' => 'abc123',
            'impressions'    => 0,
            'clicks'         => 0,
            'closed'         => 0,
        ];

        $notification = $this->helper->createNotificationFromData($data);

        $expectedNotification = [
            'status'         => $data['status'],
            'body'           => $data['body'],
            'title'          => $data['title'],
            'send_date'      => $data['send_date'],
            'image'          => $data['image'],
            'transaction_id' => $data['transaction_id'],
            'impressions'    => $data['impressions'],
            'clicks'         => $data['clicks'],
            'closed'         => $data['closed'],
        ];

        $this->assertEquals($expectedNotification, $notification);
    }
}
