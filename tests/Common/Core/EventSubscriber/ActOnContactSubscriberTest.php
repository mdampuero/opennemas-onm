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

use Common\Core\EventSubscriber\ActOnContactSubscriber;
use Common\External\ActOn\Component\Exception\ActOnException;

/**
 * Defines test cases for ActOnSubscriber class.
 */
class ActOnContactSubscriberTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Configures the testing environment.
     */
    public function setUp()
    {
        $this->actOn = $this->getMockBuilder('Common\External\ActOn\Component\Factory\ActOnFactory')
            ->disableOriginalConstructor()
            ->setMethods([ 'getEndpoint' ])
            ->getMock();

        $this->container = $this->getMockBuilder('ServiceContainer')
            ->setMethods([ 'get', 'getParameter' ])
            ->getMock();

        $this->logger = $this->getMockBuilder('Logger')
            ->setMethods([ 'error' ])
            ->getMock();

        $this->ds = $this->getMockBuilder('DataSet')
            ->setMethods([ 'get' ])
            ->getMock();

        $this->em = $this->getMockBuilder('EntityManager')
            ->setMethods([ 'getDataSet' ])
            ->getMock();

        $this->ce = $this->getMockBuilder('\Common\External\ActOn\Component\Endpoint\ContactEndpoint')
            ->disableOriginalConstructor()
            ->setMethods([ 'existContact', 'addContact' ])
            ->getMock();

        $this->em->expects($this->any())->method('getDataSet')
            ->with('Settings', 'instance')
            ->willReturn($this->ds);

        $this->container->expects($this->any())->method('get')
            ->with('orm.manager')
            ->willReturn($this->em);

        $this->actOn->expects($this->any())->method('getEndpoint')
            ->with('contact')
            ->willReturn($this->ce);

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->setMethods([ 'getArgument', 'hasArgument' ])
            ->getMock();

        $this->subscriber = new ActOnContactSubscriber($this->container, $this->actOn, $this->logger);
    }

    /**
     * Tests getSubscribedEvents.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertIsArray(ActOnContactSubscriber::getSubscribedEvents());
    }

    /**
     * Tests addContact when no item.
     */
    public function testAddContactWhenNoItem()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(false);

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }

    /**
     * Tests addContact when no acton list.
     */
    public function testAddContactWhenNoList()
    {
        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')->willReturn(true);

        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn(null);

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }

    /**
     * Tests addContact when comment is not accepted.
     */
    public function testAddContactWhenCommentNotAccepted()
    {
        $comment         = new \stdClass();
        $comment->status = 'pending';

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')
            ->willReturn(true);

        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn([ 'acton_list' => 1 ]);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')
            ->willReturn($comment);

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }

    /**
     * Tests addContact when contact already exists.
     */
    public function testAddContactWhenContactExists()
    {
        $comment         = new \stdClass();
        $comment->status = 'accepted';

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')
            ->willReturn(true);

        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn([ 'acton_list' => 1 ]);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')
            ->willReturn($comment);

        $this->ce->expects($this->any())->method('existContact')
            ->with(1, 'foo@bar.baz')
            ->willReturn(true);

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }

    /**
     * Tests addContact when error checking contact.
     */
    public function testAddContactWhenCheckingContactError()
    {
        $comment         = new \stdClass();
        $comment->status = 'accepted';

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')
            ->willReturn(true);

        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn([ 'acton_list' => 1 ]);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')
            ->willReturn($comment);

        $this->ce->expects($this->any())->method('existContact')
            ->with(1, 'foo@bar.baz')
            ->willThrowException(new ActOnException());

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }

    /**
     * Tests addContact when error adding contact.
     */
    public function testAddContactWhenAddingContactError()
    {
        $comment               = new \stdClass();
        $comment->status       = 'accepted';
        $comment->author       = 'Thud';
        $comment->author_email = 'foo@bar.baz';

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')
            ->willReturn(true);

        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn([ 'acton_list' => 1 ]);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')
            ->willReturn($comment);

        $this->ce->expects($this->at(0))->method('existContact')
            ->with(1, $comment->author_email)
            ->willReturn(false);

        $this->ce->expects($this->at(1))->method('addContact')
            ->with(1, [ 'contact' => json_encode([
                'E-mail Address' => $comment->author_email,
                'First Name'     => $comment->author,
            ])])
            ->willThrowException(new ActOnException());

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }

    /**
     * Tests addContact when valid.
     */
    public function testAddContactWhenValid()
    {
        $comment               = new \stdClass();
        $comment->status       = 'accepted';
        $comment->author       = 'Thud';
        $comment->author_email = 'foo@bar.baz';

        $this->event->expects($this->at(0))->method('hasArgument')
            ->with('item')
            ->willReturn(true);

        $this->ds->expects($this->any())->method('get')
            ->with('comments_config')
            ->willReturn([ 'acton_list' => 1 ]);

        $this->event->expects($this->at(1))->method('getArgument')
            ->with('item')
            ->willReturn($comment);

        $this->ce->expects($this->at(0))->method('existContact')
            ->with(1, $comment->author_email)
            ->willReturn(false);

        $this->ce->expects($this->at(1))->method('addContact')
            ->with(1, [ 'contact' => json_encode([
                'E-mail Address' => $comment->author_email,
                'First Name'     => $comment->author,
            ])])
            ->willReturn(false);

        $this->assertEmpty($this->subscriber->addContact($this->event));
    }
}
