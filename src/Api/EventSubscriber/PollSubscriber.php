<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\EventSubscriber;

use Common\Core\Component\Helper\VarnishHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PollSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'poll.vote' => [ [ 'onPollVote', 5 ] ]
        ];
    }

    /**
     * Initializes the PollSubscriber.
     *
     * @param VarnishHelper $vh The VarnishHelper service.
     */
    public function __construct(VarnishHelper $vh)
    {
        $this->vh = $vh;
    }

    /**
     * Removes polls from varnish when an poll is deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onPollVote(Event $event)
    {
        $poll = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $this->vh->deleteContents($poll);
    }
}
