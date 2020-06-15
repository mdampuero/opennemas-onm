<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\TagCacheHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TagSubscriber implements EventSubscriberInterface
{
    /**
     * The helper to remove caches.
     *
     * @var TagCacheHelper
     */
    protected $helper;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'tag.createItem' => [ [ 'onTagCreate', 5 ], ],
            'tag.deleteItem' => [ [ 'onTagDelete', 5 ], ],
            'tag.deleteList' => [ [ 'onTagDelete', 5 ], ],
            'tag.moveItem'   => [ [ 'onTagMove',   5 ], ],
            'tag.moveItems'  => [ [ 'onTagMove',   5 ], ],
            'tag.patchItem'  => [ [ 'onTagUpdate', 5 ], ],
            'tag.patchList'  => [ [ 'onTagUpdate', 5 ], ],
            'tag.updateItem' => [ [ 'onTagUpdate', 5 ], ]
        ];
    }

    /**
     * Initializes the TagSubscriber.
     *
     * @param TagCacheHelper $helper The helper to remove tag-related caches.
     */
    public function __construct(TagCacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Removes cache for tag list when a tag is created
     */
    public function onTagCreate()
    {
        $this->helper->deleteList();
    }

    /**
     * Removes caches for tag index and tag page when a tag is deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onTagDelete(Event $event)
    {
        $this->onTagUpdate($event);
    }

    /**
     * Removes caches tag list actions and varnish caches
     * for the instance when a tag or a list of categories are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onTagUpdate(Event $event)
    {
        $tags = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        foreach ($tags as $tag) {
            $this->helper->deleteItem($tag);
        }

        $this->helper->deleteList();
    }
}
