<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\ContentCacheHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AttachmentSubscriber implements EventSubscriberInterface
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
            'attachment.deleteItem' => [ [ 'onAttachmentDelete', 5 ] ],
            'attachment.patchItem'  => [ [ 'onAttachmentUpdate', 5 ] ],
            'attachment.updateItem' => [ [ 'onAttachmentUpdate', 5 ] ]
        ];
    }

    /**
     * Initializes the AttachmentSubscriber.
     *
     * @param ContentCacheHelper $helper The helper to remove caches for
     *                                   contents.
     */
    public function __construct(ContentCacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Removes attachments from varnish when an attachment is deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onAttachmentDelete(Event $event)
    {
        $this->onAttachmentUpdate($event);
    }

    /**
     * Removes attachments from varnish when an attachment or a list of
     * attachments are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onAttachmentUpdate(Event $event)
    {
        $contents = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        foreach ($contents as $content) {
            $this->helper->deleteItem($content);
        }
    }
}
