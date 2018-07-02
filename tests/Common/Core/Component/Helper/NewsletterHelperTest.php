<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests\Common\Core\Component\Helper;

use Common\Core\Component\Helper\NewsletterHelper;
use Common\ORM\Entity\User;
use Common\ORM\Entity\UserGroup;

/**
 * Defines test cases for class class.
 */
class NewsletterHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->settingsManager = $this->getMockBuilder('SettingsManager')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->helper = new NewsletterHelper($this->settingsManager);
    }

    /**
     * Tests helper constructor.
     */
    public function testConstructor()
    {
        $this->assertAttributeEquals($this->settingsManager, 'sm', $this->helper);
    }

    /**
     * Tests getSubscriptionType.
     */
    public function testGetSubscriptionType()
    {
        $this->settingsManager->expects($this->once())->method('get')
            ->with('newsletter_subscriptionType')
            ->willReturn('list');

        $this->assertEquals('list', $this->helper->getSubscriptionType());
    }
}
