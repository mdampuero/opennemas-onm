<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\NewsstandCacheHelper;
use Common\Core\Component\Helper\VarnishHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewsstandSubscriber implements EventSubscriberInterface
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
            'content.deleteItem' => [ [ 'onNewsstandDelete', 5 ] ],
            'content.patchItem'  => [ [ 'onNewsstandPatch',  5 ] ],
            'content.updateItem' => [ [ 'onNewsstandUpdate', 5 ] ]
        ];
    }

    /**
     * Initializes the NewsstandSubscriber.
     *
     * @param ContentCacheHelper $helper The helper to remove caches for
     *                                   contents.
     */
    public function __construct(NewsstandCacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Removes  smarty and varnish caches when a newsstand or a list of
     * newsstands are deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onNewsstandDelete(Event $event)
    {
        $this->onNewsstandUpdate($event);
    }

    /**
     * Removes  smarty and varnish caches when a newsstand or a list of
     * newsstands are patched.
     *
     * @param Event $event The dispatched event.
     */
    public function onNewsstandPatch(Event $event)
    {
        $this->onNewsstandUpdate($event);
    }

    /**
     * Removes  smarty and varnish caches when a newsstand or a list of
     * newsstands are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onNewsstandUpdate(Event $event)
    {
        $items = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        foreach ($items as $item) {
            $this->helper->deleteItem($item)->deleteFile($item);
        }

        $this->helper->deleteList();
    }
}
