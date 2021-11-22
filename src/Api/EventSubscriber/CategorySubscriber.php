<?php

namespace Api\EventSubscriber;

use Api\Helper\Cache\CategoryCacheHelper;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CategorySubscriber implements EventSubscriberInterface
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
            'category.createItem' => [ [ 'onCategoryCreate', 5 ], ],
            'category.deleteItem' => [ [ 'onCategoryDelete', 5 ], ],
            'category.deleteList' => [ [ 'onCategoryDelete', 5 ], ],
            'category.moveItem'   => [ [ 'onCategoryMove',   5 ], ],
            'category.moveItems'  => [ [ 'onCategoryMove',   5 ], ],
            'category.patchItem'  => [ [ 'onCategoryUpdate', 5 ], ],
            'category.patchList'  => [ [ 'onCategoryUpdate', 5 ], ],
            'category.updateItem' => [ [ 'onCategoryUpdate', 5 ], ]
        ];
    }

    /**
     * Initializes the CategorySubscriber.
     *
     * @param CategoryCacheHelper $helper The helper to remove category-related
     *                                    caches.
     */
    public function __construct(CategoryCacheHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Removes cache for dynamic CSS when a category is created.
     */
    public function onCategoryCreate()
    {
        $this->helper->deleteDynamicCss();
    }

    /**
     * Removes caches for dynamic CSS and category list actions and varnish
     * caches for the instance when a category or a list of categories are
     * deleted.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryDelete(Event $event)
    {
        $this->onCategoryUpdate($event);
    }

    /**
     * Removes contents from cache, category list actions and varnish caches for
     * the instance after moving contents from a category to another.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryMove(Event $event)
    {
        if (!$event->hasArgument('contents')) {
            return;
        }

        $contents = $event->getArgument('contents');
        $cacheIds = [];

        foreach ($contents as $content) {
            $cacheIds[] = 'content-' . $content['id'];
            $cacheIds[] = $content['type'] . '-' . $content['id'];
        }

        $source = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        foreach ($source as $category) {
            $this->helper->deleteItem($category);
        }

        $this->helper
            ->deleteContents($cacheIds)
            ->deleteItem($event->getArgument('target'))
            ->deleteInstance();
    }

    /**
     * Removes caches for dynamic CSS, category list actions and varnish caches
     * for the instance when a category or a list of categories are updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryUpdate(Event $event)
    {
        $categories = is_array($event->getArgument('item'))
            ? $event->getArgument('item')
            : [ $event->getArgument('item') ];

        foreach ($categories as $category) {
            $this->helper->deleteItem($category);
        }

        $this->helper->deleteDynamicCss()->deleteInstance();
    }
}
