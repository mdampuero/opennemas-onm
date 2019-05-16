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

use Common\Orm\Entity\Instance;
use Common\Core\Component\Helper\TemplateCacheHelper;
use Common\Core\Component\Helper\VarnishHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class CategorySubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'category.createItem' => [ [ 'onCategoryCreate', 5 ], ],
            'category.deleteItem' => [ [ 'onCategoryDelete', 5 ], ],
            'category.deleteList' => [ [ 'onCategoryDelete', 5 ], ],
            'category.patchItem'  => [ [ 'onCategoryUpdate', 5 ], ],
            'category.patchList'  => [ [ 'onCategoryUpdate', 5 ], ],
            'category.updateItem' => [ [ 'onCategoryUpdate', 5 ], ]
        ];
    }

    /**
     * Initializes the CategorySubscriber.
     *
     * @param TemplateCacheManager $tcm The TemplateCacheManager service.
     */
    public function __construct(Instance $instance, TemplateCacheHelper $th, VarnishHelper $vh)
    {
        $this->instance = $instance;
        $this->template = $th;
        $this->varnish  = $vh;
    }

    /**
     * Removes cache for dynamic CSS when a category is created.
     */
    public function onCategoryCreate()
    {
        $this->template->deleteDynamicCss();
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
     * Removes caches for dynamic CSS and category list actions and varnish
     * caches for the instance when a category or a list of categories are
     * updated.
     *
     * @param Event $event The dispatched event.
     */
    public function onCategoryUpdate(Event $event)
    {
        $categories = $event->hasArgument('item')
            ? [ $event->getArgument('item') ]
            : $event->getArgument('items');

        $this->template->deleteDynamicCss();
        $this->template->deleteCategories($categories);
        $this->varnish->deleteInstance($this->instance);
    }
}
