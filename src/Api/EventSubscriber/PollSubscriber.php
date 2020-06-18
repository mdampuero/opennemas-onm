<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\ContentCacheHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PollSubscriber implements EventSubscriberInterface
{
    /**
     * The helper service.
     *
     * @var ContentCacheHelper
     */
    protected $helper;

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
     * @param ContentCacheHelper $helper The helper to remove caches for
     *                                   contents.
     */
    public function __construct(ContentCacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Removes polls from varnish when someone voted on a poll.
     *
     * @param Event $event The dispatched event.
     */
    public function onPollVote(Event $event)
    {
        $contents = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        foreach ($contents as $content) {
            $this->helper->deleteItem($content);
        }
    }
}
